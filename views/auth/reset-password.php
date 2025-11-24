<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-md mx-auto">
    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Reset Password</h1>

        <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/recover/reset?token=<?= htmlspecialchars($token) ?>&email=<?= urlencode($email) ?>" class="space-y-4">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    New Password
                </label>
                <input type="password" id="password" name="password" required 
                       class="input" minlength="8">
            </div>

            <div>
                <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">
                    Confirm Password
                </label>
                <input type="password" id="password_confirm" name="password_confirm" required 
                       class="input" minlength="8">
            </div>

            <button type="submit" class="btn btn-primary w-full">Reset Password</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

