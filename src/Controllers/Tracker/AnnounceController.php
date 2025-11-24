<?php

namespace App\Controllers\Tracker;

use App\Core\Database;
use App\Core\Config;
use App\Core\Security;
use App\Core\Bencode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AnnounceController
{
    public function announce(Request $request): Response
    {
        // This is the BitTorrent announce endpoint
        // Must return bencoded response
        
        // BitTorrent clients send binary data in URL parameters
        // We need to get the raw query string and parse it manually
        $queryString = $request->server->get('QUERY_STRING', '');
        parse_str($queryString, $params);
        
        // Get parameters - handle binary data correctly
        $info_hash_raw = isset($params['info_hash']) ? $params['info_hash'] : '';
        $peer_id = isset($params['peer_id']) ? $params['peer_id'] : '';
        $port = isset($params['port']) ? (int) $params['port'] : 0;
        $uploaded = isset($params['uploaded']) ? (int) $params['uploaded'] : 0;
        $downloaded = isset($params['downloaded']) ? (int) $params['downloaded'] : 0;
        $left = isset($params['left']) ? (int) $params['left'] : 0;
        $event = isset($params['event']) ? $params['event'] : '';
        $compact = isset($params['compact']) ? (int) $params['compact'] : 1; // Default to compact format
        
        // Handle IP parameter if provided
        $ip = isset($params['ip']) ? $params['ip'] : Security::getClientIp();
        // Validate IP
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = Security::getClientIp();
        }
        
        // Validate required parameters
        if (empty($info_hash_raw) || strlen($info_hash_raw) != 20) {
            return $this->errorResponse('Invalid info_hash');
        }
        
        if (empty($peer_id) || strlen($peer_id) != 20) {
            return $this->errorResponse('Invalid peer_id');
        }
        
        if ($port <= 0 || $port > 65535) {
            return $this->errorResponse('Invalid port');
        }
        
        // Check if site is online
        if (!Config::get('site.online')) {
            return $this->errorResponse('Tracker is offline');
        }
        
        // Convert info_hash from raw binary to hex string for database lookup
        $info_hash_hex = bin2hex($info_hash_raw);
        
        // Get user from passkey (if using passkey system) or IP
        // For now, we'll use a simplified approach
        $userId = 0; // Anonymous peer
        
        // Get torrent
        $torrent = Database::fetchOne(
            "SELECT id, info_hash FROM torrents WHERE info_hash = :hash",
            ['hash' => $info_hash_hex]
        );
        
        if (!$torrent) {
            return $this->errorResponse('Torrent not registered with this tracker');
        }
        
        $torrentId = $torrent['id'];
        $seeder = ($left == 0) ? 'yes' : 'no';
        
        // Update or insert peer
        $existingPeer = Database::fetchOne(
            "SELECT id FROM peers WHERE torrent = :torrent AND peer_id = :peer_id",
            ['torrent' => $torrentId, 'peer_id' => $peer_id]
        );
        
        if ($existingPeer) {
            // Update existing peer
            Database::execute(
                "UPDATE peers SET ip = :ip, port = :port, uploaded = :uploaded, downloaded = :downloaded, 
                 to_go = :to_go, seeder = :seeder, last_action = :time 
                 WHERE id = :id",
                [
                    'id' => $existingPeer['id'],
                    'ip' => $ip, // Store as string, not integer
                    'port' => $port,
                    'uploaded' => $uploaded,
                    'downloaded' => $downloaded,
                    'to_go' => $left,
                    'seeder' => $seeder,
                    'time' => time(),
                ]
            );
        } else {
            // Insert new peer
            Database::execute(
                "INSERT INTO peers (torrent, userid, ip, port, uploaded, downloaded, to_go, seeder, peer_id, started, last_action, agent) 
                 VALUES (:torrent, :userid, :ip, :port, :uploaded, :downloaded, :to_go, :seeder, :peer_id, :started, :last_action, :agent)",
                [
                    'torrent' => $torrentId,
                    'userid' => $userId,
                    'ip' => $ip, // Store as string
                    'port' => $port,
                    'uploaded' => $uploaded,
                    'downloaded' => $downloaded,
                    'to_go' => $left,
                    'seeder' => $seeder,
                    'peer_id' => $peer_id,
                    'started' => time(),
                    'last_action' => time(),
                    'agent' => $request
                        ->server
                        ->get('HTTP_USER_AGENT', 'Unknown'),
                ]
            );
        }
        
        // Handle events
        if ($event === 'stopped' || $event === 'completed') {
            Database::execute(
                "DELETE FROM peers WHERE torrent = :torrent AND peer_id = :peer_id",
                ['torrent' => $torrentId, 'peer_id' => $peer_id]
            );
            
            if ($event === 'completed') {
                Database::execute(
                    "UPDATE torrents SET times_completed = times_completed + 1 WHERE id = :id",
                    ['id' => $torrentId]
                );
            }
        }
        
        // Get peer list
        $peers = Database::fetchAll(
            "SELECT ip, port FROM peers WHERE torrent = :torrent AND peer_id != :peer_id LIMIT 50",
            ['torrent' => $torrentId, 'peer_id' => $peer_id]
        );
        
        // Clean up old peers (older than 30 minutes)
        Database::execute(
            "DELETE FROM peers WHERE torrent = :torrent AND last_action < :timeout",
            ['torrent' => $torrentId, 'timeout' => time() - 1800]
        );
        
        // Get tracker stats
        $stats = Database::fetchOne(
            "SELECT 
                COUNT(*) as total_peers,
                SUM(CASE WHEN seeder = 'yes' THEN 1 ELSE 0 END) as seeders,
                SUM(CASE WHEN seeder = 'no' THEN 1 ELSE 0 END) as leechers
             FROM peers WHERE torrent = :torrent",
            ['torrent' => $torrentId]
        );
        
        // Format peers for response (BitTorrent spec: compact format is binary string)
        if ($compact == 1) {
            // Compact format: 6 bytes per peer (4 bytes IP + 2 bytes port, big-endian)
            $peerList = '';
            foreach ($peers as $peer) {
                $peerIp = $peer['ip']; // Already stored as string
                $peerPort = (int) $peer['port'];
                
                // Convert IP string to binary (handles both IPv4 and IPv6)
                $ipBinary = inet_pton($peerIp);
                if ($ipBinary === false) {
                    continue; // Skip invalid IPs
                }
                
                // For IPv4, use 4 bytes; for IPv6, use 16 bytes (but BitTorrent spec typically uses IPv4)
                if (strlen($ipBinary) == 4) {
                    // IPv4: 4 bytes IP + 2 bytes port (big-endian)
                    $peerList .= $ipBinary . pack('n', $peerPort);
                }
                // Note: IPv6 support would require different handling
            }
        } else {
            // Dictionary format (legacy)
            $peerList = [];
            foreach ($peers as $peer) {
                $peerList[] = [
                    'ip' => $peer['ip'], // Already a string
                    'port' => (int) $peer['port'],
                ];
            }
        }
        
        // Build response
        $response = [
            'interval' => Config::get('tracker.announce_interval', 1800),
            'complete' => (int) ($stats['seeders'] ?? 0),
            'incomplete' => (int) ($stats['leechers'] ?? 0),
            'peers' => $peerList,
        ];
        
        // Update torrent stats
        Database::execute(
            "UPDATE torrents SET seeders = :seeders, leechers = :leechers WHERE id = :id",
            [
                'id' => $torrentId,
                'seeders' => (int) ($stats['seeders'] ?? 0),
                'leechers' => (int) ($stats['leechers'] ?? 0),
            ]
        );
        
        // Update global stats
        $this->updateGlobalStats();
        
        return $this->bencodedResponse($response);
    }
    
    private function errorResponse(string $message): Response
    {
        $response = ['failure reason' => $message];
        return $this->bencodedResponse($response);
    }
    
    private function bencodedResponse(array $data): Response
    {
        $bencoded = Bencode::encode($data);
        
        return new Response($bencoded, 200, [
            'Content-Type' => 'text/plain',
            'Pragma' => 'no-cache',
        ]);
    }
    
    private function updateGlobalStats(): void
    {
        $stats = Database::fetchOne(
            "SELECT 
                SUM(CASE WHEN seeder = 'yes' THEN 1 ELSE 0 END) as seeders,
                SUM(CASE WHEN seeder = 'no' THEN 1 ELSE 0 END) as leechers
             FROM peers"
        );
        
        Database::execute(
            "INSERT INTO avps (arg, value_u) VALUES ('seeders', :seeders) 
             ON DUPLICATE KEY UPDATE value_u = :seeders",
            ['seeders' => (int) ($stats['seeders'] ?? 0)]
        );
        
        Database::execute(
            "INSERT INTO avps (arg, value_u) VALUES ('leechers', :leechers) 
             ON DUPLICATE KEY UPDATE value_u = :leechers",
            ['leechers' => (int) ($stats['leechers'] ?? 0)]
        );
    }
}


