<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Messages</h1>
        <a href="/messages/compose" class="btn btn-primary">Compose</a>
    </div>

    <!-- Tabs -->
    <div class="border-b mb-6">
        <nav class="flex space-x-8">
            <a href="/messages?location=in" 
               class="<?= $location === 'in' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' ?> py-4 px-1 border-b-2 font-medium">
                Inbox <?= $location === 'in' && $unreadCount > 0 ? "({$unreadCount})" : '' ?>
            </a>
            <a href="/messages?location=out" 
               class="<?= $location === 'out' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' ?> py-4 px-1 border-b-2 font-medium">
                Sent
            </a>
        </nav>
    </div>

    <!-- Messages List -->
    <div class="card">
        <?php if (empty($messages)): ?>
            <p class="text-gray-600 text-center py-8">No messages.</p>
        <?php else: ?>
            <div class="divide-y">
                <?php foreach ($messages as $msg): ?>
                    <a href="/messages/<?= $msg['id'] ?>" class="block p-4 hover:bg-gray-50 <?= $msg['unread'] === 'yes' ? 'bg-blue-50' : '' ?>">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-semibold">
                                        <?= $location === 'in' ? htmlspecialchars($msg['sender_name'] ?? 'Unknown') : htmlspecialchars($msg['receiver_name'] ?? 'Unknown') ?>
                                    </span>
                                    <?php if ($msg['unread'] === 'yes'): ?>
                                        <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded">New</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-gray-900 font-medium mt-1"><?= htmlspecialchars($msg['subject']) ?></p>
                                <p class="text-gray-600 text-sm mt-1 line-clamp-2"><?= htmlspecialchars(substr($msg['msg'], 0, 100)) ?>...</p>
                            </div>
                            <span class="text-sm text-gray-500">
                                <?= \App\Core\FormatHelper::timeAgo($msg['added']) ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

