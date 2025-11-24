<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Manage Categories</h1>
        <a href="/admin/categories/create" class="btn btn-primary">Add Category</a>
    </div>

    <div class="card">
        <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($categories)): ?>
            <p class="text-gray-600">No categories found.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><?= $cat['id'] ?></td>
                                <td><?= htmlspecialchars($cat['name']) ?></td>
                                <td><?= htmlspecialchars($cat['image'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($cat['cat_desc'] ?? '') ?></td>
                                <td>
                                    <a href="/admin/categories/<?= $cat['id'] ?>/delete" 
                                       class="text-red-600 hover:underline text-sm"
                                       onclick="return confirm('Delete this category?')">Delete</a>
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

