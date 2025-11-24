<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Polls</h1>
        <?php if (($user['class'] ?? 0) >= 4): ?>
            <div class="flex space-x-2">
                <a href="/polls/create" class="btn btn-primary">Create Poll</a>
                <a href="/polls/manage" class="btn btn-secondary">Manage</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Status Tabs -->
    <div class="border-b mb-6">
        <nav class="flex space-x-8">
            <a href="/polls?status=active" 
               class="<?= $status === 'active' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500' ?> py-4 px-1 border-b-2 font-medium">
                Active
            </a>
            <a href="/polls?status=closed" 
               class="<?= $status === 'closed' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500' ?> py-4 px-1 border-b-2 font-medium">
                Closed
            </a>
            <a href="/polls?status=archived" 
               class="<?= $status === 'archived' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500' ?> py-4 px-1 border-b-2 font-medium">
                Archived
            </a>
        </nav>
    </div>

    <!-- Polls List -->
    <div class="space-y-4">
        <?php if (empty($polls)): ?>
            <div class="card">
                <p class="text-gray-600 text-center py-8">No polls found.</p>
            </div>
        <?php else: ?>
            <?php foreach ($polls as $poll): ?>
                <div class="card hover:shadow-lg transition-shadow">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h2 class="text-xl font-semibold mb-2">
                                <a href="/poll/<?= $poll['id'] ?>" class="text-primary-600 hover:underline">
                                    <?= htmlspecialchars($poll['question']) ?>
                                </a>
                            </h2>
                            <?php if (!empty($poll['description'])): ?>
                                <p class="text-gray-600 mb-2"><?= htmlspecialchars($poll['description']) ?></p>
                            <?php endif; ?>
                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                <span>Created by <?= htmlspecialchars($poll['creator_name'] ?? 'Unknown') ?></span>
                                <span>•</span>
                                <span><?= \App\Core\FormatHelper::timeAgo($poll['created_at']) ?></span>
                                <span>•</span>
                                <span><?= number_format($poll['total_votes']) ?> votes</span>
                                <?php if ($poll['expires_at']): ?>
                                    <span>•</span>
                                    <span>Expires: <?= \App\Core\FormatHelper::date($poll['expires_at']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="ml-4">
                            <?php if ($poll['status'] === 'active'): ?>
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Active</span>
                            <?php elseif ($poll['status'] === 'closed'): ?>
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium">Closed</span>
                            <?php else: ?>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">Archived</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

