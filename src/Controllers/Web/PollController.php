<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Core\Database;
use App\Models\Poll;
use Symfony\Component\HttpFoundation\Request;

class PollController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $status = $request->query->get('status', 'active');
        $polls = Poll::all($status, 20);

        return ResponseHelper::view('polls/index', [
            'user' => $user,
            'polls' => $polls,
            'status' => $status,
            'pageTitle' => 'Polls',
        ]);
    }

    public function show(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $poll = Poll::findById($id);
        if (!$poll) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Poll Not Found'], 404);
        }

        // Check view permissions
        if (($user['class'] ?? 0) < $poll['min_class_view']) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $options = Poll::getOptions($id);
        $hasVoted = Poll::hasVoted($id, $user['id']);
        $userVotes = $hasVoted ? Poll::getUserVotes($id, $user['id']) : [];
        $results = Poll::getResults($id);

        // Check if expired
        $isExpired = $poll['expires_at'] && $poll['expires_at'] < time();
        $canVote = !$isExpired && $poll['status'] === 'active' && 
                   (($user['class'] ?? 0) >= $poll['min_class_vote']) &&
                   (!$hasVoted || $poll['allow_change_vote']);

        return ResponseHelper::view('polls/show', [
            'user' => $user,
            'poll' => $poll,
            'options' => $options,
            'hasVoted' => $hasVoted,
            'userVotes' => $userVotes,
            'results' => $results,
            'canVote' => $canVote,
            'isExpired' => $isExpired,
            'showResults' => $hasVoted || $poll['show_results_before_vote'] || $isExpired,
            'pageTitle' => htmlspecialchars($poll['question']),
        ]);
    }

    public function vote(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $poll = Poll::findById($id);
        if (!$poll) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Poll Not Found'], 404);
        }

        // Check permissions
        if (($user['class'] ?? 0) < $poll['min_class_vote']) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        // Check if expired
        if ($poll['expires_at'] && $poll['expires_at'] < time()) {
            return ResponseHelper::redirect("/poll/{$id}");
        }

        // Get selected options
        $optionIds = $request->request->all()['options'] ?? [];
        if (!is_array($optionIds)) {
            $optionIds = [$optionIds];
        }
        $optionIds = array_map('intval', array_filter($optionIds));

        if (empty($optionIds)) {
            return ResponseHelper::view('polls/show', [
                'user' => $user,
                'poll' => $poll,
                'options' => Poll::getOptions($id),
                'error' => 'Please select at least one option.',
                'pageTitle' => htmlspecialchars($poll['question']),
            ]);
        }

        // Check multiple votes allowed
        if (!$poll['allow_multiple'] && count($optionIds) > 1) {
            return ResponseHelper::view('polls/show', [
                'user' => $user,
                'poll' => $poll,
                'options' => Poll::getOptions($id),
                'error' => 'This poll only allows one vote.',
                'pageTitle' => htmlspecialchars($poll['question']),
            ]);
        }

        // Check if already voted
        $hasVoted = Poll::hasVoted($id, $user['id']);
        if ($hasVoted && !$poll['allow_change_vote']) {
            return ResponseHelper::view('polls/show', [
                'user' => $user,
                'poll' => $poll,
                'options' => Poll::getOptions($id),
                'error' => 'You have already voted in this poll.',
                'pageTitle' => htmlspecialchars($poll['question']),
            ]);
        }

        // Cast vote
        if (Poll::vote($id, $user['id'], $optionIds)) {
            return ResponseHelper::redirect("/poll/{$id}?voted=1");
        }

        return ResponseHelper::redirect("/poll/{$id}");
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        if ($request->getMethod() === 'POST') {
            $question = Security::sanitizeInput($request
                ->request
                ->get('question', ''));
            $description = Security::sanitizeInput($request
                ->request
                ->get('description', ''));
            $options = $request->request->all()['options'] ?? [];
            if (!is_array($options)) {
                $options = [$options];
            }
            // Filter out empty options
            $options = array_filter(array_map('trim', $options));
            $expiresAt = $request->request->get('expires_at', '');
            $allowMultiple = $request->request->get('allow_multiple', 0);
            $allowChangeVote = $request->request->get('allow_change_vote', 0);
            $showResultsBeforeVote = $request
                ->request
                ->get('show_results_before_vote', 0);

            if (empty($question) || empty($options) || count(array_filter($options)) < 2) {
                return ResponseHelper::view('polls/create', [
                    'user' => $user,
                    'error' => 'Question and at least 2 options are required.',
                    'pageTitle' => 'Create Poll',
                ]);
            }

            $expiresTimestamp = null;
            if (!empty($expiresAt)) {
                $expiresTimestamp = strtotime($expiresAt);
                if ($expiresTimestamp === false) {
                    $expiresTimestamp = null;
                }
            }

            $pollId = Poll::create([
                'question' => $question,
                'description' => $description,
                'created_by' => $user['id'],
                'expires_at' => $expiresTimestamp,
                'allow_multiple' => $allowMultiple ? 1 : 0,
                'allow_change_vote' => $allowChangeVote ? 1 : 0,
                'show_results_before_vote' => $showResultsBeforeVote ? 1 : 0,
                'min_class_create' => 4,
                'options' => $options, // Pass options to create method
            ]);

            if ($pollId > 0) {
                return ResponseHelper::redirect("/poll/{$pollId}");
            } else {
                return ResponseHelper::view('polls/create', [
                    'user' => $user,
                    'error' => 'Failed to create poll. Please try again.',
                    'pageTitle' => 'Create Poll',
                ]);
            }
        }

        return ResponseHelper::view('polls/create', [
            'user' => $user,
            'pageTitle' => 'Create Poll',
        ]);
    }

    public function manage(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        // Get all polls regardless of status
        $allPolls = Database::fetchAll(
            "SELECT p.*, u.username as creator_name
             FROM polls p
             LEFT JOIN users u ON p.created_by = u.id
             ORDER BY p.created_at DESC LIMIT 100"
        );
        
        $polls = [];
        foreach ($allPolls as $poll) {
            $poll['total_votes'] = (int) ($poll['total_votes'] ?? 0);
            $polls[] = $poll;
        }

        return ResponseHelper::view('polls/manage', [
            'user' => $user,
            'polls' => $polls,
            'pageTitle' => 'Manage Polls',
        ]);
    }

    public function close(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        Poll::close($id);
        return ResponseHelper::redirect('/polls/manage');
    }

    public function delete(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        Poll::delete($id);
        return ResponseHelper::redirect('/polls/manage');
    }
}

