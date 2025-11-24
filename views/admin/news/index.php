<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Manage News</h1>
        <a href="/admin/news/create" class="btn btn-primary">Add News</a>
    </div>

    <div class="card">
        <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($news)): ?>
            <p class="text-gray-600">No news items found.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Headline</th>
                            <th>Author</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($news as $item): ?>
                            <tr>
                                <td><?= $item['id'] ?></td>
                                <td><?= htmlspecialchars($item['headline']) ?></td>
                                <td><?= htmlspecialchars($item['username'] ?? 'Unknown') ?></td>
                                <td><?= \App\Core\FormatHelper::date($item['added']) ?></td>
                                <td>
                                    <a href="/admin/news/<?= $item['id'] ?>/edit" 
                                       class="text-primary-600 hover:underline text-sm mr-3">Edit</a>
                                    <a href="/admin/news/<?= $item['id'] ?>/delete" 
                                       class="text-red-600 hover:underline text-sm"
                                       onclick="return confirm('Delete this news item?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>

