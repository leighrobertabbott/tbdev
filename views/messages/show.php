<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="/messages" class="text-primary-600 hover:underline">← Back to Messages</a>
    </div>

    <div class="card">
        <div class="border-b pb-4 mb-4">
            <h1 class="text-2xl font-bold mb-2"><?= htmlspecialchars($message['subject']) ?></h1>
            <div class="flex items-center space-x-4 text-sm text-gray-600">
                <span>From: <a href="/user/<?= $message['sender'] ?>" class="text-primary-600 hover:underline"><?= htmlspecialchars($message['sender_name'] ?? 'Unknown') ?></a></span>
                <span>To: <a href="/user/<?= $message['receiver'] ?>" class="text-primary-600 hover:underline"><?= htmlspecialchars($message['receiver_name'] ?? 'Unknown') ?></a></span>
                <span><?= \App\Core\FormatHelper::date($message['added']) ?></span>
            </div>
        </div>

        <div class="prose max-w-none mb-6">
            <?= nl2br(htmlspecialchars($message['msg'])) ?>
        </div>

        <div class="flex space-x-4 pt-4 border-t">
            <a href="/messages/compose?to=<?= urlencode($message['sender'] == $user['id'] ? $message['receiver_name'] : $message['sender_name']) ?>" 
               class="btn btn-primary">Reply</a>
            <?php if ($message['receiver'] == $user['id']): ?>
                <form method="POST" action="/messages/<?= $message['id'] ?>/delete" class="inline">
                    <button type="submit" class="btn btn-secondary" onclick="return confirm('Delete this message?')">Delete</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

