<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="/user/<?= $user['id'] ?>" class="link">← Back to Profile</a>
    </div>

    <div class="card">
        <h1 class="text-3xl font-bold mb-6 font-display">Edit Profile</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-error mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success mb-4">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/profile/update" class="space-y-6">
            <input type="hidden" name="_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">

            <div class="form-group">
                <label for="username" class="form-label">
                    Username
                </label>
                <input type="text" id="username" name="username" 
                       value="<?= htmlspecialchars($user['username'] ?? '') ?>"
                       class="input" disabled>
                <p class="form-help">Username cannot be changed</p>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email" required
                       value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                       class="input">
                <p class="form-help">Your email address for account notifications</p>
            </div>

            <div class="form-group">
                <label for="avatar" class="form-label">
                    Avatar URL
                </label>
                <input type="url" id="avatar" name="avatar"
                       value="<?= htmlspecialchars($user['avatar'] ?? '') ?>"
                       class="input" placeholder="https://example.com/avatar.jpg">
                <p class="form-help">Enter a URL to your avatar image</p>
                <?php if (!empty($user['avatar'])): ?>
                    <div class="mt-2">
                        <img src="<?= htmlspecialchars($user['avatar']) ?>" 
                             alt="Avatar" 
                             class="w-20 h-20 rounded-full object-cover border-2 border-gray-300"
                             onerror="this.style.display='none'">
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="title" class="form-label">
                    Custom Title
                </label>
                <input type="text" id="title" name="title"
                       value="<?= htmlspecialchars($user['title'] ?? '') ?>"
                       class="input" maxlength="30" placeholder="Your custom title">
                <p class="form-help">Optional custom title displayed on your profile</p>
            </div>

            <div class="form-group">
                <label for="info" class="form-label">
                    Profile Information
                </label>
                <textarea id="info" name="info" rows="6"
                          class="input" placeholder="Tell us about yourself..."><?= htmlspecialchars($user['info'] ?? '') ?></textarea>
                <p class="form-help">Optional profile information (supports basic HTML)</p>
            </div>


            <div class="form-group">
                <label for="stylesheet" class="form-label">
                    Stylesheet
                </label>
                <select id="stylesheet" name="stylesheet" class="input">
                    <option value="0" <?= ($user['stylesheet'] ?? 0) == 0 ? 'selected' : '' ?>>Default</option>
                    <!-- Additional stylesheets can be added here -->
                </select>
                <p class="form-help">Choose your preferred site theme</p>
            </div>

            <div class="form-group">
                <label for="timezone" class="form-label">
                    Timezone Offset
                </label>
                <select id="timezone" name="timezone" class="input">
                    <?php
                    $timezones = [
                        '0' => 'UTC (GMT+0)',
                        '-5' => 'EST (GMT-5)',
                        '-8' => 'PST (GMT-8)',
                        '1' => 'CET (GMT+1)',
                        '5' => 'IST (GMT+5)',
                        '9' => 'JST (GMT+9)',
                    ];
                    $currentOffset = $user['time_offset'] ?? '0';
                    foreach ($timezones as $offset => $label):
                    ?>
                        <option value="<?= $offset ?>" <?= $currentOffset === $offset ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="form-help">Your timezone offset for displaying times</p>
            </div>

            <div class="form-group">
                <label for="privacy" class="form-label">
                    Privacy Level
                </label>
                <select id="privacy" name="privacy" class="input">
                    <option value="normal" <?= ($user['privacy'] ?? 'normal') === 'normal' ? 'selected' : '' ?>>Normal</option>
                    <option value="strong" <?= ($user['privacy'] ?? 'normal') === 'strong' ? 'selected' : '' ?>>Strong (Hide from public)</option>
                    <option value="low" <?= ($user['privacy'] ?? 'normal') === 'low' ? 'selected' : '' ?>>Low (More visible)</option>
                </select>
                <p class="form-help">Control who can see your profile information</p>
            </div>

            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="/user/<?= $user['id'] ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>

    <!-- Password Change Section -->
    <div class="card mt-6">
        <h2 class="text-2xl font-bold mb-4 font-display">Change Password</h2>
        <form method="POST" action="/profile/password" class="space-y-4">
            <input type="hidden" name="_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">

            <div class="form-group">
                <label for="current_password" class="form-label">
                    Current Password <span class="text-red-500">*</span>
                </label>
                <input type="password" id="current_password" name="current_password" required
                       class="input">
            </div>

            <div class="form-group">
                <label for="new_password" class="form-label">
                    New Password <span class="text-red-500">*</span>
                </label>
                <input type="password" id="new_password" name="new_password" required
                       class="input" minlength="8">
                <p class="form-help">Minimum 8 characters</p>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">
                    Confirm New Password <span class="text-red-500">*</span>
                </label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       class="input" minlength="8">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary">Change Password</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

