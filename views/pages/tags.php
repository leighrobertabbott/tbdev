<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="card mb-6">
        <h1 class="text-3xl font-bold mb-6">BBCode Tags</h1>
        
        <p class="text-gray-700 mb-6">
            The <?= htmlspecialchars(\App\Core\Config::get('app.name', 'TorrentBits')) ?> forums supports a number of <em>BB tags</em> which you can embed to modify how your posts are displayed.
        </p>

        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <form method="POST" action="/tags" class="flex items-end space-x-2">
                <div class="flex-1">
                    <label for="test" class="block text-sm font-medium text-gray-700 mb-1">Test your code:</label>
                    <textarea id="test" name="test" rows="3" class="input" 
                              placeholder="Enter BBCode here..."><?= htmlspecialchars($testInput ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Test</button>
            </form>
            
            <?php if (!empty($testInput ?? '')): ?>
                <div class="mt-4 p-4 bg-white border rounded-lg">
                    <h3 class="font-semibold mb-2">Result:</h3>
                    <div class="prose">
                        <?= \App\Core\FormatHelper::bbcode($testInput) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="space-y-6">
            <?php
            function renderTag($name, $description, $syntax, $example, $remarks = '') {
                $result = \App\Core\FormatHelper::bbcode($example);
                ?>
                <div class="border rounded-lg p-4">
                    <h3 class="text-lg font-semibold mb-3"><?= htmlspecialchars($name) ?></h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <strong>Description:</strong>
                            <p class="text-gray-600"><?= htmlspecialchars($description) ?></p>
                        </div>
                        <div>
                            <strong>Syntax:</strong>
                            <code class="block bg-gray-100 p-2 rounded mt-1"><?= htmlspecialchars($syntax) ?></code>
                        </div>
                        <div>
                            <strong>Example:</strong>
                            <code class="block bg-gray-100 p-2 rounded mt-1"><?= htmlspecialchars($example) ?></code>
                        </div>
                        <div>
                            <strong>Result:</strong>
                            <div class="bg-gray-50 p-2 rounded mt-1 border"><?= $result ?></div>
                        </div>
                        <?php if ($remarks): ?>
                            <div class="md:col-span-2">
                                <strong>Remarks:</strong>
                                <p class="text-gray-600"><?= htmlspecialchars($remarks) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
            
            renderTag('Bold', 'Makes the enclosed text bold.', '[b]Text[/b]', '[b]This is bold text.[/b]');
            renderTag('Italic', 'Makes the enclosed text italic.', '[i]Text[/i]', '[i]This is italic text.[/i]');
            renderTag('Underline', 'Makes the enclosed text underlined.', '[u]Text[/u]', '[u]This is underlined text.[/u]');
            renderTag('Color (alt. 1)', 'Changes the color of the enclosed text.', '[color=Color]Text[/color]', '[color=blue]This is blue text.[/color]', 'What colors are valid depends on the browser. If you use the basic colors (red, green, blue, yellow, pink etc) you should be safe.');
            renderTag('Color (alt. 2)', 'Changes the color of the enclosed text.', '[color=#RGB]Text[/color]', '[color=#0000ff]This is blue text.[/color]', 'RGB must be a six digit hexadecimal number.');
            renderTag('Size', 'Sets the size of the enclosed text.', '[size=n]text[/size]', '[size=4]This is size 4.[/size]', 'n must be an integer in the range 1 (smallest) to 7 (biggest). The default size is 2.');
            renderTag('Font', 'Sets the type-face (font) for the enclosed text.', '[font=Font]Text[/font]', '[font=Impact]Hello world![/font]', 'You specify alternative fonts by separating them with a comma.');
            renderTag('Hyperlink (alt. 1)', 'Inserts a hyperlink.', '[url]URL[/url]', '[url]' . \App\Core\Config::get('app.url', '/') . '[/url]', 'This tag is superfluous; all URLs are automatically hyperlinked.');
            renderTag('Hyperlink (alt. 2)', 'Inserts a hyperlink.', '[url=URL]Link text[/url]', '[url=' . \App\Core\Config::get('app.url', '/') . ']' . \App\Core\Config::get('app.name', 'TorrentBits') . '[/url]', 'You do not have to use this tag unless you want to set the link text; all URLs are automatically hyperlinked.');
            renderTag('Image (alt. 1)', 'Inserts a picture.', '[img=URL]', '[img=' . \App\Core\Config::get('app.url', '/') . '/pic/logo.gif]', 'The URL must end with .gif, .jpg or .png.');
            renderTag('Image (alt. 2)', 'Inserts a picture.', '[img]URL[/img]', '[img]' . \App\Core\Config::get('app.url', '/') . '/pic/logo.gif[/img]', 'The URL must end with .gif, .jpg or .png.');
            renderTag('Quote (alt. 1)', 'Inserts a quote.', '[quote]Quoted text[/quote]', '[quote]The quick brown fox jumps over the lazy dog.[/quote]');
            renderTag('Quote (alt. 2)', 'Inserts a quote.', '[quote=Author]Quoted text[/quote]', '[quote=John Doe]The quick brown fox jumps over the lazy dog.[/quote]');
            renderTag('List', 'Inserts a list item.', '[*]Text', '[*] This is item 1\n[*] This is item 2');
            renderTag('Preformat', 'Preformatted (monospace) text. Does not wrap automatically.', '[pre]Text[/pre]', '[pre]This is preformatted text.[/pre]');
            ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

