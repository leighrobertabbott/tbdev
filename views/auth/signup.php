<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-md mx-auto">
    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Sign Up</h1>

        <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/signup" class="space-y-4">
            <div>
                <label for="wantusername" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" id="wantusername" name="wantusername" required 
                       class="input" minlength="3" maxlength="20" 
                       pattern="[a-zA-Z0-9_-]+" autocomplete="username">
                <p class="text-xs text-gray-500 mt-1">3-20 characters, alphanumeric, underscores, or hyphens</p>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" required 
                       class="input" autocomplete="email">
            </div>

            <div>
                <label for="wantpassword" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="wantpassword" name="wantpassword" required 
                       class="input" minlength="8" autocomplete="new-password">
                <p class="text-xs text-gray-500 mt-1">At least 8 characters with uppercase, lowercase, and number</p>
            </div>

            <div>
                <label for="passagain" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" id="passagain" name="passagain" required 
                       class="input" autocomplete="new-password">
            </div>

            <div>
                <label for="user_timezone" class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                <select id="user_timezone" name="user_timezone" class="input">
                    <option value="0">UTC</option>
                    <option value="-5">EST (UTC-5)</option>
                    <option value="-8">PST (UTC-8)</option>
                    <option value="1">CET (UTC+1)</option>
                    <option value="0" selected>GMT (UTC+0)</option>
                </select>
            </div>

            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" name="rulesverify" value="yes" required class="mr-2">
                    <span class="text-sm">I agree to the <a href="/rules" class="text-primary-600 hover:underline">rules</a></span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="faqverify" value="yes" required class="mr-2">
                    <span class="text-sm">I have read the <a href="/faq" class="text-primary-600 hover:underline">FAQ</a></span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="ageverify" value="yes" required class="mr-2">
                    <span class="text-sm">I am 18 years or older</span>
                </label>
            </div>

            <div>
                <button type="submit" class="btn btn-primary w-full">Sign Up</button>
            </div>
        </form>

        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">
                Already have an account? <a href="/login" class="text-primary-600 hover:underline">Login</a>
            </p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>


