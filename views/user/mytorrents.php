<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold mb-6">My Torrents</h1>

    <div class="card">
        <?php if (empty($torrents)): ?>
            <p class="text-gray-600 text-center py-8">You haven't uploaded any torrents yet.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Size</th>
                            <th>Seeders</th>
                            <th>Leechers</th>
                            <th>Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($torrents as $torrent): ?>
                            <tr>
                                <td>
                                    <a href="/torrent/<?= $torrent['id'] ?>" class="text-primary-600 hover:underline">
                                        <?= htmlspecialchars($torrent['name']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($torrent['category'] ?? 'N/A') ?></td>
                                <td><?= \App\Core\FormatHelper::bytes($torrent['size'] ?? 0) ?></td>
                                <td class="text-green-600"><?= $torrent['seeders'] ?? 0 ?></td>
                                <td class="text-orange-600"><?= $torrent['leechers'] ?? 0 ?></td>
                                <td><?= \App\Core\FormatHelper::date($torrent['added']) ?></td>
                                <td>
                                    <a href="/torrent/<?= $torrent['id'] ?>/edit" class="text-primary-600 hover:underline text-sm">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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

