<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="/collections" class="text-primary-600 hover:underline">← Back to Collections</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Create Collection</h1>

        <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/collections/create" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Collection Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required 
                       class="input" maxlength="255" placeholder="My Favorite Movies">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea id="description" name="description" rows="4" 
                          class="input" placeholder="Optional description..."></textarea>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_public" value="yes" class="mr-2">
                    <span class="text-sm text-gray-700">Make this collection public</span>
                </label>
                <p class="text-xs text-gray-500 mt-1">Public collections can be viewed by other users</p>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/collections" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Collection</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

