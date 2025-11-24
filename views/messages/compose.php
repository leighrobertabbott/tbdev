<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Compose Message</h1>

        <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/messages/send" class="space-y-4">
            <div>
                <label for="to" class="block text-sm font-medium text-gray-700 mb-1">To</label>
                <input type="text" id="to" name="to" required 
                       class="input" value="<?= htmlspecialchars($toUser['username'] ?? '') ?>" 
                       placeholder="Username">
            </div>

            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                <input type="text" id="subject" name="subject" required 
                       class="input" placeholder="Message subject">
            </div>

            <div>
                <label for="msg" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea id="msg" name="msg" rows="10" required 
                          class="input" placeholder="Enter your message..."></textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/messages" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

