<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-md mx-auto">
    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Confirmation Successful</h1>

        <p class="text-gray-700 mb-4">
            <?= htmlspecialchars($message ?? 'Your account has been confirmed!') ?>
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

