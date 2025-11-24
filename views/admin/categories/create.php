<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="/admin/categories" class="text-primary-600 hover:underline">← Back to Categories</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Create Category</h1>

        <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/admin/categories/create" class="space-y-4">
            <input type="hidden" name="_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Category Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required 
                       class="input" maxlength="30" placeholder="e.g., Movies">
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                    Image Filename
                </label>
                <input type="text" id="image" name="image" 
                       class="input" placeholder="e.g., movies.gif">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea id="description" name="description" rows="3" 
                          class="input" placeholder="Category description"></textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/admin/categories" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Category</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>

