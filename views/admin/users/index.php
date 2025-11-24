<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">User Management</h1>
        <div class="flex space-x-2">
            <a href="/admin/users/search" class="btn btn-secondary">Search Users</a>
            <a href="/admin/users/add" class="btn btn-primary">Add User</a>
        </div>
    </div>

    <!-- Search Form -->
    <div class="card mb-6">
        <form method="GET" action="/admin/users" class="flex space-x-2">
            <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" 
                   placeholder="Search by username or email..." class="input flex-1">
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if (!empty($search)): ?>
                <a href="/admin/users" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <div class="mb-4 text-sm text-gray-600">
            Showing <?= number_format($total) ?> users
        </div>

        <?php if (empty($users)): ?>
            <p class="text-gray-600 text-center py-8">No users found.</p>
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
                            <th>Added</th>
                            <th>Last Access</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
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
                                    <?php if ($u['warned'] === 'yes'): ?>
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs ml-1">Warned</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-sm"><?= \App\Core\FormatHelper::date($u['added']) ?></td>
                                <td class="text-sm"><?= \App\Core\FormatHelper::timeAgo($u['last_access']) ?></td>
                                <td>
                                    <div class="flex space-x-2">
                                        <a href="/admin/users/<?= $u['id'] ?>/edit" class="text-primary-600 hover:underline text-sm">Edit</a>
                                        <a href="/user/<?= $u['id'] ?>" class="text-primary-600 hover:underline text-sm" target="_blank">View</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="mt-4 flex justify-center space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="btn btn-secondary">Previous</a>
                    <?php endif; ?>
                    <span class="px-4 py-2">Page <?= $page ?> of <?= $totalPages ?></span>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="btn btn-secondary">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>

