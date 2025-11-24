<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="/admin" class="text-primary-600 hover:underline">← Back to Admin</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Database Cleanup</h1>

        <?php if (isset($results) && !empty($results)): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <h3 class="font-semibold mb-2">Cleanup Results:</h3>
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach ($results as $action => $count): ?>
                        <li><?= ucfirst(str_replace('_', ' ', $action)) ?>: <?= number_format($count) ?> items removed</li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="space-y-4">
            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <h3 class="font-semibold">Dead Peers</h3>
                        <p class="text-sm text-gray-600">Remove peers that haven't announced in 30 minutes</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold"><?= number_format($stats['dead_peers'] ?? 0) ?></div>
                        <form method="POST" action="/admin/cleanup" class="mt-2">
                            <input type="hidden" name="action" value="dead_peers">
                            <button type="submit" class="btn btn-primary text-sm">Clean</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <h3 class="font-semibold">Old Torrents</h3>
                        <p class="text-sm text-gray-600">Hide torrents with no seeders for 30+ days</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold"><?= number_format($stats['old_torrents'] ?? 0) ?></div>
                        <form method="POST" action="/admin/cleanup" class="mt-2">
                            <input type="hidden" name="action" value="old_torrents">
                            <button type="submit" class="btn btn-primary text-sm">Clean</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <h3 class="font-semibold">Orphaned Files</h3>
                        <p class="text-sm text-gray-600">Remove file entries for deleted torrents</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold"><?= number_format($stats['orphaned_files'] ?? 0) ?></div>
                        <form method="POST" action="/admin/cleanup" class="mt-2">
                            <input type="hidden" name="action" value="orphaned_files">
                            <button type="submit" class="btn btn-primary text-sm">Clean</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <h3 class="font-semibold">Orphaned Comments</h3>
                        <p class="text-sm text-gray-600">Remove comments for deleted torrents</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold"><?= number_format($stats['orphaned_comments'] ?? 0) ?></div>
                        <form method="POST" action="/admin/cleanup" class="mt-2">
                            <input type="hidden" name="action" value="orphaned_comments">
                            <button type="submit" class="btn btn-primary text-sm">Clean</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <h3 class="font-semibold">Old Logs</h3>
                        <p class="text-sm text-gray-600">Remove logs older than 30 days</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold"><?= number_format($stats['old_logs'] ?? 0) ?></div>
                        <form method="POST" action="/admin/cleanup" class="mt-2">
                            <input type="hidden" name="action" value="old_logs">
                            <button type="submit" class="btn btn-primary text-sm">Clean</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>

