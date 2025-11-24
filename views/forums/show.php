<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-3xl font-bold"><?= htmlspecialchars($forum['name']) ?></h1>
            <p class="text-gray-600"><?= htmlspecialchars($forum['description'] ?? '') ?></p>
        </div>
        <?php if (($user['class'] ?? 0) >= $forum['minclasscreate']): ?>
            <a href="/forum/<?= $forum['id'] ?>/new-topic" class="btn btn-primary">New Topic</a>
        <?php endif; ?>
    </div>

    <div class="card">
        <?php if (empty($topics)): ?>
            <p class="text-gray-600 text-center py-8">No topics yet. Be the first to post!</p>
        <?php else: ?>
            <div class="divide-y">
                <?php foreach ($topics as $topic): ?>
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <?php if ($topic['pinned'] === 'yes'): ?>
                                        <span class="text-yellow-600">📌</span>
                                    <?php endif; ?>
                                    <?php if ($topic['locked'] === 'yes'): ?>
                                        <span class="text-red-600">🔒</span>
                                    <?php endif; ?>
                                    <a href="/topic/<?= $topic['id'] ?>" class="text-lg font-semibold text-primary-600 hover:underline">
                                        <?= htmlspecialchars($topic['subject']) ?>
                                    </a>
                                </div>
                                <div class="text-sm text-gray-600 mt-1">
                                    by <a href="/user/<?= $topic['author'] ?>" class="text-primary-600 hover:underline"><?= htmlspecialchars($topic['author_name'] ?? 'Unknown') ?></a>
                                    • <?= \App\Core\FormatHelper::timeAgo($topic['added']) ?>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <div class="text-sm text-gray-600">
                                    <div><?= number_format($topic['post_count'] ?? 0) ?> posts</div>
                                    <?php if ($topic['last_post_time']): ?>
                                        <div class="text-xs"><?= \App\Core\FormatHelper::timeAgo($topic['last_post_time']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="mt-6 flex justify-center space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>" class="btn btn-secondary">Previous</a>
                    <?php endif; ?>
                    <span class="px-4 py-2 text-gray-700">Page <?= $page ?> of <?= $totalPages ?></span>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>" class="btn btn-secondary">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

