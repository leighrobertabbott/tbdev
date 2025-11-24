<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">My Collections</h1>
        <a href="/collections/create" class="btn btn-primary">Create Collection</a>
    </div>

    <?php if (empty($collections)): ?>
        <div class="card text-center py-12">
            <div class="text-6xl mb-4">📚</div>
            <h2 class="text-xl font-semibold mb-2">No Collections Yet</h2>
            <p class="text-gray-600 mb-4">Create collections to organize your favorite torrents</p>
            <a href="/collections/create" class="btn btn-primary">Create Your First Collection</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($collections as $collection): ?>
                <div class="card hover:shadow-lg transition">
                    <div class="flex justify-between items-start mb-3">
                        <h3 class="text-lg font-semibold">
                            <a href="/collections/<?= $collection['id'] ?>" class="text-primary-600 hover:underline">
                                <?= htmlspecialchars($collection['name']) ?>
                            </a>
                        </h3>
                        <?php if ($collection['is_public'] === 'yes'): ?>
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Public</span>
                        <?php else: ?>
                            <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">Private</span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($collection['description'])): ?>
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2"><?= htmlspecialchars($collection['description']) ?></p>
                    <?php endif; ?>
                    <div class="flex justify-between items-center text-sm text-gray-600">
                        <span><?= number_format($collection['torrent_count']) ?> torrents</span>
                        <span><?= \App\Core\FormatHelper::date($collection['updated_at']) ?></span>
                    </div>
                    <div class="mt-3 flex space-x-2">
                        <a href="/collections/<?= $collection['id'] ?>" class="btn btn-secondary text-sm flex-1">View</a>
                        <a href="/collections/<?= $collection['id'] ?>/delete" class="btn btn-danger text-sm">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

