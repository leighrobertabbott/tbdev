<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="card mb-6">
        <h1 class="text-3xl font-bold mb-6">Links</h1>

        <?php if ($user): ?>
            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm">
                    <a href="/messages/compose?to=1" class="text-primary-600 hover:underline">Please report dead links!</a>
                </p>
            </div>
        <?php endif; ?>

        <div class="space-y-6">
            <!-- Other Pages -->
            <div>
                <h2 class="text-xl font-semibold mb-3">Other pages on this site</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <ul class="list-disc list-inside space-y-2">
                        <li>
                            <a href="/rss.xml" class="text-primary-600 hover:underline">RSS feed</a> - 
                            For use with RSS-enabled software. An alternative to torrent email notifications.
                        </li>
                        <li>
                            <a href="/rssdd.xml" class="text-primary-600 hover:underline">RSS feed (direct download)</a> - 
                            Links directly to the torrent file.
                        </li>
                        <li>
                            <a href="/bitbucket-upload" class="text-primary-600 hover:underline">Bitbucket</a> - 
                            If you need a place to host your avatar or other pictures.
                        </li>
                    </ul>
                </div>
            </div>

            <!-- BitTorrent Information -->
            <div>
                <h2 class="text-xl font-semibold mb-3">BitTorrent Information</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <ul class="list-disc list-inside space-y-2">
                        <li>
                            <a href="http://dessent.net/btfaq/" target="_blank" class="text-primary-600 hover:underline">Brian's BitTorrent FAQ and Guide</a> - 
                            Everything you need to know about BitTorrent. Required reading for all n00bs.
                        </li>
                        <li>
                            <a href="http://10mbit.com/faq/bt/" target="_blank" class="text-primary-600 hover:underline">The Ultimate BitTorrent FAQ</a> - 
                            Another nice BitTorrent FAQ, by Evil Timmy.
                        </li>
                    </ul>
                </div>
            </div>

            <!-- BitTorrent Software -->
            <div>
                <h2 class="text-xl font-semibold mb-3">BitTorrent Software</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <ul class="list-disc list-inside space-y-2">
                        <li><a href="http://pingpong-abc.sourceforge.net/" target="_blank" class="text-primary-600 hover:underline">ABC</a> - "ABC is an improved client for the Bittorrent peer-to-peer file distribution solution."</li>
                        <li><a href="http://azureus.sourceforge.net/" target="_blank" class="text-primary-600 hover:underline">Azureus</a> - "Azureus is a java bittorrent client. It provides a quite full bittorrent protocol implementation using java language."</li>
                        <li><a href="http://bnbt.go-dedicated.com/" target="_blank" class="text-primary-600 hover:underline">BNBT</a> - Nice BitTorrent tracker written in C++.</li>
                        <li><a href="http://bittornado.com/" target="_blank" class="text-primary-600 hover:underline">BitTornado</a> - a.k.a "TheSHAD0W's Experimental BitTorrent Client".</li>
                        <li><a href="http://www.bitconjurer.org/BitTorrent" target="_blank" class="text-primary-600 hover:underline">BitTorrent</a> - Bram Cohen's official BitTorrent client.</li>
                        <li><a href="http://ei.kefro.st/projects/btclient/" target="_blank" class="text-primary-600 hover:underline">BitTorrent EXPERIMENTAL</a> - "This is an unsupported, unofficial, and, most importantly, experimental build of the BitTorrent GUI for Windows."</li>
                        <li><a href="http://krypt.dyndns.org:81/torrent/" target="_blank" class="text-primary-600 hover:underline">Burst!</a> - Alternative Win32 BitTorrent client.</li>
                        <li><a href="http://g3torrent.sourceforge.net/" target="_blank" class="text-primary-600 hover:underline">G3 Torrent</a> - "A feature rich and graphically empowered bittorrent client written in python."</li>
                        <li><a href="http://krypt.dyndns.org:81/torrent/maketorrent/" target="_blank" class="text-primary-600 hover:underline">MakeTorrent</a> - A tool for creating torrents.</li>
                        <li><a href="http://ptc.sourceforge.net/" target="_blank" class="text-primary-600 hover:underline">Personal Torrent Collector</a> - BitTorrent client.</li>
                        <li><a href="http://www.shareaza.com/" target="_blank" class="text-primary-600 hover:underline">Shareaza</a> - Gnutella, eDonkey and BitTorrent client.</li>
                    </ul>
                </div>
            </div>

            <!-- Download Sites -->
            <div>
                <h2 class="text-xl font-semibold mb-3">Download sites</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <ul class="list-disc list-inside space-y-2">
                        <li><a href="http://www.suprnova.org/" target="_blank" class="text-primary-600 hover:underline">SuprNova</a> - Apps, games, movies, TV and other stuff. [popups]</li>
                        <li><a href="http://empornium.us:6969/" target="_blank" class="text-primary-600 hover:underline">Empornium</a> - Pr0n, and then some!</li>
                    </ul>
                </div>
            </div>

            <!-- Forum Communities -->
            <div>
                <h2 class="text-xl font-semibold mb-3">Forum communities</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <ul class="list-disc list-inside space-y-2">
                        <li><a href="http://www.filesoup.com/" target="_blank" class="text-primary-600 hover:underline">Filesoup</a> - BitTorrent community.</li>
                        <li><a href="http://www.torrent-addiction.com/forums/index.php" target="_blank" class="text-primary-600 hover:underline">Torrent Addiction</a> - Another BitTorrent community. [popups]</li>
                        <li><a href="http://www.terabits.net/" target="_blank" class="text-primary-600 hover:underline">TeraBits</a> - Games, movies, apps both unix and win, tracker support, music, xxx.</li>
                        <li><a href="http://www.ftpdreams.com/new/forum/sitenews.asp" target="_blank" class="text-primary-600 hover:underline">FTP Dreams</a> - "Where Dreams Become a Reality".</li>
                    </ul>
                </div>
            </div>

            <!-- Other Sites -->
            <div>
                <h2 class="text-xl font-semibold mb-3">Other sites</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <ul class="list-disc list-inside space-y-2">
                        <li><a href="http://www.nforce.nl/" target="_blank" class="text-primary-600 hover:underline">NFOrce</a> - Game and movie release tracker / forums.</li>
                        <li><a href="http://www.grokmusiq.com/" target="_blank" class="text-primary-600 hover:underline">grokMusiQ</a> - Music release tracker.</li>
                        <li><a href="http://www.izonews.com/" target="_blank" class="text-primary-600 hover:underline">iSONEWS</a> - Release tracker and forums.</li>
                        <li><a href="http://www.btsites.tk" target="_blank" class="text-primary-600 hover:underline">BTSITES.TK</a> - BitTorrent link site. [popups]</li>
                        <li><a href="http://www.litezone.com/" target="_blank" class="text-primary-600 hover:underline">Link2U</a> - BitTorrent link site.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

