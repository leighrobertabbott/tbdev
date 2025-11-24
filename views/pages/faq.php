<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="card">
        <h1 class="text-3xl font-bold mb-6">Frequently Asked Questions</h1>
        
        <div class="prose max-w-none">
            <h2>What is a BitTorrent tracker?</h2>
            <p>A BitTorrent tracker is a server that helps coordinate file sharing between peers in a BitTorrent network.</p>

            <h2>How do I upload a torrent?</h2>
            <p>Click on the "Upload" link in the navigation menu, fill in the required information, and select your .torrent file.</p>

            <h2>What is a good ratio?</h2>
            <p>A ratio of 1.0 or higher is generally considered good. This means you've uploaded at least as much as you've downloaded.</p>

            <h2>How do I maintain a good ratio?</h2>
            <p>Keep your torrents seeding after downloading. The longer you seed, the more you upload and the better your ratio becomes.</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

