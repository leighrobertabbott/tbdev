<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Config;
use App\Core\Security;
use App\Core\Bencode;
use App\Models\Torrent;
use App\Models\Category;
use App\Models\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadController
{
    public function show(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login?returnto=/upload');
        }

        $categories = Category::all();

        $appUrl = Config::get('app.url', 'http://localhost:8000');
        $trackerUrl = rtrim($appUrl, '/') . '/announce.php';
        
        return ResponseHelper::view('upload/index', [
            'user' => $user,
            'categories' => $categories,
            'pageTitle' => 'Upload Torrent',
            'maxSize' => Config::get('uploads.max_torrent_size'),
            'trackerUrl' => $trackerUrl,
        ]);
    }

    public function upload(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login?returnto=/upload');
        }

        /** @var UploadedFile $torrentFile */
        $torrentFile = $request->files->get('torrent');
        
        if (!$torrentFile || !$torrentFile->isValid()) {
            return ResponseHelper::view('upload/index', [
                'user' => $user,
                'categories' => Category::all(),
                'error' => 'Invalid torrent file.',
                'pageTitle' => 'Upload Torrent',
            ]);
        }

        // Validate file size
        $maxSize = Config::get('uploads.max_torrent_size', 1048576);
        if ($torrentFile->getSize() > $maxSize) {
            return ResponseHelper::view('upload/index', [
                'user' => $user,
                'categories' => Category::all(),
                'error' => 'File too large. Maximum size: ' . \App\Core\FormatHelper::bytes($maxSize),
                'pageTitle' => 'Upload Torrent',
            ]);
        }

        // Parse torrent file
        $torrentData = $this->parseTorrentFile($torrentFile->getPathname());
        
        if (!$torrentData) {
            return ResponseHelper::view('upload/index', [
                'user' => $user,
                'categories' => Category::all(),
                'error' => 'Invalid torrent file format.',
                'pageTitle' => 'Upload Torrent',
            ]);
        }

        // Get form data
        $name = Security::sanitizeInput($request->request->get('name', ''));
        $category = (int) $request->request->get('category', 0);
        $description = Security::sanitizeInput($request->request->get('description', ''));

        if (empty($name) || $category <= 0) {
            return ResponseHelper::view('upload/index', [
                'user' => $user,
                'categories' => Category::all(),
                'error' => 'Name and category are required.',
                'pageTitle' => 'Upload Torrent',
            ]);
        }

        // Calculate total size
        $totalSize = 0;
        if (isset($torrentData['info']['files'])) {
            foreach ($torrentData['info']['files'] as $file) {
                $totalSize += $file['length'];
            }
        } else {
            $totalSize = $torrentData['info']['length'] ?? 0;
        }

        // Generate info hash (must be SHA1 of bencoded info dictionary)
        // Store as hex string (40 characters) for database
        $infoHash = sha1(Bencode::encode($torrentData['info']));

        // Check if torrent already exists
        $existing = Torrent::findById(0); // Would check by hash
        if ($existing) {
            return ResponseHelper::view('upload/index', [
                'user' => $user,
                'categories' => Category::all(),
                'error' => 'This torrent already exists.',
                'pageTitle' => 'Upload Torrent',
            ]);
        }

        // Save torrent file
        $torrentDir = Config::get('uploads.torrent_dir', './torrents');
        
        // Convert relative path to absolute if needed
        if (!preg_match('/^[\/\\\\]|[A-Za-z]:[\/\\\\]/', $torrentDir)) {
            // Relative path - make it absolute from project root
            $projectRoot = dirname(__DIR__, 2); // Go up from src/Controllers/Web to project root
            $torrentDir = $projectRoot . '/' . ltrim($torrentDir, './');
        }
        
        // Normalize path separators for Windows
        $torrentDir = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $torrentDir);
        
        // Ensure directory exists and is writable
        if (!is_dir($torrentDir)) {
            if (!mkdir($torrentDir, 0755, true)) {
                return ResponseHelper::view('upload/index', [
                    'user' => $user,
                    'categories' => Category::all(),
                    'error' => 'Failed to create torrents directory. Please check permissions.',
                    'pageTitle' => 'Upload Torrent',
                ]);
            }
        }
        
        // Test if directory is writable
        $testFile = $torrentDir . DIRECTORY_SEPARATOR . '.writable_test_' . uniqid();
        if (@file_put_contents($testFile, 'test') === false) {
            return ResponseHelper::view('upload/index', [
                'user' => $user,
                'categories' => Category::all(),
                'error' => 'Torrents directory is not writable. Please check permissions on: ' . htmlspecialchars($torrentDir),
                'pageTitle' => 'Upload Torrent',
            ]);
        }
        @unlink($testFile);
        
        $filename = $infoHash . '.torrent';
        
        // Ensure filename is safe
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        try {
            $torrentFile->move($torrentDir, $filename);
        } catch (\Exception $e) {
            return ResponseHelper::view('upload/index', [
                'user' => $user,
                'categories' => Category::all(),
                'error' => 'Failed to save torrent file: ' . htmlspecialchars($e->getMessage()),
                'pageTitle' => 'Upload Torrent',
            ]);
        }

        // Create torrent record
        $torrentId = Torrent::create([
            'name' => $name,
            'filename' => $filename,
            'owner' => $user['id'],
            'category' => $category,
            'size' => $totalSize,
            'info_hash' => $infoHash,
            'descr' => $description,
        ]);

        // Extract and save file list
        if (isset($torrentData['info']['files'])) {
            foreach ($torrentData['info']['files'] as $file) {
                $path = isset($file['path']) ? implode('/', $file['path']) : $file['name'] ?? 'unknown';
                File::create([
                    'torrent' => $torrentId,
                    'filename' => $path,
                    'size' => $file['length'],
                ]);
            }
        }

        return ResponseHelper::redirect("/torrent/{$torrentId}?uploaded=1");
    }

    private function parseTorrentFile(string $filepath): ?array
    {
        $content = file_get_contents($filepath);
        if (!$content) {
            return null;
        }

        try {
            $data = Bencode::decode($content);
            if (!isset($data['info'])) {
                error_log('Invalid torrent file: missing info dictionary');
                return null;
            }
            return $data;
        } catch (\Exception $e) {
            error_log('Error parsing torrent file: ' . $e->getMessage());
            return null;
        }
    }
}

