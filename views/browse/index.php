<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold mb-2 text-[var(--deep-navy)] font-display">Browse Torrents</h1>
        <p class="text-sm text-gray-600 font-serif italic"><?= date('l, F jS, Y') ?></p>
    </div>
    
    <!-- Category Filter -->
    <div class="card mb-6">
        <form method="GET" action="/browse" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="cat" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="cat" name="cat" class="input" onchange="this.form.submit()">
                    <option value="0" <?= $selectedCategory === 0 ? 'selected' : '' ?>>All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $selectedCategory === $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="incldead" class="block text-sm font-medium text-gray-700 mb-1">Show</label>
                <select id="incldead" name="incldead" class="input" onchange="this.form.submit()">
                    <option value="0" <?= $includeDead === 0 ? 'selected' : '' ?>>Active Only</option>
                    <option value="1" <?= $includeDead === 1 ? 'selected' : '' ?>>Include Dead</option>
                    <?php if (($user['class'] ?? 0) >= 5): ?>
                        <option value="2" <?= $includeDead === 2 ? 'selected' : '' ?>>Dead Only</option>
                    <?php endif; ?>
                </select>
            </div>
            
            <?php if ($selectedCategory > 0): ?>
                <input type="hidden" name="cat" value="<?= $selectedCategory ?>">
            <?php endif; ?>
        </form>
    </div>

    <!-- Torrents List -->
    <div class="space-y-4">
        <?php if (empty($torrents)): ?>
            <div class="card">
                <p class="text-gray-600 text-center py-8 font-serif italic">No torrents found matching your criteria.</p>
            </div>
        <?php else: ?>
            <?php foreach ($torrents as $torrent): ?>
                <div class="torrent-card bg-gray-700 border border-gray-600 rounded-lg p-4 shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="flex gap-4">
                        <!-- Thumbnail -->
                        <div class="flex-shrink-0">
                            <div class="w-32 h-24 bg-gray-600 rounded border border-gray-500 flex flex-col items-center justify-center overflow-hidden">
                                <?php
                                // Try to get category image, or use placeholder
                                $categoryImage = '';
                                if (!empty($torrent['category'])) {
                                    $cat = \App\Core\Database::fetchOne(
                                        "SELECT image FROM categories WHERE id = :id",
                                        ['id' => $torrent['category']]
                                    );
                                    $categoryImage = $cat['image'] ?? '';
                                }
                                ?>
                                <?php if (!empty($categoryImage) && file_exists(__DIR__ .
                                    '/../../public/images/categories/' .
                                    $categoryImage)): ?>
                                    <img src="/images/categories/<?= htmlspecialchars($categoryImage) ?>" 
                                         alt="<?= htmlspecialchars($torrent['category_name'] ?? '') ?>"
                                         class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="text-center p-2">
                                        <div class="text-3xl mb-1">📦</div>
                                        <div class="text-xs text-white font-mono uppercase font-bold">
                                            <?= htmlspecialchars($torrent['category_name'] ?? 'N/A') ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <!-- Title -->
                            <h3 class="text-base font-semibold text-white mb-2.5 line-clamp-1 hover:text-[var(--gold-highlight)] transition-colors">
                                <a href="/torrent/<?= $torrent['id'] ?>">
                                    <?= htmlspecialchars($torrent['name']) ?>
                                </a>
                            </h3>

                            <!-- Technical Specs with Icons -->
                            <div class="flex flex-wrap items-center gap-x-5 gap-y-1.5 text-xs font-mono text-gray-300 mb-2.5">
                                <!-- File Size -->
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path>
                                        <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-blue-400"><?= \App\Core\FormatHelper::bytes($torrent['size'] ?? 0) ?></span>
                                </div>

                                <!-- Seeders -->
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-green-400 font-bold"><?= number_format($torrent['seeders'] ?? 0) ?> SEEDER<?= ($torrent['seeders'] ?? 0) != 1 ? 'S' : '' ?></span>
                                </div>

                                <!-- Leechers -->
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-red-400 font-bold"><?= number_format($torrent['leechers'] ?? 0) ?> LEECHER<?= ($torrent['leechers'] ?? 0) != 1 ? 'S' : '' ?></span>
                                </div>

                                <!-- Times Downloaded -->
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-300"><?= number_format($torrent['times_completed'] ?? 0) ?> TIMES</span>
                                </div>

                                <!-- Views -->
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <span class="text-gray-300"><?= number_format($torrent['views'] ?? 0) ?> VIEWS</span>
                                </div>

                                <!-- Comments -->
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    <span class="text-gray-300"><?= number_format($torrent['comments'] ?? 0) ?> COMMENTS</span>
                                </div>
                            </div>

                            <!-- Meta Info -->
                            <div class="flex items-center gap-2.5 text-xs text-gray-400">
                                <span>by <a href="/user/<?= $torrent['owner'] ?>" class="text-[var(--gold-highlight)] hover:text-yellow-300 hover:underline transition-colors"><?= htmlspecialchars($torrent['owner_name'] ?? 'Unknown') ?></a></span>
                                <span class="text-gray-500">•</span>
                                <span class="text-gray-400"><?= \App\Core\FormatHelper::timeAgo($torrent['added'] ?? 0) ?></span>
                                <span class="text-gray-500">•</span>
                                <span class="px-2 py-0.5 rounded bg-[var(--tracker-red)]/20 text-[var(--tracker-red)] border border-[var(--tracker-red)]/40 text-xs font-semibold uppercase">
                                    <?= htmlspecialchars($torrent['category_name'] ?? 'N/A') ?>
                                </span>
                            </div>
                        </div>

                        <!-- Download Button -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="/download/<?= $torrent['id'] ?>" 
                               class="download-btn group relative flex items-center justify-center w-14 h-14 rounded-lg bg-[var(--tracker-red)] hover:bg-[var(--tracker-red)]/90 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                                <svg class="w-6 h-6 text-white group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="mt-8 flex justify-center items-center space-x-4">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?><?= $selectedCategory > 0 ? '&cat=' .
                    $selectedCategory : '' ?><?= $includeDead > 0 ? '&incldead=' .
                    $includeDead : '' ?>"
                   class="btn btn-secondary">Previous</a>
            <?php endif; ?>
            
            <span class="px-6 py-2 bg-[var(--deep-navy)]/10 text-[var(--deep-navy)] rounded-lg font-mono text-sm border border-[var(--tracker-red)]/20">
                Page <?= $page ?> of <?= $totalPages ?>
            </span>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?><?= $selectedCategory > 0 ? '&cat=' .
                    $selectedCategory : '' ?><?= $includeDead > 0 ? '&incldead=' .
                    $includeDead : '' ?>"
                   class="btn btn-secondary">Next</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Make Format available globally
window.Format = window.Format || {
    bytes: (bytes) => {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    },
    date: (timestamp) => new Date(timestamp * 1000).toLocaleDateString()
};
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

