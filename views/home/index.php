<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <!-- Top Section: Latest News and Recent News Items -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- LATEST NEWS (Left) -->
        <div class="space-y-4">
            <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-wide mb-4">LATEST NEWS</h2>
            
            <?php if ($latestNews): ?>
                <div class="bg-warm-cream border-2 border-gray-300 rounded-lg p-6 shadow-lg">
                    <?php if (!empty($latestNews['headline'])): ?>
                        <h3 class="text-lg font-bold text-cyan-600 uppercase mb-3">
                            <?= htmlspecialchars($latestNews['headline']) ?>
                        </h3>
                    <?php endif; ?>
                    
                    <div class="text-gray-800 text-sm leading-relaxed mb-4">
                        <?php
                        $body = htmlspecialchars($latestNews['body'] ?? '');
                        // Truncate to ~200 characters for preview
                        if (strlen($body) > 200) {
                            $body = substr($body, 0, 200) . '...';
                        }
                        echo nl2br($body);
                        ?>
                    </div>
                    
                    <a href="/news/<?= $latestNews['id'] ?>" 
                       class="inline-block px-6 py-2 bg-cyan-600 text-white font-semibold rounded-lg hover:bg-cyan-700 transition-colors text-sm uppercase tracking-wide">
                        READ MORE
                    </a>
                    
                    <div class="mt-4 pt-4 border-t border-gray-300 text-xs text-gray-600 font-mono">
                        <?= strtoupper(date('l F jS, Y', $latestNews['added'])) ?> 
                        <?= htmlspecialchars($latestNews['username'] ?? 'Unknown') ?> 
                        0 REPLIES
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-warm-cream border-2 border-gray-300 rounded-lg p-6 shadow-lg">
                    <p class="text-gray-600 text-center py-8">No news available.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- RECENT NEWS ITEMS (Right) -->
        <div class="space-y-4">
            <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-wide mb-4">RECENT NEWS ITEMS</h2>
            
            <?php if (empty($recentNews)): ?>
                <div class="bg-warm-cream border-2 border-gray-300 rounded-lg p-6 shadow-lg">
                    <p class="text-gray-600 text-center py-8">No recent news items.</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($recentNews as $item): ?>
                        <div class="bg-warm-cream border-2 border-gray-300 rounded-lg p-4 shadow-md hover:shadow-lg transition-shadow">
                            <h3 class="text-sm font-bold text-gray-800 uppercase mb-2">
                                <a href="/news/<?= $item['id'] ?>" class="hover:text-cyan-600 transition-colors">
                                    <?= htmlspecialchars($item['headline']) ?>
                                </a>
                            </h3>
                            <div class="text-xs text-gray-600 font-mono">
                                <span class="text-gray-800"><?= htmlspecialchars($item['username'] ?? 'Unknown') ?></span>
                                <span class="text-cyan-600"> <?= strtoupper(date('M j, Y', $item['added'])) ?></span>
                                <span class="text-gray-600"> 0 REPLIES</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- RECENT ACTIVE FORUM THREADS (Full Width) -->
    <div class="mt-8">
        <div class="flex items-center gap-4 mb-6 border-b-2 border-gray-300">
            <h2 class="text-2xl font-bold text-gray-800 uppercase tracking-wide pb-2 border-b-4 border-cyan-600">
                RECENT ACTIVE FORUM THREADS
            </h2>
            <h2 class="text-xl font-bold text-gray-400 uppercase tracking-wide pb-2 cursor-pointer hover:text-gray-600 transition-colors">
                MOST RECENT THREADS
            </h2>
        </div>
        
        <?php if (empty($recentThreads)): ?>
            <div class="bg-warm-cream border-2 border-gray-300 rounded-lg p-8 shadow-lg text-center">
                <p class="text-gray-600">No active forum threads yet.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($recentThreads as $thread): ?>
                    <div class="bg-cyan-50 border-2 border-gray-300 rounded-lg p-4 shadow-md hover:shadow-lg transition-all hover:border-cyan-400 relative group">
                        <!-- Arrow icon -->
                        <div class="absolute top-4 right-4 text-cyan-600 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        
                        <a href="/topic/<?= $thread['id'] ?>" class="block">
                            <!-- Author Info -->
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                    <?php if (!empty($thread['author_avatar'])): ?>
                                        <img src="<?= htmlspecialchars($thread['author_avatar']) ?>" 
                                             alt="<?= htmlspecialchars($thread['author_name'] ?? '') ?>"
                                             class="w-full h-full rounded-full object-cover">
                                    <?php else: ?>
                                        <?= strtoupper(substr($thread['author_name'] ?? 'U', 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-bold text-gray-800">
                                        <?= htmlspecialchars($thread['author_name'] ?? 'Unknown') ?>
                                    </div>
                                    <div class="text-xs text-gray-600 font-mono">
                                        <?= strtoupper(date('D M jS Y, H:i', $thread['lastpost'] ?? $thread['added'] ?? time())) ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Thread Title -->
                            <h3 class="text-sm font-bold text-gray-800 mb-3 line-clamp-2 leading-tight">
                                <?= htmlspecialchars($thread['subject'] ?? 'No Subject') ?>
                            </h3>
                            
                            <!-- Stats -->
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-mono text-gray-600">
                                <span><?= number_format($thread['views'] ?? 0) ?> VIEWS</span>
                                <span><?= number_format($thread['reply_count'] ?? 0) ?> REPLIES</span>
                                <?php if (!empty($thread['last_post_by'])): ?>
                                    <span>LP BY <span class="text-cyan-600 font-semibold"><?= htmlspecialchars($thread['last_post_by']) ?></span></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
