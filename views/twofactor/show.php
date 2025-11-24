<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Two-Factor Authentication</h1>

    <?php if (isset($error)): ?>
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($enabled): ?>
        <div class="card mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-green-600">2FA is Enabled</h2>
                    <p class="text-sm text-gray-600">Your account is protected with two-factor authentication</p>
                </div>
                <span class="text-3xl">🔒</span>
            </div>

            <form method="POST" action="/twofactor/disable" class="mt-4">
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Enter your password to disable 2FA
                    </label>
                    <input type="password" id="password" name="password" required class="input">
                </div>
                <button type="submit" class="btn btn-danger">Disable 2FA</button>
            </form>
        </div>
    <?php else: ?>
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Enable Two-Factor Authentication</h2>
            <p class="text-gray-700 mb-6">
                Two-factor authentication adds an extra layer of security to your account. 
                You'll need to enter a code from your authenticator app when logging in.
            </p>

            <?php if ($qrUrl): ?>
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold mb-2">Step 1: Scan QR Code</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Scan this QR code with your authenticator app (Google Authenticator, Authy, etc.)
                    </p>
                    <div class="flex justify-center mb-4">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($qrUrl) ?>" 
                             alt="QR Code" class="border rounded-lg">
                    </div>
                    <p class="text-xs text-gray-500 text-center">
                        Or enter this code manually: <code class="font-mono bg-white px-2 py-1 rounded"><?= htmlspecialchars($secret) ?></code>
                    </p>
                </div>

                <form method="POST" action="/twofactor/enable" class="space-y-4">
                    <input type="hidden" name="secret" value="<?= htmlspecialchars($secret) ?>">
                    
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                            Step 2: Enter Verification Code
                        </label>
                        <input type="text" id="code" name="code" required 
                               placeholder="000000" maxlength="6" pattern="[0-9]{6}"
                               class="input text-center text-2xl tracking-widest">
                        <p class="text-xs text-gray-500 mt-1">Enter the 6-digit code from your authenticator app</p>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">Enable 2FA</button>
                </form>
            <?php else: ?>
                <p class="text-gray-600">Generating QR code...</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

