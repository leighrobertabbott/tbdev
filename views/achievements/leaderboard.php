<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="/achievements" class="text-primary-600 hover:underline">← Back to My Achievements</a>
    </div>

    <div class="card">
        <h1 class="text-3xl font-bold mb-6">Achievement Leaderboard</h1>

        <?php if (empty($leaderboard)): ?>
            <p class="text-gray-600 text-center py-8">No users on the leaderboard yet.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>User</th>
                            <th>Achievement Points</th>
                            <th>Achievements Earned</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        foreach ($leaderboard as $entry): 
                            $isCurrentUser = ($user['id'] ?? 0) == $entry['id'];
                        ?>
                            <tr class="<?= $isCurrentUser ? 'bg-primary-50' : '' ?>">
                                <td class="font-bold">
                                    <?php if ($rank <= 3): ?>
                                        <span class="text-2xl">
                                            <?= $rank == 1 ? '🥇' : ($rank == 2 ? '🥈' : '🥉') ?>
                                        </span>
                                    <?php else: ?>
                                        #<?= $rank ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="/user/<?= $entry['id'] ?>" class="text-primary-600 hover:underline font-semibold">
                                        <?= htmlspecialchars($entry['username']) ?>
                                        <?php if ($isCurrentUser): ?>
                                            <span class="text-xs text-gray-500">(You)</span>
                                        <?php endif; ?>
                                    </a>
                                </td>
                                <td class="font-semibold text-primary-600"><?= number_format($entry['achievement_points']) ?></td>
                                <td><?= number_format($entry['achievement_count']) ?></td>
                            </tr>
                        <?php 
                            $rank++;
                        endforeach; 
                        ?>
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

