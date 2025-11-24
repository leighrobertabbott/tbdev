<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold mb-6 text-gray-800 font-display">Forums</h1>

    <?php if (empty($sections)): ?>
        <div class="card">
            <p class="text-gray-600 text-center py-8">No forums available.</p>
        </div>
    <?php else: ?>
        <?php foreach ($sections as $section): ?>
            <!-- Section Header -->
            <div class="mb-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-1 h-8 bg-cyan-500 rounded"></div>
                    <h2 class="text-xl font-bold text-gray-400 uppercase tracking-wide opacity-60"><?= htmlspecialchars($section['name']) ?></h2>
                </div>
                <?php if (!empty($section['description'])): ?>
                    <p class="text-gray-600 text-sm ml-4"><?= htmlspecialchars($section['description']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Forums in Section -->
            <?php foreach ($section['forums'] as $forum): ?>
                <div class="bg-warm-cream rounded-lg mb-3 border border-gray-300 hover:border-gray-400 transition-colors shadow-md">
                    <div class="p-4">
                        <div class="flex gap-4">
                            <!-- Forum Icon/Thumbnail -->
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-gray-700 rounded border border-gray-600 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"></path>
                                        <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Forum Info -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-gray-800 mb-1.5">
                                    <a href="/forum/<?= $forum['id'] ?>" class="hover:text-cyan-600 transition-colors">
                                        <?= htmlspecialchars($forum['name']) ?>
                                    </a>
                                </h3>
                                <p class="text-gray-700 text-sm mb-3 leading-relaxed"><?= htmlspecialchars($forum['description'] ?? '') ?></p>
                                
                                <!-- Statistics -->
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs font-mono font-semibold text-gray-600">
                                    <span><?= number_format($forum['topiccount'] ?? 0) ?> TOPICS</span>
                                    <span><?= number_format($forum['postcount'] ?? 0) ?> POSTS</span>
                                </div>

                                <!-- Last Post Info -->
                                <?php if (!empty($forum['last_post_time'])): ?>
                                    <div class="mt-2 text-xs text-gray-600">
                                        Last post by 
                                        <span class="text-cyan-600 font-semibold"><?= htmlspecialchars($forum['last_post_user'] ?? 'Unknown') ?></span>
                                        <?php if ($forum['last_post_time']): ?>
                                            on <span class="text-gray-600"><?= date('D M jS Y, H:i', $forum['last_post_time']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Subforums -->
                                <?php if (!empty($forum['subforums'])): ?>
                                    <div class="mt-3 pt-3 border-t border-gray-300">
                                        <div class="space-y-2">
                                            <?php foreach ($forum['subforums'] as $subforum): ?>
                                                <div class="ml-4 flex items-start gap-3">
                                                    <div class="flex-1">
                                                        <a href="/forum/<?= $subforum['id'] ?>" class="text-sm text-gray-800 hover:text-cyan-600 font-bold">
                                                            <?= htmlspecialchars($subforum['name']) ?>
                                                        </a>
                                                        <p class="text-xs text-gray-700 mt-0.5 leading-relaxed"><?= htmlspecialchars($subforum['description'] ?? '') ?></p>
                                                        <div class="flex items-center gap-x-3 gap-y-1 mt-1 text-xs font-mono font-semibold text-gray-600">
                                                            <span><?= number_format($subforum['topiccount'] ?? 0) ?> TOPICS</span>
                                                            <span><?= number_format($subforum['postcount'] ?? 0) ?> POSTS</span>
                                                        </div>
                                                        <?php if (!empty($subforum['last_post_time'])): ?>
                                                            <div class="text-xs text-gray-600 mt-1">
                                                                Last: <span class="text-cyan-600 font-semibold"><?= htmlspecialchars($subforum['last_post_user'] ?? 'Unknown') ?></span>
                                                                <?php if ($subforum['last_post_time']): ?>
                                                                    - <?= date('D M jS Y, H:i', $subforum['last_post_time']) ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
