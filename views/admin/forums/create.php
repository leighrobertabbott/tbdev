<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="/admin/forums" class="text-primary-600 hover:underline">← Back to Forums</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Create Forum</h1>

        <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/admin/forums/create" class="space-y-4">
            <input type="hidden" name="_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Forum Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required 
                       class="input" maxlength="60" placeholder="e.g., ANNOUNCEMENTS">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea id="description" name="description" rows="3" 
                          class="input" maxlength="200" placeholder="Forum description..."></textarea>
            </div>

            <div>
                <label for="section_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Section <span class="text-red-500">*</span>
                </label>
                <select id="section_id" name="section_id" required class="input">
                    <option value="">Select a section</option>
                    <?php foreach ($sections ?? [] as $section): ?>
                        <option value="<?= $section['id'] ?>"><?= htmlspecialchars($section['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">The section this forum belongs to</p>
            </div>

            <div>
                <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Parent Forum (Optional)
                </label>
                <select id="parent_id" name="parent_id" class="input">
                    <option value="0">None (Main Forum)</option>
                    <?php foreach ($forums ?? [] as $forum): ?>
                        <option value="<?= $forum['id'] ?>"><?= htmlspecialchars($forum['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">Leave as "None" to create a main forum, or select a parent to create a subforum</p>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label for="min_class_read" class="block text-sm font-medium text-gray-700 mb-1">
                        Minimum Read Class
                    </label>
                    <input type="number" id="min_class_read" name="min_class_read" 
                           value="0" min="0" max="6" class="input">
                </div>

                <div>
                    <label for="min_class_write" class="block text-sm font-medium text-gray-700 mb-1">
                        Minimum Write Class
                    </label>
                    <input type="number" id="min_class_write" name="min_class_write" 
                           value="0" min="0" max="6" class="input">
                </div>

                <div>
                    <label for="min_class_create" class="block text-sm font-medium text-gray-700 mb-1">
                        Minimum Create Class
                    </label>
                    <input type="number" id="min_class_create" name="min_class_create" 
                           value="0" min="0" max="6" class="input">
                </div>
            </div>

            <div>
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">
                    Sort Order
                </label>
                <input type="number" id="sort" name="sort" 
                       value="0" min="0" class="input">
                <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/admin/forums" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Forum</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>
