<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold mb-2">My Achievements</h1>
        <div class="flex space-x-6 text-sm text-gray-600">
            <div>
                <span class="font-semibold text-primary-600"><?= $earnedCount ?></span> of <?= count($achievements) ?> earned
            </div>
            <div>
                <span class="font-semibold text-primary-600"><?= number_format($totalPoints) ?></span> achievement points
            </div>
        </div>
    </div>

    <div class="card mb-6">
        <a href="/achievements/leaderboard" class="btn btn-secondary mb-4">View Leaderboard</a>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($achievements as $achievement): ?>
                <div class="border rounded-lg p-4 <?= $achievement['earned'] ? 'bg-green-50 border-green-200' : 'bg-gray-50' ?>">
                    <div class="flex items-start space-x-3">
                        <div class="text-3xl"><?= $achievement['icon'] ?></div>
                        <div class="flex-1">
                            <h3 class="font-semibold <?= $achievement['earned'] ? 'text-green-800' : 'text-gray-600' ?>">
                                <?= htmlspecialchars($achievement['name']) ?>
                            </h3>
                            <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars($achievement['description']) ?></p>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-semibold text-primary-600"><?= $achievement['points'] ?> points</span>
                                <?php if ($achievement['earned']): ?>
                                    <span class="text-xs bg-green-200 text-green-800 px-2 py-1 rounded">Earned</span>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400">Locked</span>
                                <?php endif; ?>
                            </div>
                            <?php if ($achievement['earned'] && $achievement['awarded_at']): ?>
                                <div class="text-xs text-gray-500 mt-1">
                                    Earned <?= \App\Core\FormatHelper::date($achievement['awarded_at']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

