<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="card">
        <h1 class="text-3xl font-bold mb-6"><?= htmlspecialchars(\App\Core\Config::get('app.name', 'TorrentBits')) ?> User Agreement</h1>
        
        <div class="prose max-w-none space-y-6">
            <section>
                <h2 class="text-2xl font-semibold mb-3">Terms of Service</h2>
                <p class="text-gray-700">
                    By accessing and using <?= htmlspecialchars(\App\Core\Config::get('app.name', 'TorrentBits')) ?>, you agree to be bound by the following terms and conditions.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold mb-3">1. Account Responsibility</h2>
                <p class="text-gray-700 mb-2">You are responsible for:</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                    <li>Maintaining the security of your account credentials</li>
                    <li>All activities that occur under your account</li>
                    <li>Keeping your account information up to date</li>
                    <li>Not sharing your account with others</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold mb-3">2. Upload Rules</h2>
                <p class="text-gray-700 mb-2">When uploading content, you agree to:</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                    <li>Only upload content you have the legal right to share</li>
                    <li>Provide accurate descriptions and information</li>
                    <li>Use appropriate categories</li>
                    <li>Seed your uploads to maintain good ratios</li>
                    <li>Not upload illegal, copyrighted, or harmful content</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold mb-3">3. Ratio Requirements</h2>
                <p class="text-gray-700">
                    Users are expected to maintain a minimum upload/download ratio. Failure to maintain an adequate ratio may result in account restrictions or termination.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold mb-3">4. Prohibited Activities</h2>
                <p class="text-gray-700 mb-2">The following activities are strictly prohibited:</p>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                    <li>Uploading or sharing illegal content</li>
                    <li>Violating copyright laws</li>
                    <li>Spamming, trolling, or harassing other users</li>
                    <li>Attempting to hack, exploit, or damage the system</li>
                    <li>Creating multiple accounts</li>
                    <li>Cheating or manipulating statistics</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold mb-3">5. Privacy</h2>
                <p class="text-gray-700">
                    We respect your privacy and will not share your personal information with third parties except as required by law. Your IP address and activity may be logged for security and moderation purposes.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold mb-3">6. Content Disclaimer</h2>
                <p class="text-gray-700">
                    <?= htmlspecialchars(\App\Core\Config::get('app.name', 'TorrentBits')) ?> is a file sharing platform. We do not host or control the content shared by users. We are not responsible for the content, quality, or legality of files shared through our platform.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold mb-3">7. Account Termination</h2>
                <p class="text-gray-700">
                    We reserve the right to suspend or terminate accounts that violate these terms, engage in prohibited activities, or for any other reason we deem necessary to maintain the integrity of the platform.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold mb-3">8. Changes to Terms</h2>
                <p class="text-gray-700">
                    We reserve the right to modify these terms at any time. Continued use of the service after changes constitutes acceptance of the new terms.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold mb-3">9. Limitation of Liability</h2>
                <p class="text-gray-700">
                    <?= htmlspecialchars(\App\Core\Config::get('app.name', 'TorrentBits')) ?> and its operators are not liable for any damages, losses, or issues arising from the use of this service.
                </p>
            </section>

            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-gray-700">
                    <strong>By using this service, you acknowledge that you have read, understood, and agree to be bound by these terms and conditions.</strong>
                </p>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

