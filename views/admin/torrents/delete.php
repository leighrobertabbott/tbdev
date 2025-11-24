<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="/admin/torrents" class="text-primary-600 hover:underline">← Back to Torrents</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6 text-red-600">Delete Torrent</h1>

        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800 font-semibold mb-2">Warning: This action cannot be undone!</p>
            <p class="text-red-700">
                You are about to permanently delete the torrent <strong><?= htmlspecialchars($torrent['name']) ?></strong>.
                This will delete all associated files, comments, and peer data.
            </p>
        </div>

        <form method="POST" action="/admin/torrents/<?= $torrent['id'] ?>/delete" class="space-y-4">
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="sure" value="1" required class="mr-2">
                    <span>I understand this action is permanent and cannot be undone</span>
                </label>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/admin/torrents" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-danger">Delete Torrent</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>

