<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Wizard - TorrentBits</title>
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
                <div class="text-center mb-8">
                    <h1 class="text-4xl md:text-5xl font-serif font-bold text-gray-900 mb-4 tracking-tight" style="font-family: 'Playfair Display', serif;">
                        Welcome to TorrentBits 2025
                    </h1>
                    <p class="text-xl text-gray-700 font-medium">Installation Wizard</p>
                </div>

                <div class="bg-cyan-50 border-2 border-cyan-200 rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3 font-serif">Before You Begin</h2>
                    <ul class="list-disc list-inside text-gray-800 space-y-2 text-sm">
                        <li>Make sure you have PHP 8.2+ installed</li>
                        <li>MySQL 8.0+ database server running</li>
                        <li>Required PHP extensions enabled (PDO, MySQL, mbstring, OpenSSL, JSON)</li>
                        <li>Write permissions for <code class="bg-cyan-100 px-2 py-0.5 rounded font-mono text-xs">torrents/</code>, <code class="bg-cyan-100 px-2 py-0.5 rounded font-mono text-xs">cache/</code>, and <code class="bg-cyan-100 px-2 py-0.5 rounded font-mono text-xs">logs/</code> directories</li>
                    </ul>
                </div>

                <div class="bg-warm-cream border-2 border-gray-300 rounded-lg p-6 mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 font-serif">Installation Steps</h2>
                    <ol class="list-decimal list-inside space-y-3 text-gray-800">
                        <li><strong class="text-gray-900">System Requirements</strong> - Check PHP version, extensions, and permissions</li>
                        <li><strong class="text-gray-900">Database Configuration</strong> - Enter database credentials and import schema</li>
                        <li><strong class="text-gray-900">Application Settings</strong> - Configure site name, URL, and email settings</li>
                        <li><strong class="text-gray-900">Admin Account</strong> - Create your first administrator account</li>
                    </ol>
                </div>

                <div class="text-center">
                    <a href="/installer/step1" 
                       class="inline-block px-8 py-4 rounded-lg font-semibold text-white transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5 text-lg"
                       style="background-color: #8b2635; font-family: 'Inter', sans-serif;"
                       onmouseover="this.style.backgroundColor='#6b1a25'"
                       onmouseout="this.style.backgroundColor='#8b2635'">
                        Start Installation →
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
