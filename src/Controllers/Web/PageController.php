<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Models\Torrent;
use App\Core\Database;
use Symfony\Component\HttpFoundation\Request;

class PageController
{
    public function faq(Request $request)
    {
        return ResponseHelper::view('pages/faq', [
            'user' => Auth::user(),
            'pageTitle' => 'FAQ',
        ]);
    }

    public function rules(Request $request)
    {
        return ResponseHelper::view('pages/rules', [
            'user' => Auth::user(),
            'pageTitle' => 'Rules',
        ]);
    }

    public function staff(Request $request)
    {
        $staff = Database::fetchAll(
            "SELECT id, username, class, title, added FROM users WHERE class >= 4 ORDER BY class DESC, username ASC"
        );

        return ResponseHelper::view('pages/staff', [
            'user' => Auth::user(),
            'staff' => $staff,
            'pageTitle' => 'Staff',
        ]);
    }

    public function topten(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        // Get top 10 torrents by various metrics
        $topSeeded = Database::fetchAll(
            "SELECT * FROM torrents WHERE visible = 'yes' ORDER BY seeders DESC LIMIT 10"
        );

        $topDownloaded = Database::fetchAll(
            "SELECT * FROM torrents WHERE visible = 'yes' ORDER BY times_completed DESC LIMIT 10"
        );

        $topRated = Database::fetchAll(
            "SELECT *, (ratingsum / NULLIF(numratings, 0)) as rating 
             FROM torrents 
             WHERE visible = 'yes' AND numratings >= 5 
             ORDER BY rating DESC LIMIT 10"
        );

        return ResponseHelper::view('pages/topten', [
            'user' => $user,
            'topSeeded' => $topSeeded,
            'topDownloaded' => $topDownloaded,
            'topRated' => $topRated,
            'pageTitle' => 'Top 10',
        ]);
    }

    public function donate(Request $request)
    {
        return ResponseHelper::view('pages/donate', [
            'user' => Auth::user(),
            'pageTitle' => 'Donate',
        ]);
    }

    public function links(Request $request)
    {
        return ResponseHelper::view('pages/links', [
            'user' => Auth::user(),
            'pageTitle' => 'Links',
        ]);
    }

    public function tags(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        return ResponseHelper::view('pages/tags', [
            'user' => $user,
            'testInput' => $request->request->get('test', ''),
            'pageTitle' => 'BBCode Tags',
        ]);
    }

    public function formats(Request $request)
    {
        return ResponseHelper::view('pages/formats', [
            'user' => Auth::user(),
            'pageTitle' => 'File Formats',
        ]);
    }

    public function videoFormats(Request $request)
    {
        return ResponseHelper::view('pages/videoformats', [
            'user' => Auth::user(),
            'pageTitle' => 'Video Formats',
        ]);
    }

    public function userAgreement(Request $request)
    {
        return ResponseHelper::view('pages/useragreement', [
            'user' => Auth::user(),
            'pageTitle' => 'User Agreement',
        ]);
    }
}
