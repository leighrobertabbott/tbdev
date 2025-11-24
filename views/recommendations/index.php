<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Recommendations for You</h1>

    <!-- Personalized Recommendations -->
    <div class="card mb-6">
        <h2 class="text-2xl font-semibold mb-4">Based on Your Activity</h2>
        <?php if (empty($recommendations)): ?>
            <p class="text-gray-600">No recommendations available yet. Start downloading torrents to get personalized recommendations!</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php foreach ($recommendations as $torrent): ?>
                    <div class="border rounded-lg p-4 hover:shadow-lg transition">
                        <a href="/torrent/<?= $torrent['id'] ?>" class="block">
                            <h3 class="font-semibold mb-2 line-clamp-2"><?= htmlspecialchars($torrent['name']) ?></h3>
                            <div class="text-sm text-gray-600 space-y-1">
                                <div>Category: <?= htmlspecialchars($torrent['category_name'] ?? 'N/A') ?></div>
                                <div>Size: <?= \App\Core\FormatHelper::bytes($torrent['size']) ?></div>
                                <div class="flex justify-between">
                                    <span class="text-green-600"><?= number_format($torrent['seeders'] ?? 0) ?> seeders</span>
                                    <span class="text-orange-600"><?= number_format($torrent['leechers'] ?? 0) ?> leechers</span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Trending -->
    <div class="card mb-6">
        <h2 class="text-2xl font-semibold mb-4">Trending Now</h2>
        <?php if (!empty($trending)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <?php foreach ($trending as $torrent): ?>
                    <div class="border rounded-lg p-4 hover:shadow-lg transition">
                        <a href="/torrent/<?= $torrent['id'] ?>" class="block">
                            <h3 class="font-semibold mb-2 text-sm line-clamp-2"><?= htmlspecialchars($torrent['name']) ?></h3>
                            <div class="text-xs text-gray-600">
                                <div><?= \App\Core\FormatHelper::bytes($torrent['size']) ?></div>
                                <div class="text-green-600"><?= number_format($torrent['seeders'] ?? 0) ?> ↑</div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Popular -->
    <div class="card">
        <h2 class="text-2xl font-semibold mb-4">Popular Torrents</h2>
        <?php if (!empty($popular)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <?php foreach ($popular as $torrent): ?>
                    <div class="border rounded-lg p-4 hover:shadow-lg transition">
                        <a href="/torrent/<?= $torrent['id'] ?>" class="block">
                            <h3 class="font-semibold mb-2 text-sm line-clamp-2"><?= htmlspecialchars($torrent['name']) ?></h3>
                            <div class="text-xs text-gray-600">
                                <div><?= \App\Core\FormatHelper::bytes($torrent['size']) ?></div>
                                <div class="text-green-600"><?= number_format($torrent['seeders'] ?? 0) ?> seeders</div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

