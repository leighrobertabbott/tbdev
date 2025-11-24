<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold mb-6">Staff</h1>

    <div class="card">
        <?php if (empty($staff)): ?>
            <p class="text-gray-600">No staff members listed.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Title</th>
                            <th>Member Since</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($staff as $member): ?>
                            <tr>
                                <td>
                                    <a href="/user/<?= $member['id'] ?>" class="text-primary-600 hover:underline">
                                        <?= htmlspecialchars($member['username']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($member['title'] ?? 'Staff') ?></td>
                                <td><?= \App\Core\FormatHelper::date($member['added']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

