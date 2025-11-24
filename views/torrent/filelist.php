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
            File List: <a href="/torrent/<?= $torrent['id'] ?>" class="text-primary-600 hover:underline"><?= htmlspecialchars($torrent['name']) ?></a>
        </h1>

        <?php if (empty($files)): ?>
            <p class="text-gray-600">No files found for this torrent.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>File Path</th>
                            <th class="text-right">Size</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $counter = 0;
                        foreach ($files as $file): 
                            $counter++;
                            if ($counter > 0 && $counter % 10 == 0):
                        ?>
                            <tr>
                                <td colspan="2" class="text-right">
                                    <a href="#top" class="text-primary-600 hover:underline">↑ Back to Top</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                            <tr>
                                <td class="font-mono text-sm"><?= htmlspecialchars($file['filename']) ?></td>
                                <td class="text-right"><?= \App\Core\FormatHelper::bytes($file['size']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

