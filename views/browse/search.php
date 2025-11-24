<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Search Results</h1>
        <a href="/browse" class="btn btn-secondary">Back to Browse</a>
    </div>

    <!-- Search Form -->
    <div class="card mb-6">
        <form method="GET" action="/search" class="flex gap-4">
            <div class="flex-1">
                <input type="text" 
                       name="q" 
                       value="<?= htmlspecialchars($query ?? '') ?>" 
                       placeholder="Search torrents..." 
                       class="input"
                       required>
            </div>
            <div>
                <select name="cat" class="input">
                    <option value="0">All Categories</option>
                    <?php foreach ($categories ?? [] as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (($selectedCategory ?? 0) == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

    <!-- Results -->
    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php elseif (empty($torrents)): ?>
        <div class="card text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <h2 class="text-2xl font-semibold text-gray-700 mb-2">No Results Found</h2>
            <p class="text-gray-500 mb-6">
                <?php if (!empty($query)): ?>
                    No torrents found matching "<?= htmlspecialchars($query) ?>"
                <?php else: ?>
                    Please enter a search query
                <?php endif; ?>
            </p>
            <a href="/browse" class="btn btn-primary">Browse All Torrents</a>
        </div>
    <?php else: ?>
        <div class="mb-4 text-sm text-gray-600">
            Found <strong><?= number_format($total ?? count($torrents)) ?></strong> result<?= ($total ?? count($torrents)) != 1 ? 's' : '' ?>
            <?php if (!empty($query)): ?>
                for "<strong><?= htmlspecialchars($query) ?></strong>"
            <?php endif; ?>
        </div>

        <div class="space-y-4">
            <?php foreach ($torrents as $torrent): ?>
                <div class="card hover:shadow-xl transition-all duration-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h2 class="text-xl font-semibold mb-2">
                                <a href="/torrent/<?= $torrent['id'] ?>" class="text-primary-600 hover:text-primary-700 hover:underline">
                                    <?= htmlspecialchars($torrent['name']) ?>
                                </a>
                            </h2>
                            
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-3">
                                <span class="badge badge-primary"><?= htmlspecialchars($torrent['category_name'] ?? 'Uncategorized') ?></span>
                                <span><?= \App\Core\FormatHelper::bytes($torrent['size']) ?></span>
                                <span>Uploaded by <a href="/user/<?= $torrent['owner'] ?>" class="link"><?= htmlspecialchars($torrent['owner_name'] ?? 'Unknown') ?></a></span>
                                <span><?= \App\Core\FormatHelper::timeAgo($torrent['added']) ?></span>
                            </div>

                            <?php if (!empty($torrent['descr'])): ?>
                                <p class="text-gray-700 line-clamp-2">
                                    <?= htmlspecialchars(substr($torrent['descr'], 0, 200)) ?><?= strlen($torrent['descr']) > 200 ? '...' : '' ?>
                                </p>
                            <?php endif; ?>

                            <div class="flex items-center gap-6 mt-4 text-sm">
                                <span class="flex items-center text-green-600">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <?= number_format($torrent['seeders'] ?? 0) ?> seeders
                                </span>
                                <span class="flex items-center text-orange-600">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <?= number_format($torrent['leechers'] ?? 0) ?> leechers
                                </span>
                                <span class="flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <?= number_format($torrent['times_completed'] ?? 0) ?> completed
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <a href="/download/<?= $torrent['id'] ?>" class="btn btn-primary btn-sm">
                                Download
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
            <div class="mt-8 flex justify-center items-center space-x-2">
                <?php if ($page > 1): ?>
                    <a href="?q=<?= urlencode($query ?? '') ?>&cat=<?= $selectedCategory ?? 0 ?>&page=<?= $page - 1 ?>" 
                       class="btn btn-secondary btn-sm">Previous</a>
                <?php endif; ?>
                
                <span class="text-gray-600">
                    Page <?= $page ?> of <?= $totalPages ?>
                </span>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?q=<?= urlencode($query ?? '') ?>&cat=<?= $selectedCategory ?? 0 ?>&page=<?= $page + 1 ?>" 
                       class="btn btn-secondary btn-sm">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

