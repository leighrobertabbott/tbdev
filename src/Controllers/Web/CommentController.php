<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Models\Comment;
use App\Services\NotificationService;
use Symfony\Component\HttpFoundation\Request;

class CommentController
{
    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $torrentId = (int) $request->request->get('torrent_id', 0);
        $text = Security::sanitizeInput($request->request->get('text', ''));

        if ($torrentId <= 0 || empty($text)) {
            return ResponseHelper::redirect("/torrent/{$torrentId}");
        }

        $commentId = Comment::create([
            'user' => $user['id'],
            'torrent' => $torrentId,
            'text' => $text,
        ]);

        // Notify torrent owner
        $torrent = \App\Models\Torrent::findById($torrentId);
        if ($torrent && $torrent['owner'] != $user['id']) {
            NotificationService::notifyTorrentComment($torrent['owner'], $torrentId, $user['id']);
        }

        return ResponseHelper::redirect("/torrent/{$torrentId}#comment-{$commentId}");
    }

    public function edit(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $comment = Comment::findById($id);
        if (!$comment || ($comment['user'] != $user['id'] && ($user['class'] ?? 0) < 4)) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        if ($request->getMethod() === 'POST') {
            $text = Security::sanitizeInput($request->request->get('text', ''));
            Comment::update($id, $text, $user['id']);
            return ResponseHelper::redirect("/torrent/{$comment['torrent']}");
        }

        return ResponseHelper::view('comments/edit', [
            'user' => $user,
            'comment' => $comment,
            'pageTitle' => 'Edit Comment',
        ]);
    }

    public function delete(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $comment = Comment::findById($id);
        if (!$comment || ($comment['user'] != $user['id'] && ($user['class'] ?? 0) < 4)) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $torrentId = $comment['torrent'];
        Comment::delete($id);

        return ResponseHelper::redirect("/torrent/{$torrentId}");
    }
}

