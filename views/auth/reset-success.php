<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-md mx-auto">
    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Password Reset Successful</h1>

        <p class="text-gray-700 mb-4">
            Your password has been successfully reset. You can now log in with your new password.
        </p>

        <div class="mt-6 text-center">
            <a href="/login" class="btn btn-primary">Go to Login</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

