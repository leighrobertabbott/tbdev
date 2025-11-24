<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="mb-4">
        <a href="/admin" class="text-primary-600 hover:underline">← Back to Admin</a>
    </div>

    <div class="card mb-6">
        <h1 class="text-2xl font-bold mb-4">MySQL Overview</h1>
        <p class="text-gray-600">Detailed database information and status</p>
    </div>

    <div class="card mb-6">
        <h2 class="text-xl font-semibold mb-4">Tables</h2>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Table Name</th>
                        <th>Rows</th>
                        <th>Data Size (MB)</th>
                        <th>Index Size (MB)</th>
                        <th>Total Size (MB)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tables as $table): ?>
                        <tr>
                            <td class="font-mono text-sm"><?= htmlspecialchars($table['table_name']) ?></td>
                            <td><?= number_format($table['table_rows']) ?></td>
                            <td><?= number_format($table['data_mb'], 2) ?></td>
                            <td><?= number_format($table['index_mb'], 2) ?></td>
                            <td class="font-semibold"><?= number_format($table['size_mb'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h2 class="text-xl font-semibold mb-4">MySQL Status Variables</h2>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Variable</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($status as $var): ?>
                        <tr>
                            <td class="font-mono text-sm"><?= htmlspecialchars($var['Variable_name']) ?></td>
                            <td class="font-mono"><?= htmlspecialchars($var['Value']) ?></td>
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

