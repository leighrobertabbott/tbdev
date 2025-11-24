<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-md mx-auto mt-12">
    <div class="card">
        <h1 class="text-2xl font-bold mb-4 text-center">Two-Factor Authentication</h1>
        
        <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <p class="text-gray-600 mb-6 text-center">
            Please enter the 6-digit code from your authenticator app
        </p>

        <form method="POST" action="/login" class="space-y-4">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id ?? '') ?>">
            <input type="hidden" name="returnto" value="<?= htmlspecialchars($returnto ?? '/') ?>">
            
            <div>
                <label for="two_factor_code" class="block text-sm font-medium text-gray-700 mb-1">
                    Verification Code
                </label>
                <input type="text" id="two_factor_code" name="two_factor_code" required 
                       placeholder="000000" maxlength="6" pattern="[0-9]{6}"
                       class="input text-center text-2xl tracking-widest w-full">
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600 mb-2">Or use a backup code:</p>
                <input type="text" name="backup_code" 
                       placeholder="Backup code" 
                       class="input text-center w-full">
            </div>

            <button type="submit" class="btn btn-primary w-full">Verify & Login</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

