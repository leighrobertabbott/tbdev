<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="/admin/torrents" class="text-primary-600 hover:underline">← Back to Torrents</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Edit Torrent: <?= htmlspecialchars($torrent['name']) ?></h1>

        <form method="POST" action="/admin/torrents/<?= $torrent['id'] ?>/edit" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Torrent Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required 
                       value="<?= htmlspecialchars($torrent['name']) ?>"
                       class="input" maxlength="255">
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                    Category <span class="text-red-500">*</span>
                </label>
                <select id="category" name="category" class="input" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($torrent['category'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="visible" class="block text-sm font-medium text-gray-700 mb-1">
                    Visibility
                </label>
                <select id="visible" name="visible" class="input">
                    <option value="yes" <?= ($torrent['visible'] ?? 'yes') === 'yes' ? 'selected' : '' ?>>Visible</option>
                    <option value="no" <?= ($torrent['visible'] ?? 'yes') === 'no' ? 'selected' : '' ?>>Hidden</option>
                </select>
            </div>

            <div>
                <label for="banned" class="block text-sm font-medium text-gray-700 mb-1">
                    Banned
                </label>
                <select id="banned" name="banned" class="input">
                    <option value="no" <?= ($torrent['banned'] ?? 'no') === 'no' ? 'selected' : '' ?>>No</option>
                    <option value="yes" <?= ($torrent['banned'] ?? 'no') === 'yes' ? 'selected' : '' ?>>Yes</option>
                </select>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea id="description" name="description" rows="6" 
                          class="input"><?= htmlspecialchars($torrent['descr'] ?? '') ?></textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/admin/torrents" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>

