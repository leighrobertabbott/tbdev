<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $siteSettings = \App\Services\SettingsService::getAll();
    $siteName = $siteSettings['site_name'] ?? \App\Core\Config::get('app.name', 'TorrentBits');
    $siteDescription = $siteSettings['site_description'] ?? ($metaDescription ?? 'Modern BitTorrent Tracker');
    $siteKeywords = $siteSettings['site_keywords'] ?? '';
    $pageTitleFull = ($pageTitle ?? 'Home') . ' - ' . $siteName;
    ?>
    <meta name="description" content="<?= htmlspecialchars($siteDescription) ?>">
    <?php if (!empty($siteKeywords)): ?>
        <meta name="keywords" content="<?= htmlspecialchars($siteKeywords) ?>">
    <?php endif; ?>
    <title><?= htmlspecialchars($pageTitleFull) ?></title>
    <?php if (!empty($siteSettings['site_favicon_url'])): ?>
        <link rel="icon" href="<?= htmlspecialchars($siteSettings['site_favicon_url']) ?>" type="image/x-icon">
    <?php endif; ?>
    <?php if (!empty($siteSettings['meta_og_image'])): ?>
        <meta property="og:image" content="<?= htmlspecialchars($siteSettings['meta_og_image']) ?>">
        <meta property="og:title" content="<?= htmlspecialchars($pageTitleFull) ?>">
        <meta property="og:description" content="<?= htmlspecialchars($siteDescription) ?>">
        <meta property="og:type" content="website">
    <?php endif; ?>
    <?php if (!empty($siteSettings['meta_twitter_card'])): ?>
        <meta name="twitter:card" content="<?= htmlspecialchars($siteSettings['meta_twitter_card']) ?>">
        <?php if (!empty($siteSettings['meta_og_image'])): ?>
            <meta name="twitter:image" content="<?= htmlspecialchars($siteSettings['meta_og_image']) ?>">
        <?php endif; ?>
    <?php endif; ?>
    <link rel="stylesheet" href="/css/app.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?= $additionalHead ?? '' ?>
</head>
<body class="min-h-screen selection:bg-[#8b2635] selection:text-white">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 w-full bg-white/80 backdrop-blur-xl shadow-2xl border-b-2 z-50" style="border-color: rgba(139, 38, 53, 0.2);">
    <!-- Film Grain Overlay -->
    <div class="grain-overlay"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-12">
                    <a href="/" class="flex items-center space-x-3">
                        <?php if (!empty($siteSettings['site_logo_url'])): ?>
                            <img src="<?= htmlspecialchars($siteSettings['site_logo_url']) ?>" 
                                 alt="<?= htmlspecialchars($siteName) ?>" 
                                 class="h-10 object-contain"
                                 onerror="this.style.display='none'">
                        <?php endif; ?>
                        <span class="text-3xl font-display font-bold transition-all duration-300 tracking-tight" style="color: var(--tracker-red);">
                            <?= htmlspecialchars($siteName) ?>
                        </span>
                    </a>
                    <?php if ($user ?? null): ?>
                    <div class="hidden lg:flex items-center space-x-1">
                        <a href="/browse" class="nav-link">Browse</a>
                        <a href="/search" class="nav-link">Search</a>
                        <a href="/upload" class="nav-link">Upload</a>
                        <a href="/forums" class="nav-link">Forums</a>
                        <a href="/polls" class="nav-link">Polls</a>
                        <a href="/topten" class="nav-link">Top 10</a>
                        <?php if (($user['class'] ?? 0) >= 4): ?>
                            <a href="/admin" class="nav-link">Admin</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if ($user ?? null): ?>
                        <a href="/messages" class="relative p-2.5 rounded-lg transition-all duration-300 group" style="color: rgb(55, 65, 81);">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <?php 
                            $unreadCount = 0;
                            if ($user ?? null) {
                                $unreadCount = \App\Models\Message::getUnreadCount($user['id']);
                            }
                            if ($unreadCount > 0): ?>
                                <span class="absolute -top-1 -right-1 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center shadow-lg animate-pulse ring-2 ring-white" style="background: var(--tracker-red);"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="/user/<?= $user['id'] ?>" class="px-4 py-2 font-semibold rounded-lg transition-all duration-300" style="color: var(--deep-navy);">
                            <?= htmlspecialchars($user['username']) ?>
                        </a>
                        <a href="/logout" class="btn btn-secondary btn-sm">Logout</a>
                    <?php else: ?>
                        <a href="/login" class="btn btn-primary btn-sm">Login</a>
                        <a href="/signup" class="btn btn-secondary btn-sm">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 min-h-[calc(100vh-20rem)] pt-28">
        <?php if (isset($flashMessage)): ?>
            <div class="alert alert-info animate-fadeIn mb-6 fade-in-scroll">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-serif italic"><?= htmlspecialchars($flashMessage) ?></span>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="fade-in-scroll">
            <?= $content ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-[var(--deep-navy)] text-white mt-20 border-t-4 border-[var(--tracker-red)] relative overflow-hidden">
        <!-- Decorative element -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-[var(--tracker-red)]/5 rounded-full -mr-48 -mt-48"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-[var(--rust-accent)]/5 rounded-full -ml-32 -mb-32"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-8">
                <div>
                    <h3 class="text-2xl font-display font-bold mb-4 text-[var(--gold-highlight)]"><?= htmlspecialchars($siteName) ?></h3>
                    <p class="text-gray-300 text-sm font-serif italic leading-relaxed"><?= htmlspecialchars($siteSettings['site_tagline'] ?? 'A modern BitTorrent tracker with vintage charm. Built for the community, by the community.') ?></p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-[var(--gold-highlight)] font-display">Quick Links</h4>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li><a href="/faq" class="hover:text-[var(--gold-highlight)] transition-colors duration-300 font-serif">FAQ</a></li>
                        <li><a href="/rules" class="hover:text-[var(--gold-highlight)] transition-colors duration-300 font-serif">Rules</a></li>
                        <li><a href="/staff" class="hover:text-[var(--gold-highlight)] transition-colors duration-300 font-serif">Staff</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-[var(--gold-highlight)] font-display">Connect</h4>
                    <p class="text-sm text-gray-300 font-serif italic mb-4">Stay updated with the latest torrents and community news.</p>
                    <div class="flex space-x-4">
                        <?php if (!empty($siteSettings['social_facebook'])): ?>
                            <a href="<?= htmlspecialchars($siteSettings['social_facebook']) ?>" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-[var(--gold-highlight)] transition-colors duration-200">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879V14.62H8.04V12h2.398V9.995c0-2.365 1.443-3.658 3.543-3.658 1.057 0 1.96.077 2.225.113v2.43h-1.44c-1.136 0-1.357.54-1.357 1.33V12h2.692l-.442 2.62h-2.25V21.88C18.343 21.128 22 16.991 22 12c0-5.523-4.477-10-10-10z"/></svg>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($siteSettings['social_twitter'])): ?>
                            <a href="<?= htmlspecialchars($siteSettings['social_twitter']) ?>" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-[var(--gold-highlight)] transition-colors duration-200">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.37-.83.5-1.75.85-2.72 1.05-.78-.83-1.89-1.35-3.13-1.35-2.37 0-4.3 1.93-4.3 4.3 0 .34.04.67.11.98-3.58-.18-6.75-1.89-8.88-4.48-.37.64-.58 1.38-.58 2.18 0 1.49.76 2.81 1.91 3.59-.7-.02-1.36-.21-1.93-.53v.05c0 2.08 1.48 3.82 3.44 4.22-.36.1-.74.15-1.13.15-.28 0-.55-.03-.81-.08.55 1.71 2.14 2.95 4.02 2.98-1.47 1.15-3.33 1.84-5.36 1.84-.35 0-.69-.02-1.03-.06C4.64 20.13 6.8 21 9.04 21c10.84 0 16.76-8.98 16.76-16.76 0-.26-.01-.52-.02-.78.96-.69 1.79-1.56 2.45-2.55z"/></svg>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($siteSettings['social_discord'])): ?>
                            <a href="<?= htmlspecialchars($siteSettings['social_discord']) ?>" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-[var(--gold-highlight)] transition-colors duration-200">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.295-.444.682-.608 1.001a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.001.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928-1.793 6.4-3.157 8.144-4.01a.076.076 0 0 1 .084.01c1.23.99 2.41 2.04 3.49 3.18a.077.077 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($siteSettings['social_telegram'])): ?>
                            <a href="<?= htmlspecialchars($siteSettings['social_telegram']) ?>" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-[var(--gold-highlight)] transition-colors duration-200">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($siteSettings['social_reddit'])): ?>
                            <a href="<?= htmlspecialchars($siteSettings['social_reddit']) ?>" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-[var(--gold-highlight)] transition-colors duration-200">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm5.01 4.744c.688 0 1.25.561 1.25 1.249a1.25 1.25 0 0 1-2.498.056l-2.597-2.597a1.25 1.25 0 0 1 0-1.768l2.597-2.597a1.25 1.25 0 0 1 1.768 0l2.597 2.597a1.25 1.25 0 0 1-.001 1.768zm-4.476 3.678a3.2 3.2 0 1 1 3.2-3.2 3.2 3.2 0 0 1-3.2 3.2zm-11.023 0a3.2 3.2 0 1 1 3.2-3.2 3.2 3.2 0 0 1-3.2 3.2zm4.723 3.51a.75.75 0 0 0-1.5-.098 7.49 7.49 0 0 0 14.996 0 .75.75 0 1 0-1.5.098 6 6 0 0 1-11.996 0z"/></svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700/50 pt-6 text-center">
                <p class="text-xs text-gray-400 font-mono tracking-wider"><?= htmlspecialchars($siteSettings['site_footer_text'] ?? '© 2025 TorrentBits. All rights reserved.') ?></p>
            </div>
        </div>
    </footer>

    <script>
        // Fade in on scroll
        window.addEventListener('scroll', () => {
            const reveals = document.querySelectorAll('.fade-in-scroll');
            reveals.forEach(element => {
                const windowHeight = window.innerHeight;
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                if (elementTop < windowHeight - elementVisible) {
                    element.classList.add('visible');
                }
            });
        });
        window.dispatchEvent(new Event('scroll'));
    </script>

    <script src="/js/app.js"></script>
</body>
</html>


