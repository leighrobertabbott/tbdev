<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold mb-6">Statistics</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Users</h2>
            <dl class="space-y-2">
                <div class="flex justify-between">
                    <dt>Total Users:</dt>
                    <dd class="font-semibold"><?= number_format($stats['users']) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt>Confirmed:</dt>
                    <dd class="text-green-600"><?= number_format($stats['users_confirmed']) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt>Pending:</dt>
                    <dd class="text-orange-600"><?= number_format($stats['users_pending']) ?></dd>
                </div>
            </dl>
        </div>

        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Torrents</h2>
            <dl class="space-y-2">
                <div class="flex justify-between">
                    <dt>Total Torrents:</dt>
                    <dd class="font-semibold"><?= number_format($stats['torrents']) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt>Visible:</dt>
                    <dd class="text-green-600"><?= number_format($stats['torrents_visible']) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt>Hidden:</dt>
                    <dd class="text-gray-600"><?= number_format($stats['torrents_hidden']) ?></dd>
                </div>
            </dl>
        </div>

        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Peers</h2>
            <dl class="space-y-2">
                <div class="flex justify-between">
                    <dt>Total Peers:</dt>
                    <dd class="font-semibold"><?= number_format($peerStats['total_peers'] ?? 0) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt>Seeders:</dt>
                    <dd class="text-green-600"><?= number_format($peerStats['seeders'] ?? 0) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt>Leechers:</dt>
                    <dd class="text-orange-600"><?= number_format($peerStats['leechers'] ?? 0) ?></dd>
                </div>
            </dl>
        </div>

        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Content</h2>
            <dl class="space-y-2">
                <div class="flex justify-between">
                    <dt>Comments:</dt>
                    <dd><?= number_format($stats['comments']) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt>Forum Topics:</dt>
                    <dd><?= number_format($stats['topics']) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt>Forum Posts:</dt>
                    <dd><?= number_format($stats['posts']) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt>Messages:</dt>
                    <dd><?= number_format($stats['messages']) ?></dd>
                </div>
            </dl>
        </div>

        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Traffic</h2>
            <dl class="space-y-2">
                <div class="flex justify-between">
                    <dt>Total Uploaded:</dt>
                    <dd class="font-semibold"><?= \App\Core\FormatHelper::bytes($trafficStats['total_uploaded'] ?? 0) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt>Total Downloaded:</dt>
                    <dd class="font-semibold"><?= \App\Core\FormatHelper::bytes($trafficStats['total_downloaded'] ?? 0) ?></dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Top Uploaders -->
    <div class="card mb-6">
        <h2 class="text-xl font-semibold mb-4">Top Uploaders</h2>
        <?php if (empty($topUploaders)): ?>
            <p class="text-gray-600">No data available.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Uploaded</th>
                            <th>Downloaded</th>
                            <th>Ratio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topUploaders as $uploader): ?>
                            <tr>
                                <td>
                                    <a href="/user/<?= $uploader['id'] ?>" class="text-primary-600 hover:underline">
                                        <?= htmlspecialchars($uploader['username']) ?>
                                    </a>
                                </td>
                                <td><?= \App\Core\FormatHelper::bytes($uploader['uploaded']) ?></td>
                                <td><?= \App\Core\FormatHelper::bytes($uploader['downloaded']) ?></td>
                                <td><?= \App\Core\FormatHelper::ratio($uploader['uploaded'], $uploader['downloaded']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Top Torrents -->
    <div class="card">
        <h2 class="text-xl font-semibold mb-4">Top Torrents (by Seeders)</h2>
        <?php if (empty($topTorrents)): ?>
            <p class="text-gray-600">No data available.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Torrent</th>
                            <th>Size</th>
                            <th>Seeders</th>
                            <th>Leechers</th>
                            <th>Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topTorrents as $torrent): ?>
                            <tr>
                                <td>
                                    <a href="/torrent/<?= $torrent['id'] ?>" class="text-primary-600 hover:underline">
                                        <?= htmlspecialchars($torrent['name']) ?>
                                    </a>
                                </td>
                                <td><?= \App\Core\FormatHelper::bytes($torrent['size']) ?></td>
                                <td class="text-green-600"><?= number_format($torrent['seeders']) ?></td>
                                <td class="text-orange-600"><?= number_format($torrent['leechers']) ?></td>
                                <td><?= number_format($torrent['times_completed']) ?></td>
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
include __DIR__ . '/../../layouts/app.php';
?>

