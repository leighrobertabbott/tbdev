<?php
$content = ob_get_clean();
ob_start();
?>

<div class="text-center py-12">
    <h1 class="text-6xl font-bold text-gray-300 mb-4">404</h1>
    <h2 class="text-2xl font-semibold mb-4">Page Not Found</h2>
    <p class="text-gray-600 mb-8">The page you're looking for doesn't exist.</p>
    <a href="/" class="btn btn-primary">Go Home</a>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>


