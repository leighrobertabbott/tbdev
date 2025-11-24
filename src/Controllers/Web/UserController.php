<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Core\Database;
use App\Models\User;
use App\Models\Torrent;
use App\Models\Peer;
use App\Services\ReputationService;
use App\Services\AchievementService;
use App\Services\ActivityService;
use Symfony\Component\HttpFoundation\Request;

class UserController
{
    public function show(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $profileUser = User::findById($id);
        if (!$profileUser) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'User Not Found'], 404);
        }

        // Get user stats
        $uploadedTorrents = Torrent::count(['owner' => $id]);
        $peers = Peer::findByUser($id);
        $reputation = ReputationService::getUserReputation($id);
        $reputationLevel = ReputationService::getReputationLevel($reputation);

        // Calculate ratio
        $ratio = \App\Core\FormatHelper::ratio(
            $profileUser['uploaded'] ?? 0,
            $profileUser['downloaded'] ?? 0
        );

        // Get achievements
        $achievements = AchievementService::getUserAchievements($id);
        $earnedAchievements = array_filter($achievements, fn($a) => $a['earned']);
        $achievementPoints = $profileUser['achievement_points'] ?? 0;

        // Log activity
        ActivityService::log($user['id'], 'profile_view', 'user', $id);

        return ResponseHelper::view('user/profile', [
            'user' => $user,
            'profileUser' => $profileUser,
            'uploadedTorrents' => $uploadedTorrents,
            'peers' => $peers,
            'reputation' => $reputation,
            'reputationLevel' => $reputationLevel,
            'ratio' => $ratio,
            'achievements' => $achievements,
            'earnedAchievements' => $earnedAchievements,
            'achievementPoints' => $achievementPoints,
            'pageTitle' => htmlspecialchars($profileUser['username']) .
                ' - Profile',
        ]);
    }

    public function profile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        return $this->show($request, $user['id']);
    }

    public function edit(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        return ResponseHelper::view('user/edit', [
            'user' => $user,
            'pageTitle' => 'Edit Profile',
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        if ($request->getMethod() !== 'POST') {
            return ResponseHelper::redirect('/profile/edit');
        }

        $data = [];
        $errors = [];
        
        // Email
        if ($request->request->has('email')) {
            $email = Security::sanitizeInput($request->request->get('email'));
            if (Security::validateEmail($email)) {
                // Check if email is already in use by another user
                $existing = Database::fetchOne(
                    "SELECT id FROM users WHERE email = :email AND id != :id",
                    ['email' => $email, 'id' => $user['id']]
                );
                if ($existing) {
                    $errors[] = 'This email address is already in use.';
                } else {
                    $data['email'] = $email;
                }
            } else {
                $errors[] = 'Invalid email address.';
            }
        }

        // Avatar
        if ($request->request->has('avatar')) {
            $avatar = Security::sanitizeInput($request->request->get('avatar'));
            if (!empty($avatar) && !filter_var($avatar, FILTER_VALIDATE_URL)) {
                $errors[] = 'Invalid avatar URL.';
            } else {
                $data['avatar'] = $avatar;
            }
        }

        // Title
        if ($request->request->has('title')) {
            $data['title'] = Security::sanitizeInput($request
                ->request
                ->get('title', ''));
        }

        // Info
        if ($request->request->has('info')) {
            $data['info'] = Security::sanitizeInput($request
                ->request
                ->get('info', ''));
        }

        // Stylesheet
        if ($request->request->has('stylesheet')) {
            $data['stylesheet'] = (int) $request->request->get('stylesheet', 1);
        }

        // Timezone (stored as time_offset in database - format: "+05" or "-08")
        if ($request->request->has('timezone')) {
            $timezone = Security::sanitizeInput($request
                ->request
                ->get('timezone', '0'));
            // Store as offset string (e.g., "+05", "-08", "0")
            $data['time_offset'] = $timezone;
        }

        // Privacy (enum: 'strong', 'normal', 'low')
        if ($request->request->has('privacy')) {
            $privacy = Security::sanitizeInput($request
                ->request
                ->get('privacy', 'normal'));
            if (in_array($privacy, ['strong', 'normal', 'low'])) {
                $data['privacy'] = $privacy;
            }
        }

        if (!empty($errors)) {
            return ResponseHelper::view('user/edit', [
                'user' => $user,
                'error' => implode(' ', $errors),
                'pageTitle' => 'Edit Profile',
            ]);
        }

        if (!empty($data)) {
            User::update($user['id'], $data);
        }

        return ResponseHelper::redirect('/user/' . $user['id'] . '?updated=1');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        if ($request->getMethod() !== 'POST') {
            return ResponseHelper::redirect('/profile/edit');
        }

        $currentPassword = $request->request->get('current_password', '');
        $newPassword = $request->request->get('new_password', '');
        $confirmPassword = $request->request->get('confirm_password', '');

        // Verify current password
        if (!password_verify($currentPassword, $user['passhash'])) {
            return ResponseHelper::view('user/edit', [
                'user' => $user,
                'error' => 'Current password is incorrect.',
                'pageTitle' => 'Edit Profile',
            ]);
        }

        // Validate new password
        if (strlen($newPassword) < 8) {
            return ResponseHelper::view('user/edit', [
                'user' => $user,
                'error' => 'New password must be at least 8 characters long.',
                'pageTitle' => 'Edit Profile',
            ]);
        }

        if ($newPassword !== $confirmPassword) {
            return ResponseHelper::view('user/edit', [
                'user' => $user,
                'error' => 'New passwords do not match.',
                'pageTitle' => 'Edit Profile',
            ]);
        }

        // Update password
        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
        User::update($user['id'], ['passhash' => $newHash]);

        return ResponseHelper::redirect('/user/' .
            $user['id'] .
            '?password_changed=1');
    }

    public function myTorrents(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $torrents = Torrent::findAll(['owner' => $user['id']], $perPage, $offset);
        $total = Torrent::count(['owner' => $user['id']]);
        $totalPages = ceil($total / $perPage);

        return ResponseHelper::view('user/mytorrents', [
            'user' => $user,
            'torrents' => $torrents,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'pageTitle' => 'My Torrents',
        ]);
    }
}

