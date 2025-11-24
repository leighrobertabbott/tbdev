<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Step 1: System Requirements - TorrentBits</title>
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
                        Step 1: System Requirements
                    </h1>
                    <p class="text-gray-700 font-medium">Checking your server environment...</p>
                </div>

                <div class="space-y-4 mb-8">
                    <?php foreach ($requirements as $key => $req): ?>
                        <div class="flex items-center justify-between p-4 border-2 rounded-lg <?= $req['met'] ? 'bg-green-50 border-green-300' : 'bg-red-50 border-red-300' ?>">
                            <div class="flex items-center space-x-3">
                                <?php if ($req['met']): ?>
                                    <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                <?php else: ?>
                                    <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                <?php endif; ?>
                                <div>
                                    <div class="font-semibold text-gray-900"><?= htmlspecialchars($req['required']) ?></div>
                                    <div class="text-sm text-gray-700"><?= htmlspecialchars($req['current']) ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (isset($directories)): ?>
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4 font-serif text-gray-900">Directory Creation</h3>
                        <div class="space-y-2">
                            <?php foreach ($directories as $dir => $created): ?>
                                <div class="flex items-center space-x-2 text-sm">
                                    <?php if ($created): ?>
                                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-gray-800"><?= htmlspecialchars($dir) ?>/ directory ready</span>
                                    <?php else: ?>
                                        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span class="text-red-800 font-medium">Failed to create <?= htmlspecialchars($dir) ?>/ directory</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="flex justify-between items-center pt-6 border-t-2 border-gray-300">
                    <a href="/installer" 
                       class="px-6 py-3 rounded-lg font-semibold border-2 transition-all text-gray-800 hover:bg-gray-50"
                       style="border-color: #8b2635; font-family: 'Inter', sans-serif;">
                        ← Back
                    </a>
                    <?php if ($allMet): ?>
                        <a href="/installer/step2" 
                           class="px-6 py-3 rounded-lg font-semibold text-white transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5"
                           style="background-color: #8b2635; font-family: 'Inter', sans-serif;"
                           onmouseover="this.style.backgroundColor='#6b1a25'"
                           onmouseout="this.style.backgroundColor='#8b2635'">
                            Continue →
                        </a>
                    <?php else: ?>
                        <button disabled class="px-6 py-3 rounded-lg font-semibold text-gray-400 border-2 border-gray-300 cursor-not-allowed opacity-50">
                            Please fix requirements first
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
