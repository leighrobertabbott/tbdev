<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="card">
        <h1 class="text-3xl font-bold mb-6">Upload Torrent</h1>

        <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Tracker URL Information -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h2 class="text-lg font-semibold text-blue-900 mb-2">📡 Tracker URL</h2>
            <p class="text-sm text-blue-800 mb-2">
                When creating your torrent file, use this tracker URL:
            </p>
            <div class="flex items-center space-x-2">
                <code class="flex-1 px-3 py-2 bg-white border border-blue-300 rounded text-sm font-mono text-blue-900 break-all">
                    <?= htmlspecialchars($trackerUrl ?? 'http://localhost:8000/announce.php') ?>
                </code>
                <button onclick="copyTrackerUrl()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                    Copy
                </button>
            </div>
            <p class="text-xs text-blue-700 mt-2">
                ⚠️ Make sure your torrent file includes this tracker URL before uploading.
            </p>
        </div>

        <form method="POST" action="/upload" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">
            <div>
                <label for="torrent" class="block text-sm font-medium text-gray-700 mb-1">
                    Torrent File <span class="text-red-500">*</span>
                </label>
                <input type="file" id="torrent" name="torrent" accept=".torrent" required 
                       class="input">
                <p class="text-xs text-gray-500 mt-1">
                    Maximum file size: <?= \App\Core\FormatHelper::bytes($maxSize) ?>
                </p>
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Torrent Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required 
                       class="input" maxlength="255" placeholder="Enter torrent name">
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                    Category <span class="text-red-500">*</span>
                </label>
                <select id="category" name="category" required class="input">
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea id="description" name="description" rows="8" 
                          class="input" placeholder="Enter torrent description..."></textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/browse" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Upload Torrent</button>
            </div>
        </form>
    </div>
</div>

<script>
function copyTrackerUrl(event) {
    event = event || window.event;
    const url = '<?= htmlspecialchars($trackerUrl ?? 'http://localhost:8000/announce.php', ENT_QUOTES) ?>';
    navigator.clipboard.writeText(url).then(() => {
        const btn = event.target || event.currentTarget;
        const originalText = btn.textContent;
        btn.textContent = 'Copied!';
        btn.classList.add('bg-green-600');
        setTimeout(() => {
            btn.textContent = originalText;
            btn.classList.remove('bg-green-600');
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy:', err);
    });
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>


