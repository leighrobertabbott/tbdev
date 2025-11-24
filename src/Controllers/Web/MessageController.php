<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Models\Message;
use App\Models\User;
use App\Services\NotificationService;
use Symfony\Component\HttpFoundation\Request;

class MessageController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $location = $request->query->get('location', 'in');
        $messages = Message::findForUser($user['id'], $location);
        $unreadCount = Message::getUnreadCount($user['id']);

        return ResponseHelper::view('messages/index', [
            'user' => $user,
            'messages' => $messages,
            'location' => $location,
            'unreadCount' => $unreadCount,
            'pageTitle' => 'Messages',
        ]);
    }

    public function show(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $message = Message::findById($id);
        
        if (!$message || ($message['sender'] != $user['id'] && $message['receiver'] != $user['id'])) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Not Found'], 404);
        }

        // Mark as read if receiver
        if ($message['receiver'] == $user['id'] && $message['unread'] === 'yes') {
            Message::markAsRead($id, $user['id']);
        }

        return ResponseHelper::view('messages/show', [
            'user' => $user,
            'message' => $message,
            'pageTitle' => htmlspecialchars($message['subject']),
        ]);
    }

    public function compose(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $to = $request->query->get('to');
        $toUser = $to ? User::findByUsername($to) : null;

        return ResponseHelper::view('messages/compose', [
            'user' => $user,
            'toUser' => $toUser,
            'pageTitle' => 'Compose Message',
        ]);
    }

    public function send(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $to = Security::sanitizeInput($request->request->get('to', ''));
        $subject = Security::sanitizeInput($request->request->get('subject', ''));
        $msg = Security::sanitizeInput($request->request->get('msg', ''));

        if (empty($to) || empty($subject) || empty($msg)) {
            return ResponseHelper::view('messages/compose', [
                'user' => $user,
                'error' => 'All fields are required.',
                'pageTitle' => 'Compose Message',
            ]);
        }

        $toUser = User::findByUsername($to);
        if (!$toUser) {
            return ResponseHelper::view('messages/compose', [
                'user' => $user,
                'error' => 'User not found.',
                'pageTitle' => 'Compose Message',
            ]);
        }

        $messageId = Message::create([
            'sender' => $user['id'],
            'receiver' => $toUser['id'],
            'subject' => $subject,
            'msg' => $msg,
            'location' => 3, // Both inbox and sent
        ]);

        // Send notification
        NotificationService::notifyNewMessage($toUser['id'], $user['id']);

        return ResponseHelper::redirect('/messages');
    }
}

