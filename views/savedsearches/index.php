<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Saved Searches</h1>

    <?php if (empty($searches)): ?>
        <div class="card text-center py-12">
            <div class="text-6xl mb-4">🔍</div>
            <h2 class="text-xl font-semibold mb-2">No Saved Searches</h2>
            <p class="text-gray-600 mb-4">Save your favorite searches for quick access</p>
            <a href="/browse" class="btn btn-primary">Browse Torrents</a>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Query</th>
                            <th>Filters</th>
                            <th>Saved</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($searches as $search): ?>
                            <tr>
                                <td class="font-semibold"><?= htmlspecialchars($search['name']) ?></td>
                                <td><?= htmlspecialchars($search['query']) ?></td>
                                <td class="text-sm text-gray-600">
                                    <?php 
                                    $filters = json_decode($search['filters'] ?? '{}', true);
                                    if (!empty($filters)) {
                                        echo implode(', ', array_keys($filters));
                                    } else {
                                        echo 'None';
                                    }
                                    ?>
                                </td>
                                <td class="text-sm"><?= \App\Core\FormatHelper::date($search['created_at']) ?></td>
                                <td>
                                    <div class="flex space-x-2">
                                        <a href="/browse?q=<?= urlencode($search['query']) ?>" 
                                           class="text-primary-600 hover:underline text-sm">Run</a>
                                        <button onclick="deleteSearch(<?= $search['id'] ?>)" 
                                                class="text-red-600 hover:underline text-sm">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function deleteSearch(id) {
    if (!confirm('Delete this saved search?')) return;
    
    fetch(`/savedsearches/${id}/delete`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
        },
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

