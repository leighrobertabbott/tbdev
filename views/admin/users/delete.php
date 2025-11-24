<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="/admin/users/<?= $targetUser['id'] ?>" class="text-primary-600 hover:underline">← Back to User</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6 text-red-600">Delete User Account</h1>

        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800 font-semibold mb-2">Warning: This action cannot be undone!</p>
            <p class="text-red-700">
                You are about to permanently delete the account for <strong><?= htmlspecialchars($targetUser['username']) ?></strong>.
                This will delete all associated data including torrents, comments, and messages.
            </p>
        </div>

        <form method="POST" action="/admin/users/<?= $targetUser['id'] ?>/delete" class="space-y-4">
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="sure" value="1" required class="mr-2">
                    <span>I understand this action is permanent and cannot be undone</span>
                </label>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/admin/users/<?= $targetUser['id'] ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-danger">Delete Account</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>

