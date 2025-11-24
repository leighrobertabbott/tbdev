<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="/admin/users" class="text-primary-600 hover:underline">← Back to Users</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Add User</h1>

        <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/admin/users/add" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                    Username <span class="text-red-500">*</span>
                </label>
                <input type="text" id="username" name="username" required 
                       class="input" maxlength="40">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email" required 
                       class="input" maxlength="80">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password" id="password" name="password" required 
                       class="input" minlength="8">
            </div>

            <div>
                <label for="class" class="block text-sm font-medium text-gray-700 mb-1">
                    User Class
                </label>
                <select id="class" name="class" class="input">
                    <option value="0">User</option>
                    <option value="1">Power User</option>
                    <option value="2">VIP</option>
                    <option value="3">Uploader</option>
                    <option value="4">Moderator</option>
                    <option value="5">Administrator</option>
                    <option value="6">Sysop</option>
                </select>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/admin/users" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create User</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>

