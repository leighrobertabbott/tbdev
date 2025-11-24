<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Manage Bans</h1>
        <a href="/admin/bans/create" class="btn btn-primary">Add Ban</a>
    </div>

    <div class="card">
        <?php if (empty($bans)): ?>
            <p class="text-gray-600">No bans found.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>IP Range</th>
                            <th>Comment</th>
                            <th>Added By</th>
                            <th>Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bans as $ban): ?>
                            <tr>
                                <td>
                                    <?= long2ip($ban['first']) ?> - <?= long2ip($ban['last']) ?>
                                </td>
                                <td><?= htmlspecialchars($ban['comment'] ?? '') ?></td>
                                <td><?= htmlspecialchars($ban['addedby_name'] ?? 'Unknown') ?></td>
                                <td><?= \App\Core\FormatHelper::date($ban['added']) ?></td>
                                <td>
                                    <a href="/admin/bans/<?= $ban['id'] ?>/delete" 
                                       class="text-red-600 hover:underline text-sm"
                                       onclick="return confirm('Delete this ban?')">Delete</a>
                                </td>
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
include __DIR__ . '/../../layouts/app.php';
?>

