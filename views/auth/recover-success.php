<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-md mx-auto">
    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Recovery Email Sent</h1>

        <p class="text-gray-700 mb-4">
            If an account exists with that email address, we've sent a password recovery link.
        </p>

        <?php if (isset($recoveryUrl)): ?>
            <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded-lg">
                <p class="font-semibold mb-2">Development Mode:</p>
                <a href="<?= htmlspecialchars($recoveryUrl) ?>" class="text-primary-600 hover:underline break-all">
                    <?= htmlspecialchars($recoveryUrl) ?>
                </a>
            </div>
        <?php endif; ?>

        <p class="text-sm text-gray-600">
            Please check your email and click the link to reset your password.
        </p>

        <div class="mt-6 text-center">
            <a href="/login" class="btn btn-primary">Back to Login</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

