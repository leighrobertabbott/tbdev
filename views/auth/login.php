<?php
// Standalone login page - don't use the main layout
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $siteSettings = \App\Services\SettingsService::getAll();
    $siteName = $siteSettings['site_name'] ?? \App\Core\Config::get('app.name', 'TorrentBits');
    ?>
    <title>Login - <?= htmlspecialchars($siteName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }
        .grain-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.4'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 1;
        }
    </style>
</head>
<body>
    <?php
    // Ensure CSRF token is generated before rendering the form
    $csrfToken = \App\Core\Security::generateCsrfToken();
    ?>
    
    <!-- Simple Header -->
    <header class="fixed top-0 left-0 w-full bg-white/90 backdrop-blur-sm shadow-md border-b-2 z-50" style="border-color: rgba(139, 38, 53, 0.2);">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <a href="/" class="text-2xl font-serif font-bold text-gray-900" style="font-family: 'Playfair Display', serif;">
                    <?= htmlspecialchars($siteName) ?>
                </a>
                <div class="flex items-center gap-4">
                    <a href="/login" 
                       class="px-5 py-2 rounded-lg font-semibold text-white transition-all"
                       style="background-color: #8b2635; font-family: 'Inter', sans-serif;">
                        Login
                    </a>
                    <a href="/signup" 
                       class="px-5 py-2 rounded-lg font-semibold border-2 transition-all text-gray-800 hover:bg-gray-50"
                       style="border-color: #8b2635; font-family: 'Inter', sans-serif;">
                        Sign Up
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="min-h-screen flex items-center justify-center px-4 py-12 pt-24" style="background: linear-gradient(135deg, #f5f1e8 0%, #e8e0d0 100%);">
        <!-- Film Grain Overlay -->
        <div class="grain-overlay"></div>
        
        <div class="w-full max-w-md relative z-10">
            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-2xl border-2 border-gray-200 p-8" style="box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);">
                <!-- Title -->
                <h1 class="text-3xl font-serif font-bold text-gray-900 mb-6 tracking-tight" style="font-family: 'Playfair Display', serif;">
                    Login
                </h1>

                <!-- Error Message -->
                <?php if (isset($error)): ?>
                    <div class="mb-6 p-4 rounded-lg border-2" style="background-color: #8b2635; border-color: #6b1a25;">
                        <p class="text-white text-sm font-medium">
                            <?= htmlspecialchars($error) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" action="/login" class="space-y-5">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    
                    <?php if (isset($returnto)): ?>
                        <input type="hidden" name="returnto" value="<?= htmlspecialchars($returnto) ?>">
                    <?php endif; ?>

                    <!-- Username Field -->
                    <div>
                        <label for="username" class="block text-sm font-semibold text-gray-800 mb-2 font-serif">
                            Username
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            required 
                            autocomplete="username"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-[#8b2635] focus:ring-2 focus:ring-[#8b2635]/20 transition-all bg-white text-gray-900 font-medium"
                            style="font-family: 'Inter', sans-serif;"
                            placeholder="Enter your username"
                        >
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-800 mb-2 font-serif">
                            Password
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-[#8b2635] focus:ring-2 focus:ring-[#8b2635]/20 transition-all bg-white text-gray-900 font-medium"
                            style="font-family: 'Inter', sans-serif;"
                            placeholder="Enter your password"
                        >
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            class="w-full py-3 px-6 rounded-lg font-semibold text-white transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5"
                            style="background-color: #8b2635; font-family: 'Inter', sans-serif;"
                            onmouseover="this.style.backgroundColor='#6b1a25'"
                            onmouseout="this.style.backgroundColor='#8b2635'"
                        >
                            Login
                        </button>
                    </div>
                </form>

                <!-- Links -->
                <div class="mt-6 space-y-3 text-center">
                    <p class="text-sm text-gray-700">
                        Don't have an account? 
                        <a href="/signup" class="text-[#8b2635] font-semibold hover:underline transition-colors">
                            Sign up
                        </a>
                    </p>
                    <p class="text-sm">
                        <a href="/recover" class="text-[#8b2635] font-semibold hover:underline transition-colors">
                            Forgot password?
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
