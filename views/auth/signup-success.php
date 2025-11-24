<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-md mx-auto">
    <div class="card">
        <div class="text-center">
            <div class="mb-4">
                <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold mb-4">Sign Up Successful!</h1>
            <p class="text-gray-600 mb-6">
                Your account has been created. Please check your email to confirm your account.
            </p>
            <a href="/login" class="btn btn-primary">Go to Login</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>


