<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="/admin/bans" class="text-primary-600 hover:underline">← Back to Bans</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Add IP Ban</h1>

        <form method="POST" action="/admin/bans/create" class="space-y-4">
            <div>
                <label for="first" class="block text-sm font-medium text-gray-700 mb-1">
                    Start IP <span class="text-red-500">*</span>
                </label>
                <input type="text" id="first" name="first" required 
                       class="input" placeholder="192.168.1.1">
            </div>

            <div>
                <label for="last" class="block text-sm font-medium text-gray-700 mb-1">
                    End IP <span class="text-red-500">*</span>
                </label>
                <input type="text" id="last" name="last" required 
                       class="input" placeholder="192.168.1.255">
                <p class="text-xs text-gray-500 mt-1">For single IP, use the same IP for both fields</p>
            </div>

            <div>
                <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">
                    Comment
                </label>
                <textarea id="comment" name="comment" rows="3" 
                          class="input" placeholder="Reason for ban"></textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/admin/bans" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Ban</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>

