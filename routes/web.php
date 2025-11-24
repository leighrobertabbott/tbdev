<?php

use App\Core\Router;
use App\Core\Middleware;
use App\Controllers\Web\HomeController;
use App\Controllers\Web\AuthController;
use App\Controllers\Web\BrowseController;
use Symfony\Component\HttpFoundation\Response;

/** @var Router $router */

// Installer routes (must be before other routes)
$router->get(
    '/installer',
    [\App\Controllers\Web\InstallerController::class, 'index'],
    'installer');
$router->get(
    '/installer/step1',
    [\App\Controllers\Web\InstallerController::class, 'step1'],
    'installer.step1');
$router->get(
    '/installer/step2',
    [\App\Controllers\Web\InstallerController::class, 'step2'],
    'installer.step2');
$router->post(
    '/installer/step2',
    [\App\Controllers\Web\InstallerController::class, 'step2'],
    'installer.step2.post');
$router->get(
    '/installer/step3',
    [\App\Controllers\Web\InstallerController::class, 'step3'],
    'installer.step3');
$router->post(
    '/installer/step3',
    [\App\Controllers\Web\InstallerController::class, 'step3'],
    'installer.step3.post');
$router->get(
    '/installer/step4',
    [\App\Controllers\Web\InstallerController::class, 'step4'],
    'installer.step4');
$router->post(
    '/installer/step4',
    [\App\Controllers\Web\InstallerController::class, 'step4'],
    'installer.step4.post');
$router->get(
    '/installer/complete',
    [\App\Controllers\Web\InstallerController::class, 'complete'],
    'installer.complete');

// Home page
$router->get('/', [HomeController::class, 'index'], 'home');

// Auth routes
$router->get('/login', [AuthController::class, 'showLogin'], 'login');
$router->post(
    '/login',
    [AuthController::class, 'login'],
    'login.post',
    [Middleware::csrf()]);
$router->get('/signup', [AuthController::class, 'showSignup'], 'signup');
$router->post(
    '/signup',
    [AuthController::class, 'signup'],
    'signup.post',
    [Middleware::csrf()]);
$router->get('/logout', [AuthController::class, 'logout'], 'logout');

// Recovery routes
$router->get(
    '/recover',
    [\App\Controllers\Web\RecoveryController::class, 'show'],
    'recover');
$router->post(
    '/recover',
    [\App\Controllers\Web\RecoveryController::class, 'recover'],
    'recover.post',
    [Middleware::csrf()]);
$router->get(
    '/recover/reset',
    [\App\Controllers\Web\RecoveryController::class, 'reset'],
    'recover.reset');
$router->post(
    '/recover/reset',
    [\App\Controllers\Web\RecoveryController::class, 'reset'],
    'recover.reset.post',
    [Middleware::csrf()]);

// Confirmation routes
$router->get(
    '/confirm',
    [\App\Controllers\Web\ConfirmController::class, 'confirm'],
    'confirm');
$router->get(
    '/confirmemail',
    [\App\Controllers\Web\ConfirmController::class, 'confirmEmail'],
    'confirmemail');

// Browse routes
$router->get(
    '/browse',
    [BrowseController::class, 'index'],
    'browse',
    [Middleware::auth()]);
$router->get(
    '/search',
    [BrowseController::class, 'search'],
    'search',
    [Middleware::auth()]);

// Torrent routes
$router->get(
    '/torrent/{id}',
    [\App\Controllers\Web\TorrentController::class, 'show'],
    'torrent.show',
    [Middleware::auth()]);
$router->get(
    '/torrent/{id}/nfo',
    [\App\Controllers\Web\TorrentController::class, 'viewNfo'],
    'torrent.nfo',
    [Middleware::auth()]);
$router->get(
    '/torrent/{id}/files',
    [\App\Controllers\Web\TorrentController::class, 'fileList'],
    'torrent.files',
    [Middleware::auth()]);
$router->get(
    '/torrent/{id}/peers',
    [\App\Controllers\Web\TorrentController::class, 'peerList'],
    'torrent.peers',
    [Middleware::auth()]);
$router->get(
    '/upload',
    [\App\Controllers\Web\UploadController::class, 'show'],
    'upload',
    [Middleware::auth()]);
$router->post(
    '/upload',
    [\App\Controllers\Web\UploadController::class, 'upload'],
    'upload.post',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/download/{id}',
    [\App\Controllers\Web\DownloadController::class, 'download'],
    'download',
    [Middleware::auth()]);

// User routes
$router->get(
    '/user/{id}',
    [\App\Controllers\Web\UserController::class, 'show'],
    'user.show',
    [Middleware::auth()]);
$router->get(
    '/profile',
    [\App\Controllers\Web\UserController::class, 'profile'],
    'profile',
    [Middleware::auth()]);
$router->get(
    '/profile/edit',
    [\App\Controllers\Web\UserController::class, 'edit'],
    'profile.edit',
    [Middleware::auth()]);
$router->post(
    '/profile/update',
    [\App\Controllers\Web\UserController::class, 'update'],
    'profile.update',
    [Middleware::auth(), Middleware::csrf()]);
$router->post(
    '/profile/password',
    [\App\Controllers\Web\UserController::class, 'changePassword'],
    'profile.password',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/mytorrents',
    [\App\Controllers\Web\UserController::class, 'myTorrents'],
    'mytorrents',
    [Middleware::auth()]);

// Messages routes (specific routes must come before parameterized routes)
$router->get(
    '/messages',
    [\App\Controllers\Web\MessageController::class, 'index'],
    'messages',
    [Middleware::auth()]);
$router->get(
    '/messages/compose',
    [\App\Controllers\Web\MessageController::class, 'compose'],
    'message.compose',
    [Middleware::auth()]);
$router->post(
    '/messages/send',
    [\App\Controllers\Web\MessageController::class, 'send'],
    'message.send',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/messages/{id}',
    [\App\Controllers\Web\MessageController::class, 'show'],
    'message.show',
    [Middleware::auth()]);

// Forums routes
$router->get(
    '/forums',
    [\App\Controllers\Web\ForumController::class, 'index'],
    'forums',
    [Middleware::auth()]);
$router->get(
    '/forum/{id}',
    [\App\Controllers\Web\ForumController::class, 'show'],
    'forum.show',
    [Middleware::auth()]);
$router->get(
    '/topic/{id}',
    [\App\Controllers\Web\ForumController::class, 'topic'],
    'topic.show',
    [Middleware::auth()]);
$router->get(
    '/forum/{id}/new-topic',
    [\App\Controllers\Web\ForumController::class, 'newTopic'],
    'forum.new-topic',
    [Middleware::auth()]);
$router->post(
    '/forum/{id}/new-topic',
    [\App\Controllers\Web\ForumController::class, 'createTopic'],
    'forum.create-topic',
    [Middleware::auth(), Middleware::csrf()]);
$router->post(
    '/topic/{id}/reply',
    [\App\Controllers\Web\ForumController::class, 'reply'],
    'topic.reply',
    [Middleware::auth(), Middleware::csrf()]);

// Comment routes
$router->post(
    '/comment',
    [\App\Controllers\Web\CommentController::class, 'create'],
    'comment.create',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/comment/{id}/edit',
    [\App\Controllers\Web\CommentController::class, 'edit'],
    'comment.edit',
    [Middleware::auth()]);
$router->post(
    '/comment/{id}/edit',
    [\App\Controllers\Web\CommentController::class, 'edit'],
    'comment.update',
    [Middleware::auth(), Middleware::csrf()]);
$router->post(
    '/comment/{id}/delete',
    [\App\Controllers\Web\CommentController::class, 'delete'],
    'comment.delete',
    [Middleware::auth(), Middleware::csrf()]);

// Rating routes
$router->post(
    '/rate',
    [\App\Controllers\Web\RatingController::class, 'rate'],
    'rate',
    [Middleware::auth(), Middleware::csrf()]);

// Friends/Blocks routes
$router->get(
    '/friends',
    [\App\Controllers\Web\FriendsController::class, 'index'],
    'friends',
    [Middleware::auth()]);
$router->get(
    '/friends/add',
    [\App\Controllers\Web\FriendsController::class, 'add'],
    'friends.add',
    [Middleware::auth()]);
$router->get(
    '/friends/delete',
    [\App\Controllers\Web\FriendsController::class, 'delete'],
    'friends.delete',
    [Middleware::auth()]);

// User History routes
$router->get(
    '/userhistory',
    [\App\Controllers\Web\UserHistoryController::class, 'index'],
    'userhistory',
    [Middleware::auth()]);

// Admin routes
$router->get(
    '/admin',
    [\App\Controllers\Admin\AdminController::class, 'index'],
    'admin',
    [Middleware::auth()]);
$router->get(
    '/admin/settings',
    [\App\Controllers\Admin\SettingsAdminController::class, 'index'],
    'admin.settings',
    [Middleware::auth()]);
$router->post(
    '/admin/settings',
    [\App\Controllers\Admin\SettingsAdminController::class, 'index'],
    'admin.settings.post',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/admin/users',
    [\App\Controllers\Admin\UserAdminController::class, 'index'],
    'admin.users',
    [Middleware::auth()]);
$router->get(
    '/admin/users/{id}',
    [\App\Controllers\Admin\UserAdminController::class, 'show'],
    'admin.users.show',
    [Middleware::auth()]);
$router->get(
    '/admin/users/{id}/edit',
    [\App\Controllers\Admin\UserAdminController::class, 'edit'],
    'admin.users.edit',
    [Middleware::auth()]);
$router->post(
    '/admin/users/{id}/edit',
    [\App\Controllers\Admin\UserAdminController::class, 'edit'],
    'admin.users.edit.post',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/admin/users/{id}/delete',
    [\App\Controllers\Admin\UserAdminController::class, 'delete'],
    'admin.users.delete',
    [Middleware::auth()]);
$router->post(
    '/admin/users/{id}/delete',
    [\App\Controllers\Admin\UserAdminController::class, 'delete'],
    'admin.users.delete.post',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/admin/users/add',
    [\App\Controllers\Admin\UserAdminController::class, 'add'],
    'admin.users.add',
    [Middleware::auth()]);
$router->post(
    '/admin/users/add',
    [\App\Controllers\Admin\UserAdminController::class, 'add'],
    'admin.users.add.post',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/admin/users/search',
    [\App\Controllers\Admin\UserAdminController::class, 'search'],
    'admin.users.search',
    [Middleware::auth()]);
$router->get(
    '/admin/torrents',
    [\App\Controllers\Admin\TorrentAdminController::class, 'index'],
    'admin.torrents',
    [Middleware::auth()]);
$router->get(
    '/admin/torrents/{id}/edit',
    [\App\Controllers\Admin\TorrentAdminController::class, 'edit'],
    'admin.torrents.edit',
    [Middleware::auth()]);
$router->post(
    '/admin/torrents/{id}/edit',
    [\App\Controllers\Admin\TorrentAdminController::class, 'edit'],
    'admin.torrents.edit.post',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/admin/torrents/{id}/delete',
    [\App\Controllers\Admin\TorrentAdminController::class, 'delete'],
    'admin.torrents.delete',
    [Middleware::auth()]);
$router->post(
    '/admin/torrents/{id}/delete',
    [\App\Controllers\Admin\TorrentAdminController::class, 'delete'],
    'admin.torrents.delete.post',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/admin/news',
    [\App\Controllers\Admin\NewsAdminController::class, 'index'],
    'admin.news',
    [Middleware::auth()]);
$router->get(
    '/admin/categories',
    [\App\Controllers\Admin\CategoryAdminController::class, 'index'],
    'admin.categories',
    [Middleware::auth()]);
$router->get(
    '/admin/categories/create',
    [\App\Controllers\Admin\CategoryAdminController::class, 'create'],
    'admin.categories.create',
    [Middleware::auth()]);
$router->post(
    '/admin/categories/create',
    [\App\Controllers\Admin\CategoryAdminController::class, 'create'],
    'admin.categories.create.post',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/admin/bans',
    [\App\Controllers\Admin\BanAdminController::class, 'index'],
    'admin.bans',
    [Middleware::auth()]);
$router->get(
    '/admin/bans/create',
    [\App\Controllers\Admin\BanAdminController::class, 'create'],
    'admin.bans.create',
    [Middleware::auth()]);
$router->post(
    '/admin/bans/create',
    [\App\Controllers\Admin\BanAdminController::class, 'create'],
    'admin.bans.create.post',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/admin/bans/{id}/delete',
    [\App\Controllers\Admin\BanAdminController::class, 'delete'],
    'admin.bans.delete',
    [Middleware::auth()]);
$router->get(
    '/admin/categories/{id}/delete',
    [\App\Controllers\Admin\CategoryAdminController::class, 'delete'],
    'admin.categories.delete',
    [Middleware::auth()]);
$router->get(
    '/admin/stats',
    [\App\Controllers\Admin\StatsAdminController::class, 'index'],
    'admin.stats',
    [Middleware::auth()]);
$router->get(
    '/admin/logs',
    [\App\Controllers\Admin\LogAdminController::class, 'index'],
    'admin.logs',
    [Middleware::auth()]);
$router->get(
    '/admin/cleanup',
    [\App\Controllers\Admin\CleanupAdminController::class, 'index'],
    'admin.cleanup',
    [Middleware::auth()]);
$router->post(
    '/admin/cleanup',
    [\App\Controllers\Admin\CleanupAdminController::class, 'index'],
    'admin.cleanup.post',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/admin/iptest',
    [\App\Controllers\Admin\IpTestAdminController::class, 'index'],
    'admin.iptest',
    [Middleware::auth()]);
$router->get(
    '/admin/forums',
    [\App\Controllers\Admin\ForumAdminController::class, 'index'],
    'admin.forums',
    [Middleware::auth()]);
$router->get(
    '/admin/forums/create',
    [\App\Controllers\Admin\ForumAdminController::class, 'create'],
    'admin.forums.create',
    [Middleware::auth()]);
$router->post(
    '/admin/forums/create',
    [\App\Controllers\Admin\ForumAdminController::class, 'create'],
    'admin.forums.create.post',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/admin/forums/{id}/delete',
    [\App\Controllers\Admin\ForumAdminController::class, 'delete'],
    'admin.forums.delete',
    [Middleware::auth()]);
$router->get(
    '/admin/mysql/stats',
    [\App\Controllers\Admin\MysqlAdminController::class, 'stats'],
    'admin.mysql.stats',
    [Middleware::auth()]);
$router->get(
    '/admin/mysql/overview',
    [\App\Controllers\Admin\MysqlAdminController::class, 'overview'],
    'admin.mysql.overview',
    [Middleware::auth()]);

// Poll routes
$router->get(
    '/polls',
    [\App\Controllers\Web\PollController::class, 'index'],
    'polls',
    [Middleware::auth()]);
$router->get(
    '/poll/{id}',
    [\App\Controllers\Web\PollController::class, 'show'],
    'poll.show',
    [Middleware::auth()]);
$router->post(
    '/poll/{id}/vote',
    [\App\Controllers\Web\PollController::class, 'vote'],
    'poll.vote',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/polls/create',
    [\App\Controllers\Web\PollController::class, 'create'],
    'polls.create',
    [Middleware::auth()]);
$router->post(
    '/polls/create',
    [\App\Controllers\Web\PollController::class, 'create'],
    'polls.create.post',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/polls/manage',
    [\App\Controllers\Web\PollController::class, 'manage'],
    'polls.manage',
    [Middleware::auth()]);
$router->get(
    '/poll/{id}/close',
    [\App\Controllers\Web\PollController::class, 'close'],
    'poll.close',
    [Middleware::auth()]);
$router->get(
    '/poll/{id}/delete',
    [\App\Controllers\Web\PollController::class, 'delete'],
    'poll.delete',
    [Middleware::auth()]);

// Other pages
$router->get(
    '/faq',
    [\App\Controllers\Web\PageController::class, 'faq'],
    'faq');
$router->get(
    '/rules',
    [\App\Controllers\Web\PageController::class, 'rules'],
    'rules');
$router->get(
    '/staff',
    [\App\Controllers\Web\PageController::class, 'staff'],
    'staff');
$router->get(
    '/topten',
    [\App\Controllers\Web\PageController::class, 'topten'],
    'topten',
    [Middleware::auth()]);
$router->get(
    '/donate',
    [\App\Controllers\Web\PageController::class, 'donate'],
    'donate');
$router->get(
    '/links',
    [\App\Controllers\Web\PageController::class, 'links'],
    'links');
$router->get(
    '/tags',
    [\App\Controllers\Web\PageController::class, 'tags'],
    'tags',
    [Middleware::auth()]);
$router->get(
    '/formats',
    [\App\Controllers\Web\PageController::class, 'formats'],
    'formats');
$router->get(
    '/videoformats',
    [\App\Controllers\Web\PageController::class, 'videoFormats'],
    'videoformats');
$router->get(
    '/useragreement',
    [\App\Controllers\Web\PageController::class, 'userAgreement'],
    'useragreement');

// Tracker routes (no auth required for announce)
$router->get(
    '/announce.php',
    [\App\Controllers\Tracker\AnnounceController::class, 'announce'],
    'announce');
$router->get(
    '/announce',
    [\App\Controllers\Tracker\AnnounceController::class, 'announce'],
    'announce.alt');
$router->get(
    '/scrape.php',
    [\App\Controllers\Tracker\ScrapeController::class, 'scrape'],
    'scrape');

// Advanced Features Routes
$router->get(
    '/recommendations',
    [\App\Controllers\Web\RecommendationController::class, 'index'],
    'recommendations',
    [Middleware::auth()]);
$router->get(
    '/achievements',
    [\App\Controllers\Web\AchievementController::class, 'index'],
    'achievements',
    [Middleware::auth()]);
$router->get(
    '/achievements/leaderboard',
    [\App\Controllers\Web\AchievementController::class, 'leaderboard'],
    'achievements.leaderboard',
    [Middleware::auth()]);
$router->get(
    '/twofactor',
    [\App\Controllers\Web\TwoFactorController::class, 'show'],
    'twofactor',
    [Middleware::auth()]);
$router->post(
    '/twofactor/enable',
    [\App\Controllers\Web\TwoFactorController::class, 'enable'],
    'twofactor.enable',
    [Middleware::auth(), Middleware::csrf()]);
$router->post(
    '/twofactor/disable',
    [\App\Controllers\Web\TwoFactorController::class, 'disable'],
    'twofactor.disable',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/collections',
    [\App\Controllers\Web\CollectionController::class, 'index'],
    'collections',
    [Middleware::auth()]);
$router->get(
    '/collections/create',
    [\App\Controllers\Web\CollectionController::class, 'create'],
    'collections.create',
    [Middleware::auth()]);
$router->post(
    '/collections/create',
    [\App\Controllers\Web\CollectionController::class, 'create'],
    'collections.create.post',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/collections/{id}',
    [\App\Controllers\Web\CollectionController::class, 'show'],
    'collections.show',
    [Middleware::auth()]);
$router->post(
    '/collections/{id}/add',
    [\App\Controllers\Web\CollectionController::class, 'addTorrent'],
    'collections.add',
    [Middleware::auth(), Middleware::csrf()]);
$router->post(
    '/collections/{id}/remove/{torrentId}',
    [\App\Controllers\Web\CollectionController::class, 'removeTorrent'],
    'collections.remove',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/collections/{id}/delete',
    [\App\Controllers\Web\CollectionController::class, 'delete'],
    'collections.delete',
    [Middleware::auth()]);
$router->post(
    '/collections/{id}/delete',
    [\App\Controllers\Web\CollectionController::class, 'delete'],
    'collections.delete.post',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/savedsearches',
    [\App\Controllers\Web\SavedSearchController::class, 'index'],
    'savedsearches',
    [Middleware::auth()]);
$router->post(
    '/savedsearches/save',
    [\App\Controllers\Web\SavedSearchController::class, 'save'],
    'savedsearches.save',
    [Middleware::auth(), Middleware::csrf()]);
$router->post(
    '/savedsearches/{id}/delete',
    [\App\Controllers\Web\SavedSearchController::class, 'delete'],
    'savedsearches.delete',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/admin/analytics',
    [\App\Controllers\Admin\AnalyticsAdminController::class, 'index'],
    'admin.analytics',
    [Middleware::auth()]);

// Legacy route support - redirect old PHP files to new structure
$router->get('/index.php', function() {
    return new Response('', 301, ['Location' => '/']);
}, 'legacy_index');

