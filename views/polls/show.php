<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="/polls" class="text-primary-600 hover:underline">← Back to Polls</a>
    </div>

    <div class="card mb-6">
        <div class="flex justify-between items-start mb-4">
            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-2"><?= htmlspecialchars($poll['question']) ?></h1>
                <?php if (!empty($poll['description'])): ?>
                    <p class="text-gray-600 mb-4"><?= htmlspecialchars($poll['description']) ?></p>
                <?php endif; ?>
                <div class="flex items-center space-x-4 text-sm text-gray-500">
                    <span>Created by <a href="/user/<?= $poll['created_by'] ?>" class="text-primary-600 hover:underline"><?= htmlspecialchars($poll['creator_name'] ?? 'Unknown') ?></a></span>
                    <span>•</span>
                    <span><?= \App\Core\FormatHelper::timeAgo($poll['created_at']) ?></span>
                    <span>•</span>
                    <span><?= number_format($poll['total_votes']) ?> total votes</span>
                    <?php if ($poll['expires_at']): ?>
                        <span>•</span>
                        <span class="<?= $isExpired ? 'text-red-600' : '' ?>">
                            <?= $isExpired ? 'Expired' : 'Expires' ?>: <?= \App\Core\FormatHelper::date($poll['expires_at']) ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (($user['class'] ?? 0) >= 4): ?>
                <div class="flex space-x-2">
                    <?php if ($poll['status'] === 'active'): ?>
                        <a href="/poll/<?= $poll['id'] ?>/close" class="btn btn-secondary text-sm">Close</a>
                    <?php endif; ?>
                    <a href="/poll/<?= $poll['id'] ?>/delete" class="btn btn-secondary text-sm" 
                       onclick="return confirm('Delete this poll?')">Delete</a>
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['voted'])): ?>
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                Your vote has been recorded!
            </div>
        <?php endif; ?>

        <!-- Voting Form -->
        <?php if ($canVote && !$showResults): ?>
            <form method="POST" action="/poll/<?= $poll['id'] ?>/vote" class="space-y-4">
                <input type="hidden" name="_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">
                <div class="space-y-3">
                    <?php foreach ($options as $option): ?>
                        <label class="flex items-center p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="<?= $poll['allow_multiple'] ? 'checkbox' : 'radio' ?>" 
                                   name="options[]" 
                                   value="<?= $option['id'] ?>" 
                                   class="mr-3 h-4 w-4 text-primary-600 focus:ring-primary-500">
                            <span class="flex-1"><?= htmlspecialchars($option['option_text']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary">Submit Vote</button>
                </div>
            </form>
        <?php endif; ?>

        <!-- Results -->
        <?php if ($showResults): ?>
            <div class="mt-6 pt-6 border-t">
                <h2 class="text-xl font-semibold mb-4">Results</h2>
                <div class="space-y-4">
                    <?php foreach ($results as $result): ?>
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium"><?= htmlspecialchars($result['option_text']) ?></span>
                                <span class="text-sm text-gray-600">
                                    <?= number_format($result['vote_count']) ?> votes 
                                    (<?= $result['percentage'] ?>%)
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-primary-600 h-4 rounded-full transition-all duration-300" 
                                     style="width: <?= $result['percentage'] ?>%">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if ($hasVoted && $poll['allow_change_vote'] && !$isExpired): ?>
                    <div class="mt-6 pt-6 border-t">
                        <a href="/poll/<?= $poll['id'] ?>" class="btn btn-secondary">Change Vote</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Poll Settings Info -->
        <div class="mt-6 pt-6 border-t text-sm text-gray-600">
            <div class="flex flex-wrap gap-4">
                <?php if ($poll['allow_multiple']): ?>
                    <span>✓ Multiple votes allowed</span>
                <?php endif; ?>
                <?php if ($poll['allow_change_vote']): ?>
                    <span>✓ Votes can be changed</span>
                <?php endif; ?>
                <?php if ($poll['show_results_before_vote']): ?>
                    <span>✓ Results visible before voting</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

