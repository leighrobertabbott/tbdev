<?php

namespace App\Jobs;

use App\Core\Database;
use App\Core\Bencode;

class ProcessTorrentJob
{
    public function handle(array $data): void
    {
        $torrentId = $data['torrent_id'] ?? 0;
        $filePath = $data['file_path'] ?? '';

        if (!$torrentId || empty($filePath) || !file_exists($filePath)) {
            throw new \Exception('Invalid torrent file');
        }

        // Parse torrent file
        $torrentData = file_get_contents($filePath);
        $decoded = Bencode::decode($torrentData);

        if (!isset($decoded['info'])) {
            throw new \Exception('Invalid torrent file format');
        }

        $info = $decoded['info'];
        $infoHash = sha1(Bencode::encode($info));
        $name = $info['name'] ?? 'Unknown';
        $size = 0;
        $files = [];

        // Calculate size and extract files
        if (isset($info['files'])) {
            // Multi-file torrent
            foreach ($info['files'] as $file) {
                $fileSize = $file['length'] ?? 0;
                $size += $fileSize;
                $filePath = implode('/', $file['path'] ?? []);
                $files[] = [
                    'filename' => $filePath,
                    'size' => $fileSize,
                ];
            }
        } else {
            // Single-file torrent
            $size = $info['length'] ?? 0;
            $files[] = [
                'filename' => $name,
                'size' => $size,
            ];
        }

        // Update torrent with parsed data
        Database::execute(
            "UPDATE torrents SET 
                info_hash = :info_hash,
                size = :size,
                numfiles = :numfiles,
                search_text = :search_text
             WHERE id = :id",
            [
                'id' => $torrentId,
                'info_hash' => $infoHash,
                'size' => $size,
                'numfiles' => count($files),
                'search_text' => $name .
                    ' ' .
                    implode(' ', array_column($files, 'filename')),
            ]
        );

        // Insert files
        Database::execute("DELETE FROM files WHERE torrent = :id", ['id' => $torrentId]);
        foreach ($files as $file) {
            Database::execute(
                "INSERT INTO files (torrent, filename, size) VALUES (:torrent, :filename, :size)",
                [
                    'torrent' => $torrentId,
                    'filename' => $file['filename'],
                    'size' => $file['size'],
                ]
            );
        }
    }
}

