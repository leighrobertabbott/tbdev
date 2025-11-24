<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold mb-6">Top 10 Torrents</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Top Seeded -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Most Seeded</h2>
            <ol class="space-y-2">
                <?php foreach ($topSeeded as $index => $torrent): ?>
                    <li class="flex items-start">
                        <span class="font-bold text-primary-600 mr-2"><?= $index + 1 ?>.</span>
                        <a href="/torrent/<?= $torrent['id'] ?>" class="text-primary-600 hover:underline flex-1">
                            <?= htmlspecialchars($torrent['name']) ?>
                        </a>
                        <span class="text-green-600 ml-2"><?= $torrent['seeders'] ?? 0 ?></span>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>

        <!-- Top Downloaded -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Most Downloaded</h2>
            <ol class="space-y-2">
                <?php foreach ($topDownloaded as $index => $torrent): ?>
                    <li class="flex items-start">
                        <span class="font-bold text-primary-600 mr-2"><?= $index + 1 ?>.</span>
                        <a href="/torrent/<?= $torrent['id'] ?>" class="text-primary-600 hover:underline flex-1">
                            <?= htmlspecialchars($torrent['name']) ?>
                        </a>
                        <span class="text-gray-600 ml-2"><?= $torrent['times_completed'] ?? 0 ?></span>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>

        <!-- Top Rated -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Top Rated</h2>
            <ol class="space-y-2">
                <?php foreach ($topRated as $index => $torrent): ?>
                    <li class="flex items-start">
                        <span class="font-bold text-primary-600 mr-2"><?= $index + 1 ?>.</span>
                        <a href="/torrent/<?= $torrent['id'] ?>" class="text-primary-600 hover:underline flex-1">
                            <?= htmlspecialchars($torrent['name']) ?>
                        </a>
                        <span class="text-yellow-600 ml-2">★ <?= number_format($torrent['rating'] ?? 0, 1) ?></span>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

