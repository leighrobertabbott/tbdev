<?php
$content = ob_get_clean();
ob_start();
?>

<div class="max-w-4xl mx-auto">
    <div class="card mb-6">
        <h1 class="text-3xl font-bold mb-6">Downloaded Files</h1>
        
        <div class="prose max-w-none space-y-6">
            <section>
                <h2 class="text-2xl font-semibold mb-3">A Handy Guide to Using the Files You've Downloaded</h2>
                <p class="text-gray-700">
                    Hey guys, here's some info about common files that you can download from the internet,
                    and a little bit about using these files for their intended purposes. If you're stuck
                    on what exactly a file is or how to open it maybe your answer lies ahead. If you don't
                    find your answer here, then please post in the 'Forum'. So without further adieu lets
                    get the show on the road!
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold mb-3">Compression Files</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="mb-3"><strong>.rar .zip .ace .r01 .001</strong></p>
                    <p class="text-gray-700 mb-3">
                        These extensions are quite common and mean that your file(s) are compressed into an 'archive'.
                        This is just a way of making the files more compact and easier to download.
                    </p>
                    <p class="text-gray-700 mb-3">
                        To open any of those archives listed above you can use 
                        <a href="http://www.rarsoft.com/download.htm" target="_blank" class="text-primary-600 hover:underline">WinRAR</a> 
                        (Make sure you have the latest version) or 
                        <a href="http://www.powerarchiver.com/download/" target="_blank" class="text-primary-600 hover:underline">PowerArchiver</a>.
                    </p>
                    <p class="text-gray-700 mb-3">
                        If those programs aren't working for you and you have a .zip file you can try 
                        <a href="http://www.winzip.com/download.htm" target="_blank" class="text-primary-600 hover:underline">WinZip</a> (Trial version).
                    </p>
                    <p class="text-gray-700 mb-3">
                        If the two first mentioned programs aren't working for you and you have a .ace or .001
                        file you can try <a href="http://www.winace.com/" target="_blank" class="text-primary-600 hover:underline">Winace</a> (Trial version).
                    </p>
                    <p class="mb-3"><strong>.cbr .cbz</strong></p>
                    <p class="text-gray-700">
                        These are usually comic books in an archive format. a .cbr file is actually the same
                        thing as a .rar file and a .cbz file is the same as a .zip file. However, often when
                        opening them with WinRAR or WinZip it will disorder your pages. To display these
                        archives properly it's often best to use 
                        <a href="http://www.geocities.com/davidayton/CDisplay" target="_blank" class="text-primary-600 hover:underline">CDisplay</a>.
                    </p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold mb-3">Multimedia Files</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="mb-3"><strong>.avi .mpg .mpeg .divx .xvid .wmv</strong></p>
                    <p class="text-gray-700 mb-3">
                        These files are usually movies or TV shows, or a host of other types of media. They can
                        be viewed using various media players, but I suggest using
                        <a href="http://www.inmatrix.com/files/zoomplayer_download.shtml" target="_blank" class="text-primary-600 hover:underline">Zoomplayer</a>,
                        <a href="http://www.bsplayer.org/" target="_blank" class="text-primary-600 hover:underline">BSPlayer</a>, 
                        <a href="http://www.videolan.org/vlc/" target="_blank" class="text-primary-600 hover:underline">VLC media player</a>
                        or <a href="http://www.microsoft.com/windows/windowsmedia/default.aspx" target="_blank" class="text-primary-600 hover:underline">Windows Media Player</a>. 
                        Also, you'll need to make sure you have the right codecs to play each individual file.
                    </p>
                    <p class="text-gray-700 mb-3">
                        Codecs are a tricky business sometimes so to help you out with your file and what exact codecs it needs try using 
                        <a href="http://www.headbands.com/gspot/download.html" target="_blank" class="text-primary-600 hover:underline">GSpot</a>. 
                        It tells you what codecs you need. Then just look on the net to find them, below are some common codecs:
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-gray-700 mb-3">
                        <li><a href="http://sourceforge.net/project/showfiles.php?group_id=53761" target="_blank" class="text-primary-600 hover:underline">ffdshow</a> (Recommended! plays many formats: XviD, DivX, 3ivX, mpeg-4))</li>
                        <li><a href="http://nic.dnsalias.com/xvid.html" target="_blank" class="text-primary-600 hover:underline">XviD codec</a></li>
                        <li><a href="http://www.divx.com/divx/" target="_blank" class="text-primary-600 hover:underline">DivX codec</a></li>
                        <li><a href="http://sourceforge.net/project/showfiles.php?group_id=66022" target="_blank" class="text-primary-600 hover:underline">ac3filter</a> (for AC3 soundtracks, aka '5.1')</li>
                        <li><a href="http://tobias.everwicked.com/oggds.htm" target="_blank" class="text-primary-600 hover:underline">Ogg media codec</a> (for .OGM files)</li>
                    </ul>
                    <p class="text-gray-700 mb-3">Can't find what you're looking for? Check out these sites:</p>
                    <ul class="list-disc list-inside space-y-1 text-gray-700">
                        <li><a href="http://www.divx-digest.com/" target="_blank" class="text-primary-600 hover:underline">DivX-Digest</a></li>
                        <li><a href="http://www.digital-digest.com/" target="_blank" class="text-primary-600 hover:underline">Digital-Digest</a></li>
                        <li><a href="http://www.doom9.org/" target="_blank" class="text-primary-600 hover:underline">Doom9</a></li>
                        <li><a href="http://www.dvdrhelp.com/" target="_blank" class="text-primary-600 hover:underline">DVD-R Help</a></li>
                    </ul>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold mb-3">CD Image Files</h2>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="mb-3"><strong>.bin and .cue</strong></p>
                    <p class="text-gray-700 mb-3">
                        These are your standard images of a CD, and are used quite a lot these days. To open them
                        you have a couple options. You can burn them using <a href="http://www.ahead.de" target="_blank" class="text-primary-600 hover:underline">Nero</a>
                        (Trial Version) or <a href="http://www.alcohol-software.com/" target="_blank" class="text-primary-600 hover:underline">Alcohol 120%</a>.
                        You can also use <a href="http://www.daemon-tools.cc/portal/portal.php" target="_blank" class="text-primary-600 hover:underline">Daemon Tools</a>, 
                        which lets you mount the image to a 'virtual cd-rom'.
                    </p>
                    <p class="mb-3"><strong>.iso</strong></p>
                    <p class="text-gray-700 mb-3">
                        Another type of image file that follows similar rules as .bin and .cue, only you extract
                        or create them using <a href="http://www.winiso.com" target="_blank" class="text-primary-600 hover:underline">WinISO</a> or
                        <a href="http://ww.smart-projects.net/isobuster/" target="_blank" class="text-primary-600 hover:underline">ISOBuster</a>.
                    </p>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-semibold mb-3">Other Files</h2>
                <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                    <div>
                        <p class="mb-2"><strong>.txt .doc</strong></p>
                        <p class="text-gray-700">These are text files. .txt files can be opened with notepad or whatever your default text editor happens to be, and .doc are opened with Microsoft Word.</p>
                    </div>
                    <div>
                        <p class="mb-2"><strong>.nfo</strong></p>
                        <p class="text-gray-700">
                            These contain information about the file you just downloaded, and it's HIGHLY recommended
                            that you read these! They are plain text files, often with ascii-art. You can open them
                            with Notepad, Wordpad, <a href="http://www.damn.to/software/nfoviewer.html" target="_blank" class="text-primary-600 hover:underline">DAMN NFO Viewer</a>
                            or <a href="http://www.ultraedit.com/" target="_blank" class="text-primary-600 hover:underline">UltraEdit</a>.
                        </p>
                    </div>
                    <div>
                        <p class="mb-2"><strong>.pdf</strong></p>
                        <p class="text-gray-700">Opened with <a href="http://www.adobe.com/products/acrobat/main.html" target="_blank" class="text-primary-600 hover:underline">Adobe Acrobat Reader</a>.</p>
                    </div>
                    <div>
                        <p class="mb-2"><strong>.jpg .gif .tga .psd</strong></p>
                        <p class="text-gray-700">Basic image files. These files generally contain pictures, and can be opened with Adobe Photoshop or whatever your default image viewer is.</p>
                    </div>
                    <div>
                        <p class="mb-2"><strong>.sfv</strong></p>
                        <p class="text-gray-700">
                            Checks to make sure that your multi-volume archives are complete. This just lets you know
                            if you've downloaded something complete or not. (This is not really an issue when downloading
                            via torrent.) You can open/activate these files with 
                            <a href="http://www.traction-software.co.uk/SFVChecker/" target="_blank" class="text-primary-600 hover:underline">SFVChecker</a> 
                            (Trial version) or <a href="http://www.big-o-software.com/products/hksfv/" target="_blank" class="text-primary-600 hover:underline">hkSFV</a> for example.
                        </p>
                    </div>
                    <div>
                        <p class="mb-2"><strong>.par</strong></p>
                        <p class="text-gray-700">
                            This is a parity file, and is often used when downloading from newsgroups. These files can
                            fill in gaps when you're downloading a multi-volume archive and get corrupted or missing parts.
                            Open them with <a href="http://www.pbclements.co.uk/QuickPar/" target="_blank" class="text-primary-600 hover:underline">QuickPar</a>.
                        </p>
                    </div>
                </div>
            </section>

            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-gray-700">
                    If you have any suggestion/changes <a href="/staff" class="text-primary-600 hover:underline font-semibold">PM</a> one of the Admins/SysOp!<br><br>
                    This file was originally written by hussdiesel at filesoup, then edited by Rhomboid and re-edited by us.
                </p>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>

