<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Models\User;
use App\Core\Database;
use Symfony\Component\HttpFoundation\Request;

class FriendsController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $userId = (int) $request->query->get('id', $user['id']);

        if ($userId != $user['id'] && ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $friends = Database::fetchAll(
            "SELECT f.friendid as id, u.username, u.class, u.avatar, u.title, u.donor, u.warned, u.enabled, u.last_access 
             FROM friends f 
             LEFT JOIN users u ON f.friendid = u.id 
             WHERE f.userid = :user_id 
             ORDER BY u.username",
            ['user_id' => $userId]
        );

        $blocks = Database::fetchAll(
            "SELECT b.blockid as id, u.username, u.donor, u.warned, u.enabled, u.last_access 
             FROM blocks b 
             LEFT JOIN users u ON b.blockid = u.id 
             WHERE b.userid = :user_id 
             ORDER BY u.username",
            ['user_id' => $userId]
        );

        return ResponseHelper::view('friends/index', [
            'user' => $user,
            'friends' => $friends,
            'blocks' => $blocks,
            'pageTitle' => 'Friends & Blocks',
        ]);
    }

    public function add(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $targetId = (int) $request->query->get('targetid', 0);
        $type = $request->query->get('type', 'friend');

        if ($targetId <= 0 || !in_array($type, ['friend', 'block'])) {
            return ResponseHelper::redirect('/friends');
        }

        $table = $type === 'friend' ? 'friends' : 'blocks';
        $field = $type === 'friend' ? 'friendid' : 'blockid';

        // Check if already exists
        $existing = Database::fetchOne(
            "SELECT id FROM {$table} WHERE userid = :user_id AND {$field} = :target_id",
            ['user_id' => $user['id'], 'target_id' => $targetId]
        );

        if (!$existing) {
            Database::execute(
                "INSERT INTO {$table} (userid, {$field}) VALUES (:user_id, :target_id)",
                ['user_id' => $user['id'], 'target_id' => $targetId]
            );
        }

        return ResponseHelper::redirect('/friends');
    }

    public function delete(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $targetId = (int) $request->query->get('targetid', 0);
        $type = $request->query->get('type', 'friend');
        $sure = $request->query->get('sure', false);

        if (!$sure) {
            return ResponseHelper::view('friends/confirm-delete', [
                'user' => $user,
                'targetId' => $targetId,
                'type' => $type,
                'pageTitle' => 'Confirm Delete',
            ]);
        }

        $table = $type === 'friend' ? 'friends' : 'blocks';
        $field = $type === 'friend' ? 'friendid' : 'blockid';

        Database::execute(
            "DELETE FROM {$table} WHERE userid = :user_id AND {$field} = :target_id",
            ['user_id' => $user['id'], 'target_id' => $targetId]
        );

        return ResponseHelper::redirect('/friends');
    }
}

