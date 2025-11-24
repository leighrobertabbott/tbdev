<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="card">
        <div class="text-center mb-6">
            <div class="text-6xl mb-4">✅</div>
            <h1 class="text-3xl font-bold text-green-600 mb-2">2FA Successfully Enabled!</h1>
            <p class="text-gray-600">Your account is now protected with two-factor authentication</p>
        </div>

        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <h3 class="font-semibold mb-2">⚠️ Important: Save Your Backup Codes</h3>
            <p class="text-sm text-gray-700 mb-3">
                These backup codes can be used to access your account if you lose your authenticator device.
                <strong>Save them in a safe place!</strong>
            </p>
            <div class="bg-white p-4 rounded border font-mono text-sm space-y-1">
                <?php foreach ($backupCodes as $code): ?>
                    <div><?= htmlspecialchars($code) ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="flex justify-center">
            <a href="/twofactor" class="btn btn-primary">Back to 2FA Settings</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

