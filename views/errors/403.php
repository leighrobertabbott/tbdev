<?php
$content = ob_get_clean();
ob_start();
?>

<div class="text-center py-12">
    <h1 class="text-6xl font-bold text-gray-300 mb-4">403</h1>
    <h2 class="text-2xl font-semibold mb-4">Access Denied</h2>
    <p class="text-gray-600 mb-8">You don't have permission to access this resource.</p>
    <a href="/" class="btn btn-primary">Go Home</a>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

