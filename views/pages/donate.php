<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="card mb-6">
        <h1 class="text-3xl font-bold mb-6">Donate</h1>
        
        <div class="flex items-start space-x-4 mb-6">
            <div class="flex-1">
                <p class="text-gray-700 mb-4">
                    If you make use of this software, please consider donating to <?= htmlspecialchars(\App\Core\Config::get('app.name', 'TorrentBits')) ?>. 
                    We don't ask for much, just a nominal payment to show your support.
                </p>
            </div>
            <div>
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="hosted_button_id" value="KQXM3SW2RKKSS">
                    <input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" 
                           border="0" name="submit" 
                           title="PayPal - The safer, easier way to pay online!" 
                           alt="Donate with PayPal button">
                </form>
                <p class="text-sm text-gray-600 mt-2">Thank You For Using Our Software!</p>
            </div>
        </div>

        <div class="border-t pt-6">
            <h2 class="text-xl font-semibold mb-4">Other Ways to Donate</h2>
            <p class="text-gray-600">No other ways at the moment...</p>
        </div>

        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <p class="text-sm text-gray-700">
                <strong>After you have donated</strong> -- make sure to 
                <a href="/messages/compose?to=1" class="text-primary-600 hover:underline">send us</a> 
                the <span class="text-red-600 font-semibold">transaction id</span> 
                so we can credit your account!
            </p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

