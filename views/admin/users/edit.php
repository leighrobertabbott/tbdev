<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="/admin/users/<?= $targetUser['id'] ?>" class="text-primary-600 hover:underline">← Back to User</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Edit User: <?= htmlspecialchars($targetUser['username']) ?></h1>

        <form method="POST" action="/admin/users/<?= $targetUser['id'] ?>/edit" class="space-y-4">
            <div>
                <label for="class" class="block text-sm font-medium text-gray-700 mb-1">
                    User Class
                </label>
                <select id="class" name="class" class="input">
                    <option value="0" <?= ($targetUser['class'] ?? 0) == 0 ? 'selected' : '' ?>>User</option>
                    <option value="1" <?= ($targetUser['class'] ?? 0) == 1 ? 'selected' : '' ?>>Power User</option>
                    <option value="2" <?= ($targetUser['class'] ?? 0) == 2 ? 'selected' : '' ?>>VIP</option>
                    <option value="3" <?= ($targetUser['class'] ?? 0) == 3 ? 'selected' : '' ?>>Uploader</option>
                    <option value="4" <?= ($targetUser['class'] ?? 0) == 4 ? 'selected' : '' ?>>Moderator</option>
                    <option value="5" <?= ($targetUser['class'] ?? 0) == 5 ? 'selected' : '' ?>>Administrator</option>
                    <option value="6" <?= ($targetUser['class'] ?? 0) == 6 ? 'selected' : '' ?>>Sysop</option>
                </select>
            </div>

            <div>
                <label for="enabled" class="block text-sm font-medium text-gray-700 mb-1">
                    Account Status
                </label>
                <select id="enabled" name="enabled" class="input">
                    <option value="yes" <?= ($targetUser['enabled'] ?? 'yes') === 'yes' ? 'selected' : '' ?>>Enabled</option>
                    <option value="no" <?= ($targetUser['enabled'] ?? 'yes') === 'no' ? 'selected' : '' ?>>Disabled</option>
                </select>
            </div>

            <div>
                <label for="warned" class="block text-sm font-medium text-gray-700 mb-1">
                    Warned
                </label>
                <select id="warned" name="warned" class="input">
                    <option value="no" <?= ($targetUser['warned'] ?? 'no') === 'no' ? 'selected' : '' ?>>No</option>
                    <option value="yes" <?= ($targetUser['warned'] ?? 'no') === 'yes' ? 'selected' : '' ?>>Yes</option>
                </select>
            </div>

            <div>
                <label for="donor" class="block text-sm font-medium text-gray-700 mb-1">
                    Donor
                </label>
                <select id="donor" name="donor" class="input">
                    <option value="no" <?= ($targetUser['donor'] ?? 'no') === 'no' ? 'selected' : '' ?>>No</option>
                    <option value="yes" <?= ($targetUser['donor'] ?? 'no') === 'yes' ? 'selected' : '' ?>>Yes</option>
                </select>
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Custom Title
                </label>
                <input type="text" id="title" name="title" 
                       value="<?= htmlspecialchars($targetUser['title'] ?? '') ?>"
                       class="input" maxlength="30">
            </div>

            <div>
                <label for="modcomment" class="block text-sm font-medium text-gray-700 mb-1">
                    Moderator Comment
                </label>
                <textarea id="modcomment" name="modcomment" rows="4" 
                          class="input"><?= htmlspecialchars($targetUser['modcomment'] ?? '') ?></textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/admin/users/<?= $targetUser['id'] ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>

