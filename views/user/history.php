<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold mb-6">User History</h1>

    <!-- Tabs -->
    <div class="border-b mb-6">
        <nav class="flex space-x-8">
            <a href="/userhistory?action=posts<?= $viewUserId != $user['id'] ? '&id=' .
                $viewUserId : '' ?>"
               class="<?= $action === 'posts' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500' ?> py-4 px-1 border-b-2 font-medium">
                Forum Posts
            </a>
            <a href="/userhistory?action=comments<?= $viewUserId != $user['id'] ? '&id=' .
                $viewUserId : '' ?>"
               class="<?= $action === 'comments' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500' ?> py-4 px-1 border-b-2 font-medium">
                Comments
            </a>
            <a href="/userhistory?action=torrents<?= $viewUserId != $user['id'] ? '&id=' .
                $viewUserId : '' ?>"
               class="<?= $action === 'torrents' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500' ?> py-4 px-1 border-b-2 font-medium">
                Torrents
            </a>
        </nav>
    </div>

    <div class="card">
        <?php if (empty($data)): ?>
            <p class="text-gray-600 text-center py-8">No <?= $action ?> found.</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php if ($action === 'posts'): ?>
                    <?php foreach ($data as $post): ?>
                        <div class="border-b pb-4 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <a href="/topic/<?= $post['topic'] ?>" class="font-semibold text-primary-600 hover:underline">
                                        <?= htmlspecialchars($post['topic_subject'] ?? 'Unknown Topic') ?>
                                    </a>
                                    <p class="text-sm text-gray-600 mt-1">
                                        in <a href="/forum/<?= $post['forum'] ?? '' ?>" class="text-primary-600 hover:underline">
                                            <?= htmlspecialchars($post['forum_name'] ?? 'Unknown Forum') ?>
                                        </a>
                                    </p>
                                    <p class="text-gray-700 mt-2 line-clamp-2">
                                        <?= htmlspecialchars(substr($post['body'], 0, 200)) ?>...
                                    </p>
                                </div>
                                <span class="text-sm text-gray-500">
                                    <?= \App\Core\FormatHelper::timeAgo($post['added']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php elseif ($action === 'comments'): ?>
                    <?php foreach ($data as $comment): ?>
                        <div class="border-b pb-4 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <a href="/torrent/<?= $comment['torrent'] ?>" class="font-semibold text-primary-600 hover:underline">
                                        <?= htmlspecialchars($comment['torrent_name'] ?? 'Unknown Torrent') ?>
                                    </a>
                                    <p class="text-gray-700 mt-2 line-clamp-2">
                                        <?= htmlspecialchars(substr($comment['text'], 0, 200)) ?>...
                                    </p>
                                </div>
                                <span class="text-sm text-gray-500">
                                    <?= \App\Core\FormatHelper::timeAgo($comment['added']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php elseif ($action === 'torrents'): ?>
                    <?php foreach ($data as $torrent): ?>
                        <div class="border-b pb-4 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <a href="/torrent/<?= $torrent['id'] ?>" class="font-semibold text-primary-600 hover:underline">
                                        <?= htmlspecialchars($torrent['name']) ?>
                                    </a>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Size: <?= \App\Core\FormatHelper::bytes($torrent['size'] ?? 0) ?>
                                        • Seeders: <?= $torrent['seeders'] ?? 0 ?>
                                        • Leechers: <?= $torrent['leechers'] ?? 0 ?>
                                    </p>
                                </div>
                                <span class="text-sm text-gray-500">
                                    <?= \App\Core\FormatHelper::timeAgo($torrent['added']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

