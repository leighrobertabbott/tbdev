<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Models\Forum;
use App\Models\Topic;
use App\Models\Post;
use Symfony\Component\HttpFoundation\Request;

class ForumController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $userClass = $user['class'] ?? 0;
        $sections = Forum::getSectionsWithForums($userClass);

        return ResponseHelper::view('forums/index', [
            'user' => $user,
            'sections' => $sections,
            'pageTitle' => 'Forums',
        ]);
    }

    public function show(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $forum = Forum::findById($id);
        if (!$forum) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Forum Not Found'], 404);
        }

        // Check permissions
        $userClass = $user['class'] ?? 0;
        if ($userClass < $forum['minclassread']) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $topics = Forum::getTopics($id, $perPage, $offset);
        $totalTopics = Forum::getTopicCount($id);
        $totalPages = ceil($totalTopics / $perPage);

        return ResponseHelper::view('forums/show', [
            'user' => $user,
            'forum' => $forum,
            'topics' => $topics,
            'page' => $page,
            'totalPages' => $totalPages,
            'pageTitle' => htmlspecialchars($forum['name']),
        ]);
    }

    public function topic(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $topic = Topic::findById($id);
        if (!$topic) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Topic Not Found'], 404);
        }

        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $posts = Topic::getPosts($id, $perPage, $offset);

        return ResponseHelper::view('forums/topic', [
            'user' => $user,
            'topic' => $topic,
            'posts' => $posts,
            'page' => $page,
            'pageTitle' => htmlspecialchars($topic['subject']),
        ]);
    }

    public function newTopic(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $forum = Forum::findById($id);
        if (!$forum) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Forum Not Found'], 404);
        }

        $userClass = $user['class'] ?? 0;
        if ($userClass < $forum['minclasscreate']) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        return ResponseHelper::view('forums/new-topic', [
            'user' => $user,
            'forum' => $forum,
            'pageTitle' => 'New Topic',
        ]);
    }

    public function createTopic(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $forum = Forum::findById($id);
        if (!$forum) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Forum Not Found'], 404);
        }

        $subject = Security::sanitizeInput($request->request->get('subject', ''));
        $body = Security::sanitizeInput($request->request->get('body', ''));

        if (empty($subject) || empty($body)) {
            return ResponseHelper::view('forums/new-topic', [
                'user' => $user,
                'forum' => $forum,
                'error' => 'Subject and body are required.',
                'pageTitle' => 'New Topic',
            ]);
        }

        $topicId = Topic::create([
            'forum' => $id,
            'author' => $user['id'],
            'subject' => $subject,
            'body' => $body,
        ]);

        return ResponseHelper::redirect("/topic/{$topicId}");
    }

    public function reply(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $topic = Topic::findById($id);
        if (!$topic) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Topic Not Found'], 404);
        }

        // Check if topic is locked (sticky field is used for locked in old schema, but we use locked)
        if (($topic['locked'] ?? 'no') === 'yes' && ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Topic Locked'], 403);
        }

        $body = Security::sanitizeInput($request->request->get('body', ''));

        if (empty($body)) {
            return ResponseHelper::redirect("/topic/{$id}");
        }

        Post::create([
            'topic' => $id,
            'author' => $user['id'],
            'body' => $body,
        ]);

        return ResponseHelper::redirect("/topic/{$id}");
    }
}

