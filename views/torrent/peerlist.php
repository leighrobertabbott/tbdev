<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-6xl mx-auto">
    <div class="mb-4">
        <a href="/torrent/<?= $torrent['id'] ?>" class="text-primary-600 hover:underline">← Back to Torrent</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-4">
            Peer List: <a href="/torrent/<?= $torrent['id'] ?>" class="text-primary-600 hover:underline"><?= htmlspecialchars($torrent['name']) ?></a>
        </h1>

        <?php if (empty($peers)): ?>
            <p class="text-gray-600">No active peers for this torrent.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>IP Address</th>
                            <th>Port</th>
                            <th>Status</th>
                            <th>Uploaded</th>
                            <th>Downloaded</th>
                            <th>Ratio</th>
                            <th>Client</th>
                            <th>Started</th>
                            <th>Last Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($peers as $peer): ?>
                            <tr>
                                <td>
                                    <?php if ($peer['username']): ?>
                                        <a href="/user/<?= $peer['userid'] ?>" class="text-primary-600 hover:underline">
                                            <?= htmlspecialchars($peer['username']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-500">Anonymous</span>
                                    <?php endif; ?>
                                </td>
                                <td class="font-mono text-sm"><?= htmlspecialchars($peer['ip']) ?></td>
                                <td><?= $peer['port'] ?></td>
                                <td>
                                    <?php if ($peer['seeder'] === 'yes'): ?>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Seeder</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs">Leecher</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= \App\Core\FormatHelper::bytes($peer['uploaded'] ?? 0) ?></td>
                                <td><?= \App\Core\FormatHelper::bytes($peer['downloaded'] ?? 0) ?></td>
                                <td><?= \App\Core\FormatHelper::ratio($peer['uploaded'] ?? 0, $peer['downloaded'] ?? 0) ?></td>
                                <td class="text-sm"><?= htmlspecialchars($peer['agent'] ?? 'Unknown') ?></td>
                                <td class="text-sm"><?= \App\Core\FormatHelper::timeAgo($peer['started'] ?? 0) ?></td>
                                <td class="text-sm"><?= \App\Core\FormatHelper::timeAgo($peer['last_action'] ?? 0) ?></td>
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

