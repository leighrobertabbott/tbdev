<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="/torrent/<?= $torrent['id'] ?>" class="text-primary-600 hover:underline">← Back to Torrent</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-4">
            NFO for <a href="/torrent/<?= $torrent['id'] ?>" class="text-primary-600 hover:underline"><?= htmlspecialchars($torrent['name']) ?></a>
        </h1>

        <div class="bg-black text-green-400 p-4 rounded-lg font-mono text-sm overflow-x-auto">
            <pre class="whitespace-pre-wrap"><?= htmlspecialchars($nfo) ?></pre>
        </div>

        <p class="text-center text-sm text-gray-600 mt-4">
            For best viewing results, use a monospace font such as 
            <a href="ftp://<?= $_SERVER['HTTP_HOST'] ?>/misc/linedraw.ttf" class="text-primary-600 hover:underline">LineDraw</a>.
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

