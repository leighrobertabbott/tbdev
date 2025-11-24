<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-md mx-auto">
    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Password Recovery</h1>

        <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <p class="text-gray-600 mb-4">
            Enter your email address and we'll send you a link to reset your password.
        </p>

        <form method="POST" action="/recover" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email Address
                </label>
                <input type="email" id="email" name="email" required 
                       class="input" placeholder="your@email.com">
            </div>

            <button type="submit" class="btn btn-primary w-full">Send Recovery Email</button>
        </form>

        <div class="mt-4 text-center">
            <a href="/login" class="text-primary-600 hover:underline">Back to Login</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

