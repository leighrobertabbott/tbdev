<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="mb-4">
        <a href="/forum/<?= $topic['forum'] ?>" class="text-primary-600 hover:underline">← Back to Forum</a>
    </div>

    <div class="card mb-6">
        <h1 class="text-3xl font-bold mb-2">
            <?php if ($topic['pinned'] === 'yes'): ?>
                <span class="text-yellow-600">📌</span>
            <?php endif; ?>
            <?php if ($topic['locked'] === 'yes'): ?>
                <span class="text-red-600">🔒</span>
            <?php endif; ?>
            <?= htmlspecialchars($topic['subject']) ?>
        </h1>
        <p class="text-gray-600">
            Started by <a href="/user/<?= $topic['author'] ?>" class="text-primary-600 hover:underline"><?= htmlspecialchars($topic['author_name'] ?? 'Unknown') ?></a>
            • <?= \App\Core\FormatHelper::date($topic['added']) ?>
        </p>
    </div>

    <!-- Posts -->
    <div class="space-y-4 mb-6">
        <?php foreach ($posts as $post): ?>
            <div class="card">
                <div class="flex">
                    <div class="flex-shrink-0 mr-4">
                        <a href="/user/<?= $post['author'] ?>">
                            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                                <span class="text-primary-600 font-bold"><?= strtoupper(substr($post['username'] ?? 'U', 0, 1)) ?></span>
                            </div>
                        </a>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <a href="/user/<?= $post['author'] ?>" class="font-semibold text-primary-600 hover:underline">
                                    <?= htmlspecialchars($post['username'] ?? 'Unknown') ?>
                                </a>
                                <span class="text-sm text-gray-500 ml-2">
                                    <?= \App\Core\FormatHelper::timeAgo($post['added']) ?>
                                </span>
                            </div>
                            <?php if ($user['id'] == $post['author'] || ($user['class'] ?? 0) >= 4): ?>
                                <div class="flex space-x-2">
                                    <a href="/post/<?= $post['id'] ?>/edit" class="text-sm text-primary-600 hover:underline">Edit</a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="prose max-w-none">
                            <?= nl2br(htmlspecialchars($post['body'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Reply Form -->
    <?php if ($topic['locked'] === 'no' || ($user['class'] ?? 0) >= 4): ?>
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Post Reply</h2>
            <form method="POST" action="/topic/<?= $topic['id'] ?>/reply" class="space-y-4">
                <textarea name="body" rows="6" class="input" required placeholder="Enter your reply..."></textarea>
                <button type="submit" class="btn btn-primary">Post Reply</button>
            </form>
        </div>
    <?php else: ?>
        <div class="card bg-gray-100">
            <p class="text-gray-600">This topic is locked.</p>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

