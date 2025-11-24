<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="flex justify-between items-start mb-4">
        <h1 class="text-3xl font-bold"><?= htmlspecialchars($torrent['name']) ?></h1>
        <?php if ($owned): ?>
            <a href="/torrent/<?= $torrent['id'] ?>/edit" class="btn btn-secondary">Edit</a>
        <?php endif; ?>
    </div>

    <!-- Torrent Info -->
    <div class="card mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-xl font-semibold mb-4">Information</h2>
                <dl class="space-y-2">
                    <div class="flex">
                        <dt class="font-medium w-32">Category:</dt>
                        <dd><?= htmlspecialchars($torrent['category_name'] ?? 'N/A') ?></dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Size:</dt>
                        <dd><?= \App\Core\FormatHelper::bytes($torrent['size'] ?? 0) ?></dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Added:</dt>
                        <dd><?= \App\Core\FormatHelper::date($torrent['added']) ?></dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Uploaded by:</dt>
                        <dd>
                            <a href="/user/<?= $torrent['owner'] ?>" class="text-primary-600 hover:underline">
                                <?= htmlspecialchars($torrent['owner_name'] ?? 'Unknown') ?>
                            </a>
                        </dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Views:</dt>
                        <dd><?= number_format($torrent['views'] ?? 0) ?></dd>
                    </div>
                </dl>
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-4">Statistics</h2>
                <dl class="space-y-2">
                    <div class="flex">
                        <dt class="font-medium w-32">Seeders:</dt>
                        <dd class="text-green-600 font-bold"><?= number_format($peerStats['seeders'] ?? 0) ?></dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Leechers:</dt>
                        <dd class="text-orange-600 font-bold"><?= number_format($peerStats['leechers'] ?? 0) ?></dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Total Peers:</dt>
                        <dd><?= number_format($peerStats['total_peers'] ?? 0) ?></dd>
                    </div>
                    <div class="flex">
                        <dt class="font-medium w-32">Completed:</dt>
                        <dd><?= number_format($torrent['times_completed'] ?? 0) ?></dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Download Button & Rating -->
        <div class="mt-6 pt-6 border-t">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <a href="/download/<?= $torrent['id'] ?>" class="btn btn-primary text-lg px-8">
                    Download Torrent
                </a>
                
                <!-- Rating -->
                <?php if ($user): ?>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Rate:</span>
                        <form method="POST" action="/rate" class="flex space-x-1">
                            <input type="hidden" name="id" value="<?= $torrent['id'] ?>">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <button type="submit" name="rating" value="<?= $i ?>" 
                                        class="text-2xl hover:text-yellow-500 transition-colors focus:outline-none">
                                    ★
                                </button>
                            <?php endfor; ?>
                        </form>
                        <?php 
                        $avgRating = ($torrent['numratings'] ?? 0) > 0 
                            ? round(($torrent['ratingsum'] ?? 0) / ($torrent['numratings'] ?? 1), 1) 
                            : 0;
                        if ($avgRating > 0): ?>
                            <span class="text-sm text-gray-600">
                                (<?= $avgRating ?> / 5 from <?= number_format($torrent['numratings'] ?? 0) ?> ratings)
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Description -->
    <?php if (!empty($torrent['descr'])): ?>
        <div class="card mb-6">
            <h2 class="text-xl font-semibold mb-4">Description</h2>
            <div class="prose max-w-none">
                <?= nl2br(htmlspecialchars($torrent['descr'])) ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Related Torrents -->
    <?php if (!empty($related)): ?>
        <div class="card mb-6">
            <h2 class="text-xl font-semibold mb-4">Related Torrents</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($related as $relatedTorrent): ?>
                    <div class="border rounded-lg p-4 hover:shadow-lg transition">
                        <a href="/torrent/<?= $relatedTorrent['id'] ?>" class="block">
                            <h3 class="font-semibold mb-2 line-clamp-2 text-sm"><?= htmlspecialchars($relatedTorrent['name']) ?></h3>
                            <div class="text-xs text-gray-600 space-y-1">
                                <div><?= \App\Core\FormatHelper::bytes($relatedTorrent['size']) ?></div>
                                <div class="flex justify-between">
                                    <span class="text-green-600"><?= number_format($relatedTorrent['seeders'] ?? 0) ?> ↑</span>
                                    <span class="text-orange-600"><?= number_format($relatedTorrent['leechers'] ?? 0) ?> ↓</span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Comments -->
    <div class="card">
        <h2 class="text-xl font-semibold mb-4">Comments (<?= count($comments) ?>)</h2>
        
        <?php if (empty($comments)): ?>
            <p class="text-gray-600">No comments yet.</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($comments as $comment): ?>
                    <div class="border-b pb-4 last:border-b-0">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <a href="/user/<?= $comment['user'] ?>" class="font-semibold text-primary-600 hover:underline">
                                    <?= htmlspecialchars($comment['username'] ?? 'Unknown') ?>
                                </a>
                                <span class="text-sm text-gray-500 ml-2">
                                    <?= \App\Core\FormatHelper::date($comment['added']) ?>
                                </span>
                            </div>
                            <?php if ($user['id'] == $comment['user'] || ($user['class'] ?? 0) >= 4): ?>
                                <div class="flex space-x-2">
                                    <a href="/comment/<?= $comment['id'] ?>/edit" class="text-sm text-primary-600 hover:underline">Edit</a>
                                    <a href="/comment/<?= $comment['id'] ?>/delete" class="text-sm text-red-600 hover:underline">Delete</a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="text-gray-700">
                            <?= nl2br(htmlspecialchars($comment['text'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Add Comment Form -->
        <div class="mt-6 pt-6 border-t">
            <h3 class="text-lg font-semibold mb-4">Add Comment</h3>
            <form method="POST" action="/comment" class="space-y-4">
                <input type="hidden" name="torrent_id" value="<?= $torrent['id'] ?>">
                <textarea name="text" rows="4" class="input" required placeholder="Enter your comment..."></textarea>
                <button type="submit" class="btn btn-primary">Post Comment</button>
            </form>
        </div>
    </div>
</div>

<script>
window.Format = window.Format || {
    bytes: (bytes) => {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    },
    date: (timestamp) => new Date(timestamp * 1000).toLocaleDateString()
};
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

