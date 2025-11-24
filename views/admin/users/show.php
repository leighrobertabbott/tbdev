<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="/admin/users" class="text-primary-600 hover:underline">← Back to Users</a>
    </div>

    <div class="card mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-3xl font-bold"><?= htmlspecialchars($targetUser['username']) ?></h1>
                <p class="text-gray-600">User ID: <?= $targetUser['id'] ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/users/<?= $targetUser['id'] ?>/edit" class="btn btn-secondary">Edit</a>
                <a href="/user/<?= $targetUser['id'] ?>" class="btn btn-secondary" target="_blank">View Profile</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-xl font-semibold mb-3">Account Information</h2>
                <dl class="space-y-2">
                    <div class="flex">
                        <dt class="font-medium w-32">Email:</dt>
                        <dd><?= htmlspecialchars($targetUser['email']) ?></dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Class:</dt>
                        <dd>
                            <?php
                            $classes = [0 => 'User', 1 => 'Power User', 2 => 'VIP', 3 => 'Uploader', 4 => 'Moderator', 5 => 'Administrator', 6 => 'Sysop'];
                            echo htmlspecialchars($classes[$targetUser['class'] ?? 0] ?? 'User');
                            ?>
                        </dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Status:</dt>
                        <dd>
                            <?php if ($targetUser['enabled'] === 'yes'): ?>
                                <span class="text-green-600">Enabled</span>
                            <?php else: ?>
                                <span class="text-red-600">Disabled</span>
                            <?php endif; ?>
                        </dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Warned:</dt>
                        <dd><?= $targetUser['warned'] === 'yes' ? 'Yes' : 'No' ?></dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Donor:</dt>
                        <dd><?= $targetUser['donor'] === 'yes' ? 'Yes' : 'No' ?></dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Added:</dt>
                        <dd><?= \App\Core\FormatHelper::date($targetUser['added']) ?></dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Last Access:</dt>
                        <dd><?= \App\Core\FormatHelper::timeAgo($targetUser['last_access']) ?></dd>
                    </div>
                </dl>
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-3">Statistics</h2>
                <dl class="space-y-2">
                    <div class="flex">
                        <dt class="font-medium w-32">Uploaded:</dt>
                        <dd><?= \App\Core\FormatHelper::bytes($targetUser['uploaded'] ?? 0) ?></dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Downloaded:</dt>
                        <dd><?= \App\Core\FormatHelper::bytes($targetUser['downloaded'] ?? 0) ?></dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Ratio:</dt>
                        <dd><?= \App\Core\FormatHelper::ratio($targetUser['uploaded'] ?? 0, $targetUser['downloaded'] ?? 0) ?></dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Torrents:</dt>
                        <dd><?= number_format($torrents) ?></dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Comments:</dt>
                        <dd><?= number_format($comments) ?></dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Forum Posts:</dt>
                        <dd><?= number_format($posts) ?></dd>
                    </div>
                </dl>
            </div>
        </div>

        <?php if (!empty($targetUser['modcomment'])): ?>
            <div class="mt-6 pt-6 border-t">
                <h3 class="font-semibold mb-2">Moderator Comment</h3>
                <p class="text-gray-700"><?= nl2br(htmlspecialchars($targetUser['modcomment'])) ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>

