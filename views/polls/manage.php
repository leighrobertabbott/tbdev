<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Manage Polls</h1>
        <a href="/polls/create" class="btn btn-primary">Create Poll</a>
    </div>

    <div class="card">
        <?php if (empty($polls)): ?>
            <p class="text-gray-600 text-center py-8">No polls found.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Status</th>
                            <th>Votes</th>
                            <th>Created</th>
                            <th>Expires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($polls as $poll): ?>
                            <tr>
                                <td>
                                    <a href="/poll/<?= $poll['id'] ?>" class="text-primary-600 hover:underline">
                                        <?= htmlspecialchars($poll['question']) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($poll['status'] === 'active'): ?>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Active</span>
                                    <?php elseif ($poll['status'] === 'closed'): ?>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Closed</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">Archived</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= number_format($poll['total_votes']) ?></td>
                                <td><?= \App\Core\FormatHelper::date($poll['created_at']) ?></td>
                                <td>
                                    <?= $poll['expires_at'] ? \App\Core\FormatHelper::date($poll['expires_at']) : 'Never' ?>
                                </td>
                                <td>
                                    <div class="flex space-x-2">
                                        <?php if ($poll['status'] === 'active'): ?>
                                            <a href="/poll/<?= $poll['id'] ?>/close" class="text-sm text-primary-600 hover:underline">Close</a>
                                        <?php endif; ?>
                                        <a href="/poll/<?= $poll['id'] ?>/delete" 
                                           class="text-sm text-red-600 hover:underline"
                                           onclick="return confirm('Delete this poll?')">Delete</a>
                                    </div>
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
include __DIR__ . '/../layouts/app.php';
?>

