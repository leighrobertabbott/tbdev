<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold mb-6">Admin Panel</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="card">
            <h2 class="text-xl font-semibold mb-2">Users</h2>
            <div class="text-3xl font-bold text-primary-600"><?= number_format($stats['users']) ?></div>
            <a href="/admin/users" class="text-primary-600 hover:underline text-sm mt-2 inline-block">Manage Users</a>
        </div>

        <div class="card">
            <h2 class="text-xl font-semibold mb-2">Torrents</h2>
            <div class="text-3xl font-bold text-primary-600"><?= number_format($stats['torrents']) ?></div>
            <div class="text-sm text-gray-600 mt-1"><?= $stats['pending_torrents'] ?> pending</div>
            <a href="/admin/torrents" class="text-primary-600 hover:underline text-sm mt-2 inline-block">Manage Torrents</a>
        </div>

        <div class="card">
            <h2 class="text-xl font-semibold mb-2">Peers</h2>
            <div class="text-sm text-gray-600">
                <div>Seeders: <span class="text-green-600 font-bold"><?= number_format($peerStats['seeders'] ?? 0) ?></span></div>
                <div>Leechers: <span class="text-orange-600 font-bold"><?= number_format($peerStats['leechers'] ?? 0) ?></span></div>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="text-xl font-semibold mb-4">Admin Tools</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <a href="/admin/users" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="font-semibold">User Management</div>
                <div class="text-sm text-gray-600">Manage users</div>
            </a>
            <a href="/admin/torrents" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="font-semibold">Torrent Management</div>
                <div class="text-sm text-gray-600">Manage torrents</div>
            </a>
            <a href="/admin/news" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="font-semibold">News Management</div>
                <div class="text-sm text-gray-600">Manage news</div>
            </a>
            <a href="/admin/categories" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="font-semibold">Categories</div>
                <div class="text-sm text-gray-600">Manage categories</div>
            </a>
            <a href="/admin/bans" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="font-semibold">Bans</div>
                <div class="text-sm text-gray-600">Manage IP bans</div>
            </a>
            <a href="/admin/forums" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="font-semibold">Forums</div>
                <div class="text-sm text-gray-600">Manage forums</div>
            </a>
            <a href="/admin/stats" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="font-semibold">Statistics</div>
                <div class="text-sm text-gray-600">View stats</div>
            </a>
            <a href="/admin/logs" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="font-semibold">Logs</div>
                <div class="text-sm text-gray-600">View site logs</div>
            </a>
            <a href="/admin/cleanup" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="font-semibold">Cleanup</div>
                <div class="text-sm text-gray-600">Database cleanup</div>
            </a>
            <a href="/admin/iptest" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="font-semibold">IP Test</div>
                <div class="text-sm text-gray-600">Test IP bans</div>
            </a>
            <a href="/admin/mysql/stats" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="font-semibold">MySQL Stats</div>
                <div class="text-sm text-gray-600">Database stats</div>
            </a>
            <a href="/admin/mysql/overview" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="font-semibold">MySQL Overview</div>
                <div class="text-sm text-gray-600">Database overview</div>
            </a>
            <a href="/admin/analytics" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="font-semibold">Analytics Dashboard</div>
                <div class="text-sm text-gray-600">View analytics</div>
            </a>
            <a href="/admin/settings" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="font-semibold">Site Settings</div>
                <div class="text-sm text-gray-600">Customize site</div>
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

