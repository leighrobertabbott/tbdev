<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Torrent Management</h1>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
        <form method="GET" action="/admin/torrents" class="flex space-x-2">
            <select name="status" class="input">
                <option value="all" <?= ($status ?? 'all') === 'all' ? 'selected' : '' ?>>All Torrents</option>
                <option value="visible" <?= ($status ?? 'all') === 'visible' ? 'selected' : '' ?>>Visible</option>
                <option value="hidden" <?= ($status ?? 'all') === 'hidden' ? 'selected' : '' ?>>Hidden</option>
                <option value="banned" <?= ($status ?? 'all') === 'banned' ? 'selected' : '' ?>>Banned</option>
            </select>
            <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" 
                   placeholder="Search torrents..." class="input flex-1">
            <button type="submit" class="btn btn-primary">Filter</button>
            <?php if (!empty($search) || ($status ?? 'all') !== 'all'): ?>
                <a href="/admin/torrents" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <div class="mb-4 text-sm text-gray-600">
            Showing <?= number_format($total) ?> torrents
        </div>

        <?php if (empty($torrents)): ?>
            <p class="text-gray-600 text-center py-8">No torrents found.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Owner</th>
                            <th>Size</th>
                            <th>Seeders</th>
                            <th>Leechers</th>
                            <th>Status</th>
                            <th>Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($torrents as $t): ?>
                            <tr>
                                <td><?= $t['id'] ?></td>
                                <td>
                                    <a href="/torrent/<?= $t['id'] ?>" class="text-primary-600 hover:underline">
                                        <?= htmlspecialchars($t['name']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($t['category_name'] ?? 'N/A') ?></td>
                                <td>
                                    <a href="/user/<?= $t['owner'] ?>" class="text-primary-600 hover:underline">
                                        <?= htmlspecialchars($t['owner_name'] ?? 'Unknown') ?>
                                    </a>
                                </td>
                                <td><?= \App\Core\FormatHelper::bytes($t['size'] ?? 0) ?></td>
                                <td class="text-green-600"><?= number_format($t['seeders'] ?? 0) ?></td>
                                <td class="text-orange-600"><?= number_format($t['leechers'] ?? 0) ?></td>
                                <td>
                                    <?php if ($t['banned'] === 'yes'): ?>
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Banned</span>
                                    <?php elseif ($t['visible'] === 'yes'): ?>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Visible</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Hidden</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-sm"><?= \App\Core\FormatHelper::date($t['added']) ?></td>
                                <td>
                                    <div class="flex space-x-2">
                                        <a href="/admin/torrents/<?= $t['id'] ?>/edit" class="text-primary-600 hover:underline text-sm">Edit</a>
                                        <a href="/torrent/<?= $t['id'] ?>" class="text-primary-600 hover:underline text-sm" target="_blank">View</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="mt-4 flex justify-center space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&status=<?= urlencode($status ?? 'all') ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="btn btn-secondary">Previous</a>
                    <?php endif; ?>
                    <span class="px-4 py-2">Page <?= $page ?> of <?= $totalPages ?></span>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&status=<?= urlencode($status ?? 'all') ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="btn btn-secondary">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>

