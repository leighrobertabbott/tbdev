<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="/admin/users" class="text-primary-600 hover:underline">← Back to Users</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Search Users</h1>

        <form method="GET" action="/admin/users/search" class="mb-6">
            <div class="flex space-x-2">
                <input type="text" name="q" value="<?= htmlspecialchars($query ?? '') ?>" 
                       placeholder="Search by username, email, or IP..." class="input flex-1">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <?php if (!empty($query)): ?>
            <?php if (empty($results)): ?>
                <p class="text-gray-600 text-center py-8">No users found matching "<?= htmlspecialchars($query) ?>".</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Class</th>
                                <th>Status</th>
                                <th>Last Access</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $u): ?>
                                <tr>
                                    <td><?= $u['id'] ?></td>
                                    <td>
                                        <a href="/admin/users/<?= $u['id'] ?>" class="text-primary-600 hover:underline">
                                            <?= htmlspecialchars($u['username']) ?>
                                        </a>
                                    </td>
                                    <td class="text-sm"><?= htmlspecialchars($u['email']) ?></td>
                                    <td>
                                        <?php
                                        $classes = [0 => 'User', 1 => 'Power User', 2 => 'VIP', 3 => 'Uploader', 4 => 'Moderator', 5 => 'Administrator', 6 => 'Sysop'];
                                        echo htmlspecialchars($classes[$u['class'] ?? 0] ?? 'User');
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($u['enabled'] === 'yes'): ?>
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Enabled</span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Disabled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-sm"><?= \App\Core\FormatHelper::timeAgo($u['last_access']) ?></td>
                                    <td>
                                        <a href="/admin/users/<?= $u['id'] ?>" class="text-primary-600 hover:underline text-sm">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>

