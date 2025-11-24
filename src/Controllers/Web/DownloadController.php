<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\Config;
use App\Core\ResponseHelper;
use App\Models\Torrent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController
{
    public function download(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login?returnto=' . urlencode($request->getRequestUri()));
        }

        $torrent = Torrent::findById($id);
        if (!$torrent) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Torrent Not Found'], 404);
        }

        $torrentDir = Config::get('uploads.torrent_dir', './torrents');
        
        // Convert relative path to absolute if needed
        if (!preg_match('/^[\/\\\\]|[A-Za-z]:[\/\\\\]/', $torrentDir)) {
            // Relative path - make it absolute from project root
            $projectRoot = dirname(__DIR__, 2); // Go up from src/Controllers/Web to project root
            $torrentDir = $projectRoot . '/' . ltrim($torrentDir, './');
        }
        
        // Normalize path separators for Windows
        $torrentDir = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $torrentDir);
        $filepath = $torrentDir . DIRECTORY_SEPARATOR . $torrent['filename'];

        if (!file_exists($filepath)) {
            error_log("Torrent file not found: {$filepath} (torrent ID: {$id}, filename: {$torrent['filename']})");
            return ResponseHelper::view('errors/404', [
                'pageTitle' => 'Torrent File Not Found',
                'message' => 'The torrent file could not be found on the server.',
            ], 404);
        }

        // Increment download count
        Torrent::update($id, ['hits' => ($torrent['hits'] ?? 0) + 1]);

        $response = new BinaryFileResponse($filepath);
        $response->setContentDisposition(
            \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $torrent['name'] . '.torrent'
        );

        return $response;
    }
}

