<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-6xl mx-auto">
    <div class="mb-4">
        <a href="/collections" class="text-primary-600 hover:underline">← Back to Collections</a>
    </div>

    <div class="card mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-3xl font-bold mb-2"><?= htmlspecialchars($collection['name']) ?></h1>
                <?php if (!empty($collection['description'])): ?>
                    <p class="text-gray-600"><?= nl2br(htmlspecialchars($collection['description'])) ?></p>
                <?php endif; ?>
            </div>
            <div class="text-right">
                <?php if ($collection['is_public'] === 'yes'): ?>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Public</span>
                <?php else: ?>
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">Private</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="text-sm text-gray-600">
            <span><?= number_format(count($torrents)) ?> torrents</span>
            <span class="mx-2">•</span>
            <span>Updated <?= \App\Core\FormatHelper::timeAgo($collection['updated_at']) ?></span>
        </div>
    </div>

    <?php if (empty($torrents)): ?>
        <div class="card text-center py-12">
            <div class="text-6xl mb-4">📦</div>
            <h2 class="text-xl font-semibold mb-2">Collection is Empty</h2>
            <p class="text-gray-600">Add torrents to this collection to get started</p>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Torrent</th>
                            <th>Category</th>
                            <th>Size</th>
                            <th>Seeders</th>
                            <th>Leechers</th>
                            <th>Added</th>
                            <?php if ($user['id'] == $collection['user_id']): ?>
                                <th>Actions</th>
                            <?php endif; ?>
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
                                <td><?= htmlspecialchars($torrent['category_name'] ?? 'N/A') ?></td>
                                <td><?= \App\Core\FormatHelper::bytes($torrent['size']) ?></td>
                                <td class="text-green-600"><?= number_format($torrent['seeders'] ?? 0) ?></td>
                                <td class="text-orange-600"><?= number_format($torrent['leechers'] ?? 0) ?></td>
                                <td class="text-sm"><?= \App\Core\FormatHelper::date($torrent['added']) ?></td>
                                <?php if ($user['id'] == $collection['user_id']): ?>
                                    <td>
                                        <button onclick="removeFromCollection(<?= $collection['id'] ?>, <?= $torrent['id'] ?>)" 
                                                class="text-red-600 hover:underline text-sm">Remove</button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function removeFromCollection(collectionId, torrentId) {
    if (!confirm('Remove this torrent from the collection?')) return;
    
    fetch(`/collections/${collectionId}/remove/${torrentId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    });
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

