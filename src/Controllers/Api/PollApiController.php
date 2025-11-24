<?php

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Models\Poll;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PollApiController
{
    public function index(Request $request): JsonResponse
    {
        Auth::requireAuth();

        $status = $request->query->get('status', 'active');
        $polls = Poll::all($status, 20);

        return new JsonResponse(['data' => $polls]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        Auth::requireAuth();

        $poll = Poll::findById($id);
        if (!$poll) {
            return new JsonResponse(['error' => 'Poll not found'], 404);
        }

        $options = Poll::getOptions($id);
        $user = Auth::user();
        $hasVoted = Poll::hasVoted($id, $user['id']);
        $results = Poll::getResults($id);

        return new JsonResponse([
            'data' => [
                'poll' => $poll,
                'options' => $options,
                'has_voted' => $hasVoted,
                'results' => $results,
            ],
        ]);
    }

    public function vote(Request $request, int $id): JsonResponse
    {
        Auth::requireAuth();

        $user = Auth::user();
        $poll = Poll::findById($id);
        
        if (!$poll) {
            return new JsonResponse(['error' => 'Poll not found'], 404);
        }

        $optionIds = $request->request->get('options', []);
        if (!is_array($optionIds)) {
            $optionIds = [$optionIds];
        }
        $optionIds = array_map('intval', array_filter($optionIds));

        if (empty($optionIds)) {
            return new JsonResponse(['error' => 'No options selected'], 400);
        }

        if (Poll::vote($id, $user['id'], $optionIds)) {
            return new JsonResponse(['message' => 'Vote recorded', 'results' => Poll::getResults($id)]);
        }

        return new JsonResponse(['error' => 'Unable to record vote'], 400);
    }
}

