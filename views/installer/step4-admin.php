<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Step 4: Create Admin Account - TorrentBits</title>
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
    <div class="min-h-screen px-4 py-12" style="background: linear-gradient(135deg, #f5f1e8 0%, #e8e0d0 100%);">
        <!-- Film Grain Overlay -->
        <div class="grain-overlay"></div>
        
        <div class="max-w-4xl mx-auto relative z-10">
            <div class="bg-white rounded-2xl shadow-2xl border-2 border-gray-200 p-8 md:p-12" style="box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);">
                <div class="mb-8">
                    <h1 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 mb-2 tracking-tight" style="font-family: 'Playfair Display', serif;">
                        Step 4: Create Admin Account
                    </h1>
                    <p class="text-gray-700 font-medium">Create your first administrator account</p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="mb-6 p-4 rounded-lg border-2" style="background-color: #8b2635; border-color: #6b1a25;">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-white font-semibold">Error: <?= htmlspecialchars($error) ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="/installer/step4" class="space-y-6">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Security::generateCsrfToken()) ?>">
                    
                    <div>
                        <label for="username" class="block text-sm font-semibold text-gray-800 mb-2 font-serif">
                            Username
                        </label>
                        <input type="text" id="username" name="username" 
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-[#8b2635] focus:ring-2 focus:ring-[#8b2635]/20 transition-all bg-white text-gray-900 font-medium"
                               required minlength="3" maxlength="20">
                        <p class="text-sm text-gray-600 mt-1">3-20 alphanumeric characters, underscores, or hyphens</p>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-800 mb-2 font-serif">
                            Email Address
                        </label>
                        <input type="email" id="email" name="email" 
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-[#8b2635] focus:ring-2 focus:ring-[#8b2635]/20 transition-all bg-white text-gray-900 font-medium"
                               required>
                        <p class="text-sm text-gray-600 mt-1">Used for password recovery and notifications</p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-800 mb-2 font-serif">
                            Password
                        </label>
                        <input type="password" id="password" name="password" 
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-[#8b2635] focus:ring-2 focus:ring-[#8b2635]/20 transition-all bg-white text-gray-900 font-medium"
                               required minlength="8">
                        <p class="text-sm text-gray-600 mt-1">Minimum 8 characters</p>
                    </div>

                    <div>
                        <label for="password_confirm" class="block text-sm font-semibold text-gray-800 mb-2 font-serif">
                            Confirm Password
                        </label>
                        <input type="password" id="password_confirm" name="password_confirm" 
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-[#8b2635] focus:ring-2 focus:ring-[#8b2635]/20 transition-all bg-white text-gray-900 font-medium"
                               required minlength="8">
                    </div>

                    <div class="bg-cyan-50 border-2 border-cyan-200 rounded-lg p-4">
                        <p class="text-sm text-gray-800">
                            <strong>Important:</strong> This account will have full administrator (Sysop) privileges. Make sure to use a strong password and keep it secure.
                        </p>
                    </div>

                    <div class="flex justify-between items-center pt-6 border-t-2 border-gray-300">
                        <a href="/installer/step3" 
                           class="px-6 py-3 rounded-lg font-semibold border-2 transition-all text-gray-800 hover:bg-gray-50"
                           style="border-color: #8b2635; font-family: 'Inter', sans-serif;">
                            ← Back
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 rounded-lg font-semibold text-white transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5"
                                style="background-color: #8b2635; font-family: 'Inter', sans-serif;"
                                onmouseover="this.style.backgroundColor='#6b1a25'"
                                onmouseout="this.style.backgroundColor='#8b2635'">
                            Complete Installation →
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
