<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="/forum/<?= $forum['id'] ?>" class="text-primary-600 hover:underline">← Back to Forum</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">New Topic in <?= htmlspecialchars($forum['name']) ?></h1>

        <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/forum/<?= $forum['id'] ?>/new-topic" class="space-y-4">
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                <input type="text" id="subject" name="subject" required 
                       class="input" maxlength="255" placeholder="Topic subject">
            </div>

            <div>
                <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea id="body" name="body" rows="12" required 
                          class="input" placeholder="Enter your message..."></textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/forum/<?= $forum['id'] ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Topic</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

