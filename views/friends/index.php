<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold mb-6">Friends & Blocks</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Friends -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Friends</h2>
            <?php if (empty($friends)): ?>
                <p class="text-gray-600">No friends added yet.</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($friends as $friend): ?>
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <a href="/user/<?= $friend['id'] ?>" class="font-semibold text-primary-600 hover:underline">
                                        <?= htmlspecialchars($friend['username']) ?>
                                    </a>
                                    <p class="text-sm text-gray-600">
                                        Last seen: <?= \App\Core\FormatHelper::timeAgo($friend['last_access'] ?? 0) ?>
                                    </p>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="/messages/compose?to=<?= urlencode($friend['username']) ?>" 
                                       class="text-sm text-primary-600 hover:underline">PM</a>
                                    <a href="/friends/delete?targetid=<?= $friend['id'] ?>&type=friend&sure=1" 
                                       class="text-sm text-red-600 hover:underline">Remove</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Blocks -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Blocked Users</h2>
            <?php if (empty($blocks)): ?>
                <p class="text-gray-600">No blocked users.</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($blocks as $block): ?>
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <a href="/user/<?= $block['id'] ?>" class="font-semibold text-primary-600 hover:underline">
                                        <?= htmlspecialchars($block['username']) ?>
                                    </a>
                                </div>
                                <a href="/friends/delete?targetid=<?= $block['id'] ?>&type=block&sure=1" 
                                   class="text-sm text-red-600 hover:underline">Unblock</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

