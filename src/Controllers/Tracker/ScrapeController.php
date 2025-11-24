<?php

namespace App\Controllers\Tracker;

use App\Core\Database;
use App\Core\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ScrapeController
{
    public function scrape(Request $request): Response
    {
        // BitTorrent scrape endpoint
        ob_start();
        
        $infoHashes = $request->query->get('info_hash', []);
        if (!is_array($infoHashes)) {
            $infoHashes = [$infoHashes];
        }

        $files = [];
        
        foreach ($infoHashes as $infoHash) {
            if (strlen($infoHash) != 20) {
                continue;
            }
            
            $infoHashHex = bin2hex($infoHash);
            
            $torrent = Database::fetchOne(
                "SELECT id, times_completed FROM torrents WHERE info_hash = :hash",
                ['hash' => $infoHashHex]
            );
            
            if ($torrent) {
                $stats = Database::fetchOne(
                    "SELECT 
                        SUM(CASE WHEN seeder = 'yes' THEN 1 ELSE 0 END) as seeders,
                        SUM(CASE WHEN seeder = 'no' THEN 1 ELSE 0 END) as leechers
                     FROM peers WHERE torrent = :torrent",
                    ['torrent' => $torrent['id']]
                );
                
                // Use binary info_hash as key
                $files[$infoHash] = [
                    'complete' => (int) ($stats['seeders'] ?? 0),
                    'downloaded' => (int) ($torrent['times_completed'] ?? 0),
                    'incomplete' => (int) ($stats['leechers'] ?? 0),
                ];
            }
        }
        
        $response = [
            'files' => $files,
            'flags' => [
                'min_request_interval' => 1800,
            ],
        ];
        
        return $this->bencodedResponse($response);
    }
    
    private function bencodedResponse(array $data): Response
    {
        ob_clean();
        header('Content-Type: text/plain');
        header('Pragma: no-cache');
        
        $bencoded = \App\Core\Bencode::encode($data);
        
        return new Response($bencoded, 200, [
            'Content-Type' => 'text/plain',
            'Pragma' => 'no-cache',
        ]);
    }
}

