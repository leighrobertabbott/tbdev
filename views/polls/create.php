<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="/polls" class="text-primary-600 hover:underline">← Back to Polls</a>
    </div>

    <div class="card">
        <h1 class="text-2xl font-bold mb-6">Create Poll</h1>

        <?php if (isset($error)): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/polls/create" class="space-y-6" id="pollForm">
            <input type="hidden" name="_token" value="<?= \App\Core\Security::generateCsrfToken() ?>">
            <div>
                <label for="question" class="block text-sm font-medium text-gray-700 mb-1">
                    Question <span class="text-red-500">*</span>
                </label>
                <input type="text" id="question" name="question" required 
                       class="input" maxlength="255" placeholder="Enter your poll question">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea id="description" name="description" rows="3" 
                          class="input" placeholder="Optional description or context"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Options <span class="text-red-500">*</span> (Minimum 2 required)
                </label>
                <div id="optionsContainer" class="space-y-2">
                    <input type="text" name="options[]" class="input" placeholder="Option 1" required>
                    <input type="text" name="options[]" class="input" placeholder="Option 2" required>
                </div>
                <button type="button" onclick="addOption()" class="mt-2 text-sm text-primary-600 hover:underline">
                    + Add Option
                </button>
            </div>

            <div>
                <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">
                    Expiration Date (Optional)
                </label>
                <input type="datetime-local" id="expires_at" name="expires_at" 
                       class="input">
                <p class="text-xs text-gray-500 mt-1">Leave empty for no expiration</p>
            </div>

            <div class="space-y-3">
                <label class="flex items-center">
                    <input type="checkbox" name="allow_multiple" value="1" class="mr-2">
                    <span>Allow multiple option selection</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="allow_change_vote" value="1" class="mr-2">
                    <span>Allow users to change their vote</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="show_results_before_vote" value="1" class="mr-2">
                    <span>Show results before voting</span>
                </label>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/polls" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Poll</button>
            </div>
        </form>
    </div>
</div>

<script>
let optionCount = 2;

function addOption() {
    optionCount++;
    const container = document.getElementById('optionsContainer');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'options[]';
    input.className = 'input';
    input.placeholder = `Option ${optionCount}`;
    container.appendChild(input);
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

