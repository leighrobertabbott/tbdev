<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="mb-4">
        <a href="/admin" class="text-primary-600 hover:underline">← Back to Admin</a>
    </div>

    <div class="card mb-6">
        <h1 class="text-2xl font-bold mb-4">MySQL Statistics</h1>
        <dl class="space-y-2">
            <div class="flex">
                <dt class="font-medium w-48">MySQL Version:</dt>
                <dd><?= htmlspecialchars($version) ?></dd>
            </div>
            <div class="flex">
                <dt class="font-medium w-48">Database Size:</dt>
                <dd class="font-semibold"><?= number_format($dbSize['size_mb'] ?? 0, 2) ?> MB</dd>
            </div>
        </dl>
    </div>

    <div class="card">
        <h2 class="text-xl font-semibold mb-4">Table Sizes</h2>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Table Name</th>
                        <th>Rows</th>
                        <th>Data Size (MB)</th>
                        <th>Total Size (MB)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tables as $table): ?>
                        <tr>
                            <td class="font-mono text-sm"><?= htmlspecialchars($table['table_name']) ?></td>
                            <td><?= number_format($table['table_rows']) ?></td>
                            <td><?= number_format($table['size_mb'] - ($table['index_mb'] ?? 0), 2) ?></td>
                            <td class="font-semibold"><?= number_format($table['size_mb'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>

