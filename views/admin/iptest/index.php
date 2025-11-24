<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="/admin" class="text-primary-600 hover:underline">← Back to Admin</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">IP Test</h1>

        <form method="GET" action="/admin/iptest" class="mb-6">
            <div class="flex space-x-2">
                <input type="text" name="ip" value="<?= htmlspecialchars($result['ip'] ?? '') ?>" 
                       placeholder="Enter IP address (e.g., 192.168.1.1)" class="input flex-1">
                <button type="submit" class="btn btn-primary">Test</button>
            </div>
        </form>

        <?php if (isset($result)): ?>
            <?php if (isset($result['error'])): ?>
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <?= htmlspecialchars($result['error']) ?>
                </div>
            <?php else: ?>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold mb-3">Test Results</h3>
                    <dl class="space-y-2">
                        <div class="flex">
                            <dt class="font-medium w-32">IP Address:</dt>
                            <dd class="font-mono"><?= htmlspecialchars($result['ip']) ?></dd>
                        </div>
                        <div class="flex">
                            <dt class="font-medium w-32">IP (Long):</dt>
                            <dd class="font-mono"><?= $result['ip_long'] ?></dd>
                        </div>
                        <div class="flex">
                            <dt class="font-medium w-32">Status:</dt>
                            <dd>
                                <?php if ($result['banned']): ?>
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full font-semibold">BANNED</span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full font-semibold">NOT BANNED</span>
                                <?php endif; ?>
                            </dd>
                        </div>
                        <?php if ($result['banned'] && isset($result['ban'])): ?>
                            <div class="mt-4 pt-4 border-t">
                                <h4 class="font-semibold mb-2">Ban Details:</h4>
                                <dl class="space-y-1 text-sm">
                                    <div class="flex">
                                        <dt class="font-medium w-24">Range:</dt>
                                        <dd><?= long2ip($result['ban']['first']) ?> - <?= long2ip($result['ban']['last']) ?></dd>
                                    </div>
                                    <div class="flex">
                                        <dt class="font-medium w-24">Comment:</dt>
                                        <dd><?= htmlspecialchars($result['ban']['comment'] ?? '') ?></dd>
                                    </div>
                                    <div class="flex">
                                        <dt class="font-medium w-24">Added:</dt>
                                        <dd><?= \App\Core\FormatHelper::date($result['ban']['added']) ?></dd>
                                    </div>
                                </dl>
                            </div>
                        <?php endif; ?>
                    </dl>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>

