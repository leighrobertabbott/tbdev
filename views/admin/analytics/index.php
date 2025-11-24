<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Analytics Dashboard</h1>
        <div>
            <select id="dateRange" onchange="location.href='?range=' + this.value" class="input">
                <option value="1d" <?= $dateRange === '1d' ? 'selected' : '' ?>>Last 24 Hours</option>
                <option value="7d" <?= $dateRange === '7d' ? 'selected' : '' ?>>Last 7 Days</option>
                <option value="30d" <?= $dateRange === '30d' ? 'selected' : '' ?>>Last 30 Days</option>
                <option value="90d" <?= $dateRange === '90d' ? 'selected' : '' ?>>Last 90 Days</option>
            </select>
        </div>
    </div>

    <!-- Activity Stats -->
    <div class="card mb-6">
        <h2 class="text-xl font-semibold mb-4">Activity Statistics</h2>
        <?php if (empty($activityStats)): ?>
            <p class="text-gray-600">No activity data available</p>
        <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php foreach ($activityStats as $stat): ?>
                    <div class="border rounded-lg p-4">
                        <div class="text-2xl font-bold text-primary-600"><?= number_format($stat['count']) ?></div>
                        <div class="text-sm text-gray-600"><?= htmlspecialchars($stat['action']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Daily Registrations -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Daily User Registrations</h2>
            <?php if (empty($dailyRegistrations)): ?>
                <p class="text-gray-600">No registration data</p>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($dailyRegistrations as $day): ?>
                        <div class="flex items-center">
                            <div class="w-24 text-sm text-gray-600"><?= htmlspecialchars($day['date']) ?></div>
                            <div class="flex-1 bg-gray-200 rounded-full h-6 relative">
                                <div class="bg-primary-600 h-6 rounded-full" 
                                     style="width: <?= min(100, ($day['count'] / max(1, max(array_column($dailyRegistrations, 'count'))) * 100)) ?>%"></div>
                                <span class="absolute inset-0 flex items-center justify-center text-xs font-semibold">
                                    <?= number_format($day['count']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Daily Uploads -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Daily Torrent Uploads</h2>
            <?php if (empty($dailyUploads)): ?>
                <p class="text-gray-600">No upload data</p>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($dailyUploads as $day): ?>
                        <div class="flex items-center">
                            <div class="w-24 text-sm text-gray-600"><?= htmlspecialchars($day['date']) ?></div>
                            <div class="flex-1 bg-gray-200 rounded-full h-6 relative">
                                <div class="bg-green-600 h-6 rounded-full" 
                                     style="width: <?= min(100, ($day['count'] / max(1, max(array_column($dailyUploads, 'count'))) * 100)) ?>%"></div>
                                <span class="absolute inset-0 flex items-center justify-center text-xs font-semibold">
                                    <?= number_format($day['count']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Top Users -->
    <div class="card mb-6">
        <h2 class="text-xl font-semibold mb-4">Most Active Users</h2>
        <?php if (empty($topUsers)): ?>
            <p class="text-gray-600">No user activity data</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>User</th>
                            <th>Activity Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; foreach ($topUsers as $user): ?>
                            <tr>
                                <td>#<?= $rank++ ?></td>
                                <td>
                                    <a href="/user/<?= $user['id'] ?>" class="text-primary-600 hover:underline">
                                        <?= htmlspecialchars($user['username']) ?>
                                    </a>
                                </td>
                                <td class="font-semibold"><?= number_format($user['activity_count']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Popular Categories -->
    <div class="card">
        <h2 class="text-xl font-semibold mb-4">Popular Categories</h2>
        <?php if (empty($popularCategories)): ?>
            <p class="text-gray-600">No category data</p>
        <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <?php foreach ($popularCategories as $cat): ?>
                    <div class="border rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-primary-600"><?= number_format($cat['torrent_count']) ?></div>
                        <div class="text-sm text-gray-600"><?= htmlspecialchars($cat['name']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>

