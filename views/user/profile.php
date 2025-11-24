<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <!-- Profile Header -->
    <div class="card mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
            <!-- Avatar -->
            <div class="flex-shrink-0">
                <?php if (!empty($profileUser['avatar'])): ?>
                    <img src="<?= htmlspecialchars($profileUser['avatar']) ?>" 
                         alt="<?= htmlspecialchars($profileUser['username']) ?>'s avatar"
                         class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 shadow-lg"
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27128%27 height=%27128%27%3E%3Crect fill=%27%23ddd%27 width=%27128%27 height=%27128%27/%3E%3Ctext fill=%27%23999%27 font-family=%27sans-serif%27 font-size=%2750%27 dy=%2737%27 x=%2750%25%27 text-anchor=%27middle%27%3E<?= strtoupper(substr($profileUser['username'], 0, 1)) ?>%3C/text%3E%3C/svg%3E'">
                <?php else: ?>
                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white text-5xl font-bold shadow-lg border-4 border-gray-200">
                        <?= strtoupper(substr($profileUser['username'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- User Info -->
            <div class="flex-1">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold font-display mb-2">
                            <?= htmlspecialchars($profileUser['username']) ?>
                            <?php if (!empty($profileUser['title'])): ?>
                                <span class="text-lg font-normal text-gray-600 italic"><?= htmlspecialchars($profileUser['title']) ?></span>
                            <?php endif; ?>
                        </h1>
                        <p class="text-gray-600 font-serif italic">
                            Member since <?= \App\Core\FormatHelper::date($profileUser['added']) ?>
                        </p>
                        <?php
                        $classes = [0 => 'User', 1 => 'Power User', 2 => 'VIP', 3 => 'Uploader', 4 => 'Moderator', 5 => 'Administrator', 6 => 'Sysop'];
                        $userClass = $classes[$profileUser['class'] ?? 0] ?? 'User';
                        ?>
                        <span class="inline-block mt-2 px-3 py-1 bg-primary-100 text-primary-800 rounded-full text-sm font-semibold">
                            <?= htmlspecialchars($userClass) ?>
                        </span>
                    </div>
                    <?php if ($user['id'] == $profileUser['id']): ?>
                        <a href="/profile/edit" class="btn btn-secondary">Edit Profile</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Information -->
    <?php if (!empty($profileUser['info'])): ?>
        <div class="card mb-6">
            <h2 class="text-xl font-semibold mb-4 font-display">About</h2>
            <div class="prose max-w-none text-gray-700 font-serif">
                <?= nl2br(htmlspecialchars($profileUser['info'])) ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Stats Card -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Statistics</h2>
            <dl class="space-y-2">
                <div class="flex justify-between">
                    <dt>Uploaded:</dt>
                    <dd class="font-semibold"><?= \App\Core\FormatHelper::bytes($profileUser['uploaded'] ?? 0) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt>Downloaded:</dt>
                    <dd class="font-semibold"><?= \App\Core\FormatHelper::bytes($profileUser['downloaded'] ?? 0) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt>Ratio:</dt>
                    <dd class="font-semibold"><?= $ratio ?></dd>
                </div>
                    <div class="flex justify-between">
                        <dt>Torrents Uploaded:</dt>
                        <dd class="font-semibold"><?= $uploadedTorrents ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>User Class:</dt>
                        <dd class="font-semibold">
                            <?php
                            $classes = [0 => 'User', 1 => 'Power User', 2 => 'VIP', 3 => 'Uploader', 4 => 'Moderator', 5 => 'Administrator', 6 => 'Sysop'];
                            echo htmlspecialchars($classes[$profileUser['class'] ?? 0] ?? 'User');
                            ?>
                        </dd>
                    </div>
                </dl>
            </div>

        <!-- Achievements Card -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Achievements</h2>
            <div class="text-center mb-4">
                <div class="text-3xl font-bold text-primary-600"><?= number_format($achievementPoints) ?></div>
                <div class="text-sm text-gray-600">Achievement Points</div>
            </div>
            <div class="text-center mb-4">
                <div class="text-2xl font-bold"><?= count($earnedAchievements) ?></div>
                <div class="text-sm text-gray-600">of <?= count($achievements) ?> Achievements Earned</div>
            </div>
            <?php if (!empty($earnedAchievements)): ?>
                <div class="flex flex-wrap gap-2 justify-center">
                    <?php foreach (array_slice($earnedAchievements, 0, 8) as $achievement): ?>
                        <div class="text-2xl" title="<?= htmlspecialchars($achievement['name']) ?>">
                            <?= $achievement['icon'] ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="mt-4 text-center">
                <a href="/achievements<?= $user['id'] == $profileUser['id'] ? '' : '?user=' . $profileUser['id'] ?>" 
                   class="text-primary-600 hover:underline text-sm">View All Achievements</a>
            </div>
        </div>

        <!-- Reputation Card -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Reputation</h2>
            <div class="text-center">
                <div class="text-4xl font-bold text-primary-600 mb-2"><?= $reputation ?></div>
                <div class="text-lg text-gray-600"><?= htmlspecialchars($reputationLevel) ?></div>
            </div>
        </div>

        <!-- Activity Card -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Activity</h2>
            <dl class="space-y-2">
                <div class="flex justify-between">
                    <dt>Active Torrents:</dt>
                    <dd><?= count($peers) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt>Last Access:</dt>
                    <dd><?= \App\Core\FormatHelper::timeAgo($profileUser['last_access'] ?? 0) ?></dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Quick Actions -->
    <?php if ($user['id'] == $profileUser['id']): ?>
        <div class="card mb-6">
            <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
            <div class="flex flex-wrap gap-4">
                <a href="/mytorrents" class="btn btn-secondary">My Torrents</a>
                <a href="/userhistory" class="btn btn-secondary">My History</a>
                <a href="/friends" class="btn btn-secondary">Friends & Blocks</a>
                <a href="/profile/edit" class="btn btn-secondary">Edit Profile</a>
            </div>
        </div>
    <?php else: ?>
        <div class="card mb-6">
            <div class="flex gap-4">
                <a href="/messages/compose?to=<?= urlencode($profileUser['username']) ?>" class="btn btn-primary">Send Message</a>
                <a href="/friends/add?targetid=<?= $profileUser['id'] ?>&type=friend" class="btn btn-secondary">Add Friend</a>
                <a href="/userhistory?id=<?= $profileUser['id'] ?>" class="btn btn-secondary">View History</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Active Torrents -->
    <?php if (!empty($peers)): ?>
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Active Torrents</h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Torrent</th>
                            <th>Status</th>
                            <th>Uploaded</th>
                            <th>Downloaded</th>
                            <th>Ratio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($peers as $peer): ?>
                            <tr>
                                <td>
                                    <a href="/torrent/<?= $peer['torrent'] ?>" class="text-primary-600 hover:underline">
                                        <?= htmlspecialchars($peer['torrent_name'] ?? 'Unknown') ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="<?= $peer['seeder'] === 'yes' ? 'text-green-600' : 'text-orange-600' ?>">
                                        <?= $peer['seeder'] === 'yes' ? 'Seeding' : 'Leeching' ?>
                                    </span>
                                </td>
                                <td><?= \App\Core\FormatHelper::bytes($peer['uploaded'] ?? 0) ?></td>
                                <td><?= \App\Core\FormatHelper::bytes($peer['downloaded'] ?? 0) ?></td>
                                <td><?= \App\Core\FormatHelper::ratio($peer['uploaded'] ?? 0, $peer['downloaded'] ?? 0) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

