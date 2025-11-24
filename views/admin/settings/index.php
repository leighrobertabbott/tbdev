<?php
$content = ob_get_clean();
ob_start();
?>

<div class="mb-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Site Settings</h1>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                Settings saved successfully!
            </div>
        <?php endif; ?>
    </div>

    <form method="POST" action="/admin/settings" class="space-y-8">
        <input type="hidden" name="_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">

        <!-- Site Information -->
        <div class="card">
            <h2 class="text-2xl font-semibold mb-4">Site Information</h2>
            <div class="space-y-4">
                <div class="form-group">
                    <label for="site_name" class="form-label">Site Name</label>
                    <input type="text" id="site_name" name="site_name" 
                           value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>"
                           class="input" required>
                    <p class="form-help">The name of your site (appears in navigation and page titles)</p>
                </div>

                <div class="form-group">
                    <label for="site_tagline" class="form-label">Site Tagline</label>
                    <input type="text" id="site_tagline" name="site_tagline" 
                           value="<?= htmlspecialchars($settings['site_tagline'] ?? '') ?>"
                           class="input" maxlength="100">
                    <p class="form-help">A short tagline or slogan for your site</p>
                </div>

                <div class="form-group">
                    <label for="site_description" class="form-label">Site Description</label>
                    <textarea id="site_description" name="site_description" rows="3"
                              class="input"><?= htmlspecialchars($settings['site_description'] ?? '') ?></textarea>
                    <p class="form-help">Meta description for search engines (recommended: 150-160 characters)</p>
                </div>

                <div class="form-group">
                    <label for="site_keywords" class="form-label">Site Keywords</label>
                    <input type="text" id="site_keywords" name="site_keywords" 
                           value="<?= htmlspecialchars($settings['site_keywords'] ?? '') ?>"
                           class="input">
                    <p class="form-help">Comma-separated keywords for SEO</p>
                </div>

                <div class="form-group">
                    <label for="site_logo_url" class="form-label">Logo URL</label>
                    <input type="url" id="site_logo_url" name="site_logo_url" 
                           value="<?= htmlspecialchars($settings['site_logo_url'] ?? '') ?>"
                           class="input" placeholder="https://example.com/logo.png">
                    <p class="form-help">URL to your site logo image</p>
                    <?php if (!empty($settings['site_logo_url'])): ?>
                        <div class="mt-2">
                            <img src="<?= htmlspecialchars($settings['site_logo_url']) ?>" 
                                 alt="Logo Preview" 
                                 class="h-16 object-contain"
                                 onerror="this.style.display='none'">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="site_favicon_url" class="form-label">Favicon URL</label>
                    <input type="url" id="site_favicon_url" name="site_favicon_url" 
                           value="<?= htmlspecialchars($settings['site_favicon_url'] ?? '') ?>"
                           class="input" placeholder="https://example.com/favicon.ico">
                    <p class="form-help">URL to your favicon (16x16 or 32x32 pixels recommended)</p>
                </div>

                <div class="form-group">
                    <label for="site_footer_text" class="form-label">Footer Text</label>
                    <textarea id="site_footer_text" name="site_footer_text" rows="2"
                              class="input"><?= htmlspecialchars($settings['site_footer_text'] ?? '') ?></textarea>
                    <p class="form-help">Text displayed in the site footer</p>
                </div>
            </div>
        </div>

        <!-- Theme Colors -->
        <div class="card">
            <h2 class="text-2xl font-semibold mb-4">Theme Colors</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="form-group">
                    <label for="theme_primary_color" class="form-label">Primary Color</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" id="theme_primary_color" name="theme_primary_color" 
                               value="<?= htmlspecialchars($settings['theme_primary_color'] ?? '#8b2635') ?>"
                               class="h-10 w-20 rounded border">
                        <input type="text" 
                               value="<?= htmlspecialchars($settings['theme_primary_color'] ?? '#8b2635') ?>"
                               class="input flex-1" 
                               onchange="document.getElementById('theme_primary_color').value = this.value">
                    </div>
                    <p class="form-help">Main brand color</p>
                </div>

                <div class="form-group">
                    <label for="theme_secondary_color" class="form-label">Secondary Color</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" id="theme_secondary_color" name="theme_secondary_color" 
                               value="<?= htmlspecialchars($settings['theme_secondary_color'] ?? '#1a2332') ?>"
                               class="h-10 w-20 rounded border">
                        <input type="text" 
                               value="<?= htmlspecialchars($settings['theme_secondary_color'] ?? '#1a2332') ?>"
                               class="input flex-1"
                               onchange="document.getElementById('theme_secondary_color').value = this.value">
                    </div>
                    <p class="form-help">Secondary brand color</p>
                </div>

                <div class="form-group">
                    <label for="theme_accent_color" class="form-label">Accent Color</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" id="theme_accent_color" name="theme_accent_color" 
                               value="<?= htmlspecialchars($settings['theme_accent_color'] ?? '#d4af37') ?>"
                               class="h-10 w-20 rounded border">
                        <input type="text" 
                               value="<?= htmlspecialchars($settings['theme_accent_color'] ?? '#d4af37') ?>"
                               class="input flex-1"
                               onchange="document.getElementById('theme_accent_color').value = this.value">
                    </div>
                    <p class="form-help">Accent/highlight color</p>
                </div>
            </div>
        </div>

        <!-- Social Media -->
        <div class="card">
            <h2 class="text-2xl font-semibold mb-4">Social Media Links</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="social_facebook" class="form-label">Facebook</label>
                    <input type="url" id="social_facebook" name="social_facebook" 
                           value="<?= htmlspecialchars($settings['social_facebook'] ?? '') ?>"
                           class="input" placeholder="https://facebook.com/yourpage">
                </div>

                <div class="form-group">
                    <label for="social_twitter" class="form-label">Twitter/X</label>
                    <input type="url" id="social_twitter" name="social_twitter" 
                           value="<?= htmlspecialchars($settings['social_twitter'] ?? '') ?>"
                           class="input" placeholder="https://twitter.com/yourhandle">
                </div>

                <div class="form-group">
                    <label for="social_discord" class="form-label">Discord</label>
                    <input type="url" id="social_discord" name="social_discord" 
                           value="<?= htmlspecialchars($settings['social_discord'] ?? '') ?>"
                           class="input" placeholder="https://discord.gg/yourserver">
                </div>

                <div class="form-group">
                    <label for="social_telegram" class="form-label">Telegram</label>
                    <input type="url" id="social_telegram" name="social_telegram" 
                           value="<?= htmlspecialchars($settings['social_telegram'] ?? '') ?>"
                           class="input" placeholder="https://t.me/yourchannel">
                </div>

                <div class="form-group">
                    <label for="social_reddit" class="form-label">Reddit</label>
                    <input type="url" id="social_reddit" name="social_reddit" 
                           value="<?= htmlspecialchars($settings['social_reddit'] ?? '') ?>"
                           class="input" placeholder="https://reddit.com/r/yoursubreddit">
                </div>
            </div>
        </div>

        <!-- Meta Tags & SEO -->
        <div class="card">
            <h2 class="text-2xl font-semibold mb-4">Meta Tags & SEO</h2>
            <div class="space-y-4">
                <div class="form-group">
                    <label for="meta_og_image" class="form-label">Open Graph Image</label>
                    <input type="url" id="meta_og_image" name="meta_og_image" 
                           value="<?= htmlspecialchars($settings['meta_og_image'] ?? '') ?>"
                           class="input" placeholder="https://example.com/og-image.png">
                    <p class="form-help">Image shown when sharing on social media (recommended: 1200x630 pixels)</p>
                </div>

                <div class="form-group">
                    <label for="meta_twitter_card" class="form-label">Twitter Card Type</label>
                    <select id="meta_twitter_card" name="meta_twitter_card" class="input">
                        <option value="summary" <?= ($settings['meta_twitter_card'] ?? 'summary_large_image') === 'summary' ? 'selected' : '' ?>>Summary</option>
                        <option value="summary_large_image" <?= ($settings['meta_twitter_card'] ?? 'summary_large_image') === 'summary_large_image' ? 'selected' : '' ?>>Summary with Large Image</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card">
            <h2 class="text-2xl font-semibold mb-4">Contact Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="site_email" class="form-label">Site Email</label>
                    <input type="email" id="site_email" name="site_email" 
                           value="<?= htmlspecialchars($settings['site_email'] ?? '') ?>"
                           class="input" placeholder="noreply@example.com">
                    <p class="form-help">Email address for system notifications</p>
                </div>

                <div class="form-group">
                    <label for="site_contact_email" class="form-label">Contact Email</label>
                    <input type="email" id="site_contact_email" name="site_contact_email" 
                           value="<?= htmlspecialchars($settings['site_contact_email'] ?? '') ?>"
                           class="input" placeholder="contact@example.com">
                    <p class="form-help">Email address for user inquiries</p>
                </div>
            </div>
        </div>

        <!-- Maintenance Mode -->
        <div class="card">
            <h2 class="text-2xl font-semibold mb-4">Maintenance Mode</h2>
            <div class="space-y-4">
                <div class="form-group">
                    <label class="flex items-center">
                        <input type="checkbox" name="site_maintenance_mode" value="1" 
                               <?= ($settings['site_maintenance_mode'] ?? '0') === '1' ? 'checked' : '' ?>
                               class="mr-2 h-4 w-4">
                        <span class="font-semibold">Enable Maintenance Mode</span>
                    </label>
                    <p class="form-help">When enabled, only administrators can access the site</p>
                </div>

                <div class="form-group">
                    <label for="site_maintenance_message" class="form-label">Maintenance Message</label>
                    <textarea id="site_maintenance_message" name="site_maintenance_message" rows="3"
                              class="input"><?= htmlspecialchars($settings['site_maintenance_message'] ?? '') ?></textarea>
                    <p class="form-help">Message displayed to users during maintenance</p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
            <a href="/admin" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>

