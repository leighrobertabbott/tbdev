<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="card">
        <h1 class="text-3xl font-bold mb-6">Rules</h1>
        
        <div class="prose max-w-none space-y-4">
            <h2>General Rules</h2>
            <ul>
                <li>Respect all users and staff members</li>
                <li>Do not upload illegal or copyrighted content</li>
                <li>Maintain a good upload ratio</li>
                <li>Do not spam or abuse the system</li>
            </ul>

            <h2>Upload Rules</h2>
            <ul>
                <li>Only upload content you have the right to share</li>
                <li>Provide accurate descriptions</li>
                <li>Use appropriate categories</li>
                <li>Seed your uploads</li>
            </ul>

            <h2>Consequences</h2>
            <p>Violation of these rules may result in warnings, temporary bans, or permanent account termination.</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

