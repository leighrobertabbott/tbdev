<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Models\Torrent;
use App\Core\Database;
use Symfony\Component\HttpFoundation\Request;

class RatingController
{
    public function rate(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $torrentId = (int) $request->request->get('id', 0);
        $rating = (int) $request->request->get('rating', 0);

        if ($torrentId <= 0 || $rating < 1 || $rating > 5) {
            return ResponseHelper::redirect("/torrent/{$torrentId}");
        }

        $torrent = Torrent::findById($torrentId);
        if (!$torrent) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Not Found'], 404);
        }

        // Check if already rated
        $existing = Database::fetchOne(
            "SELECT id FROM ratings WHERE torrent = :torrent AND user = :user",
            ['torrent' => $torrentId, 'user' => $user['id']]
        );

        if ($existing) {
            // Update existing rating
            Database::execute(
                "UPDATE ratings SET rating = :rating, added = :time WHERE id = :id",
                ['id' => $existing['id'], 'rating' => $rating, 'time' => time()]
            );

            // Update torrent rating sum
            $oldRating = Database::fetchOne(
                "SELECT rating FROM ratings WHERE id = :id",
                ['id' => $existing['id']]
            );
            $oldRating = (int) ($oldRating['rating'] ?? 0);
            
            Database::execute(
                "UPDATE torrents SET ratingsum = ratingsum - :old + :new WHERE id = :id",
                ['id' => $torrentId, 'old' => $oldRating, 'new' => $rating]
            );
        } else {
            // Insert new rating
            Database::execute(
                "INSERT INTO ratings (torrent, user, rating, added) VALUES (:torrent, :user, :rating, :time)",
                ['torrent' => $torrentId, 'user' => $user['id'], 'rating' => $rating, 'time' => time()]
            );

            // Update torrent stats
            Database::execute(
                "UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + :rating WHERE id = :id",
                ['id' => $torrentId, 'rating' => $rating]
            );
        }

        return ResponseHelper::redirect("/torrent/{$torrentId}?rated=1");
    }
}

