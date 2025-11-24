<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Models\Torrent;
use App\Models\User;
use App\Core\Database;
use App\Services\RecommendationService;
use App\Services\AchievementService;
use Symfony\Component\HttpFoundation\Request;

class HomeController
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get latest user
            $latestUser = User::findLatest();
            
            // Get stats
            $stats = [
                'registered' => User::count(),
                'torrents' => Torrent::count(),
            ];
            
            // Get peer stats (with error handling)
            $seeders = 0;
            $leechers = 0;
            try {
                $peerStats = Database::fetchOne("SELECT value_u as seeders FROM avps WHERE arg = 'seeders'");
                $leecherStats = Database::fetchOne("SELECT value_u as leechers FROM avps WHERE arg = 'leechers'");
                $seeders = (int)($peerStats['seeders'] ?? 0);
                $leechers = (int)($leecherStats['leechers'] ?? 0);
            } catch (\Exception $e) {
                error_log('Error fetching peer stats: ' . $e->getMessage());
            }
            
            $peers = $seeders + $leechers;
            $ratio = $leechers > 0 ? round($seeders / $leechers * 100) : 0;
            
            // Get latest news (featured article)
            $latestNews = null;
            $recentNews = [];
            try {
                $allNews = Database::fetchAll(
                    "SELECT n.*, u.username,
                     (SELECT COUNT(*) FROM posts p 
                      INNER JOIN topics t ON p.topicid = t.id 
                      WHERE t.forumid = (SELECT id FROM forums WHERE name LIKE '%NEWS%' LIMIT 1) 
                      AND t.subject LIKE CONCAT('%', n.headline, '%')) as reply_count
                     FROM news n 
                     LEFT JOIN users u ON n.userid = u.id
                     WHERE n.added + (3600 * 24 * 45) > ? 
                     ORDER BY n.added DESC 
                     LIMIT 6",
                    [time()]
                );
                
                if (!empty($allNews)) {
                    $latestNews = $allNews[0];
                    $recentNews = array_slice($allNews, 1, 5);
                }
            } catch (\Exception $e) {
                error_log('Error fetching news: ' . $e->getMessage());
            }
            
            // Get recent active forum threads
            $recentThreads = [];
            try {
                $recentThreads = Database::fetchAll(
                    "SELECT t.*, 
                            u.username as author_name,
                            u.avatar as author_avatar,
                            f.name as forum_name,
                            (SELECT COUNT(*) FROM posts p WHERE p.topicid = t.id) as reply_count,
                            (SELECT u2.username FROM posts p2 
                             INNER JOIN users u2 ON p2.userid = u2.id 
                             WHERE p2.topicid = t.id 
                             ORDER BY p2.added DESC LIMIT 1) as last_post_by,
                            (SELECT p2.added FROM posts p2 
                             WHERE p2.topicid = t.id 
                             ORDER BY p2.added DESC LIMIT 1) as last_post_time
                     FROM topics t
                     LEFT JOIN users u ON t.userid = u.id
                     LEFT JOIN forums f ON t.forumid = f.id
                     WHERE t.lastpost > 0
                     ORDER BY t.lastpost DESC
                     LIMIT 9"
                );
            } catch (\Exception $e) {
                error_log('Error fetching forum threads: ' . $e->getMessage());
            }
            
            // Get active polls (with error handling)
            $activePolls = [];
            try {
                $activePolls = \App\Models\Poll::all('active', 3);
            } catch (\Exception $e) {
                error_log('Error fetching polls: ' . $e->getMessage());
            }

            // Get recommendations if user is logged in (with error handling)
            $recommendations = [];
            $trending = [];
            if ($user) {
                try {
                    $recommendations = RecommendationService::getForUser($user['id'], 8);
                    $trending = RecommendationService::getTrending(8);
                } catch (\Exception $e) {
                    error_log('Error fetching recommendations: ' .
                        $e->getMessage());
                }
                
                // Check for new achievements
                try {
                    $newAchievements = AchievementService::checkAchievements($user['id']);
                    if (!empty($newAchievements)) {
                        $_SESSION['new_achievements'] = $newAchievements;
                    }
                } catch (\Exception $e) {
                    error_log('Error checking achievements: ' .
                        $e->getMessage());
                }
            }
            
            return ResponseHelper::view('home/index', [
                'user' => $user,
                'latestUser' => $latestUser,
                'stats' => $stats,
                'seeders' => $seeders,
                'leechers' => $leechers,
                'peers' => $peers,
                'ratio' => $ratio,
                'latestNews' => $latestNews,
                'recentNews' => $recentNews,
                'recentThreads' => $recentThreads,
                'activePolls' => $activePolls,
                'recommendations' => $recommendations,
                'trending' => $trending,
                'pageTitle' => 'Home',
            ]);
        } catch (\Exception $e) {
            error_log('HomeController error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            return new \Symfony\Component\HttpFoundation\Response(
                '<pre>Error: ' .
                    htmlspecialchars($e->getMessage()) .
                    "\n\n" .
                    htmlspecialchars($e->getTraceAsString()) .
                    '</pre>',
                500,
                ['Content-Type' => 'text/html']
            );
        }
    }
}


