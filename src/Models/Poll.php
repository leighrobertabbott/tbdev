<?php

namespace App\Models;

use App\Core\Database;

class Poll
{
    public static function all(string $status = 'active', int $limit = 10): array
    {
        $limit = max(1, min(100, (int) $limit)); // Sanitize limit
        
        if ($status === 'all') {
            $sql = "SELECT p.*, u.username as creator_name,
                           COUNT(DISTINCT po.id) as option_count
                    FROM polls p
                    LEFT JOIN users u ON p.created_by = u.id
                    LEFT JOIN poll_options po ON p.id = po.poll_id
                    GROUP BY p.id ORDER BY p.sort_order DESC, p.created_at DESC LIMIT " . (int) $limit;
            $params = [];
        } else {
            $sql = "SELECT p.*, u.username as creator_name,
                           COUNT(DISTINCT po.id) as option_count
                    FROM polls p
                    LEFT JOIN users u ON p.created_by = u.id
                    LEFT JOIN poll_options po ON p.id = po.poll_id
                    WHERE p.status = :status";
            
            if ($status === 'active') {
                $sql .= " AND (p.expires_at IS NULL OR p.expires_at > :now)";
                $params = ['status' => $status, 'now' => time()];
            } else {
                $params = ['status' => $status];
            }
            
            $sql .= " GROUP BY p.id ORDER BY p.sort_order DESC, p.created_at DESC LIMIT " . (int) $limit;
        }
        
        return Database::fetchAll($sql, $params);
    }

    public static function findById(int $id): ?array
    {
        $sql = "SELECT p.*, u.username as creator_name
                FROM polls p
                LEFT JOIN users u ON p.created_by = u.id
                WHERE p.id = :id";
        return Database::fetchOne($sql, ['id' => $id]);
    }

    public static function getOptions(int $pollId): array
    {
        return Database::fetchAll(
            "SELECT * FROM poll_options WHERE poll_id = :poll_id ORDER BY option_order ASC, id ASC",
            ['poll_id' => $pollId]
        );
    }

    public static function getResults(int $pollId): array
    {
        $options = self::getOptions($pollId);
        $poll = self::findById($pollId);
        $totalVotes = (int) ($poll['total_votes'] ?? 0);

        foreach ($options as &$option) {
            $option['percentage'] = $totalVotes > 0 
                ? round(($option['vote_count'] / $totalVotes) * 100, 1) 
                : 0;
        }

        return $options;
    }

    public static function hasVoted(int $pollId, int $userId): bool
    {
        $result = Database::fetchOne(
            "SELECT id FROM poll_votes WHERE poll_id = :poll_id AND user_id = :user_id LIMIT 1",
            ['poll_id' => $pollId, 'user_id' => $userId]
        );
        return $result !== null;
    }

    public static function getUserVotes(int $pollId, int $userId): array
    {
        return Database::fetchAll(
            "SELECT option_id FROM poll_votes WHERE poll_id = :poll_id AND user_id = :user_id",
            ['poll_id' => $pollId, 'user_id' => $userId]
        );
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO polls (question, description, created_by, created_at, expires_at, status, 
                allow_multiple, allow_change_vote, min_class_view, min_class_vote, min_class_create, 
                show_results_before_vote, sort_order) 
                VALUES (:question, :description, :created_by, :created_at, :expires_at, :status,
                :allow_multiple, :allow_change_vote, :min_class_view, :min_class_vote, :min_class_create,
                :show_results_before_vote, :sort_order)";
        
        $params = [
            'question' => $data['question'],
            'description' => $data['description'] ?? null,
            'created_by' => $data['created_by'],
            'created_at' => time(),
            'expires_at' => $data['expires_at'] ?? null,
            'status' => $data['status'] ?? 'active',
            'allow_multiple' => $data['allow_multiple'] ?? 0,
            'allow_change_vote' => $data['allow_change_vote'] ?? 0,
            'min_class_view' => $data['min_class_view'] ?? 0,
            'min_class_vote' => $data['min_class_vote'] ?? 0,
            'min_class_create' => $data['min_class_create'] ?? 4,
            'show_results_before_vote' => $data['show_results_before_vote'] ?? 0,
            'sort_order' => $data['sort_order'] ?? 0,
        ];

        Database::execute($sql, $params);
        $pollId = (int) Database::lastInsertId();

        // Create options
        if (isset($data['options']) && is_array($data['options'])) {
            foreach ($data['options'] as $index => $optionText) {
                $optionText = is_string($optionText) ? trim($optionText) : '';
                if (!empty($optionText)) {
                    try {
                        PollOption::create([
                            'poll_id' => $pollId,
                            'option_text' => $optionText,
                            'option_order' => $index,
                        ]);
                    } catch (\Exception $e) {
                        error_log('Failed to create poll option: ' . $e->getMessage());
                    }
                }
            }
        }

        return $pollId;
    }

    public static function vote(int $pollId, int $userId, array $optionIds): bool
    {
        $poll = self::findById($pollId);
        if (!$poll) {
            return false;
        }

        // Check if already voted
        $hasVoted = self::hasVoted($pollId, $userId);
        
        if ($hasVoted && !$poll['allow_change_vote']) {
            return false;
        }

        // If changing vote, remove old votes
        if ($hasVoted && $poll['allow_change_vote']) {
            $oldVotes = self::getUserVotes($pollId, $userId);
            foreach ($oldVotes as $oldVote) {
                Database::execute(
                    "DELETE FROM poll_votes WHERE poll_id = :poll_id AND user_id = :user_id AND option_id = :option_id",
                    ['poll_id' => $pollId, 'user_id' => $userId, 'option_id' => $oldVote['option_id']]
                );
                
                // Decrement option vote count
                Database::execute(
                    "UPDATE poll_options SET vote_count = vote_count - 1 WHERE id = :id",
                    ['id' => $oldVote['option_id']]
                );
            }
            
            // Update total votes
            Database::execute(
                "UPDATE polls SET total_votes = total_votes - :count WHERE id = :id",
                ['id' => $pollId, 'count' => count($oldVotes)]
            );
        }

        // Add new votes
        $voteCount = 0;
        foreach ($optionIds as $optionId) {
            // Check if already voted for this option
            $existing = Database::fetchOne(
                "SELECT id FROM poll_votes WHERE poll_id = :poll_id AND user_id = :user_id AND option_id = :option_id",
                ['poll_id' => $pollId, 'user_id' => $userId, 'option_id' => $optionId]
            );

            if (!$existing) {
                try {
                    Database::execute(
                        "INSERT INTO poll_votes (poll_id, user_id, option_id, voted_at) 
                         VALUES (:poll_id, :user_id, :option_id, :voted_at)",
                        [
                            'poll_id' => $pollId,
                            'user_id' => $userId,
                            'option_id' => $optionId,
                            'voted_at' => time(),
                        ]
                    );

                    // Increment option vote count
                    Database::execute(
                        "UPDATE poll_options SET vote_count = vote_count + 1 WHERE id = :id",
                        ['id' => $optionId]
                    );

                    $voteCount++;
                } catch (\Exception $e) {
                    error_log('Failed to record vote: ' . $e->getMessage());
                    // Continue with other options even if one fails
                }
            }
        }

        // Update poll total votes
        if ($voteCount > 0) {
            try {
                Database::execute(
                    "UPDATE polls SET total_votes = total_votes + :count WHERE id = :id",
                    ['id' => $pollId, 'count' => $voteCount]
                );
            } catch (\Exception $e) {
                error_log('Failed to update poll total votes: ' . $e->getMessage());
            }
        }

        return $voteCount > 0;
    }

    public static function update(int $id, array $data): bool
    {
        $allowed = ['question', 'description', 'status', 'expires_at', 'sort_order'];
        $updates = [];

        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = :$field";
            }
        }

        if (empty($updates)) {
            return false;
        }

        $sql = "UPDATE polls SET " . implode(', ', $updates) . " WHERE id = :id";
        $data['id'] = $id;
        
        return Database::execute($sql, $data) > 0;
    }

    public static function delete(int $id): bool
    {
        return Database::execute("DELETE FROM polls WHERE id = :id", ['id' => $id]) > 0;
    }

    public static function close(int $id): bool
    {
        return Database::execute(
            "UPDATE polls SET status = 'closed' WHERE id = :id",
            ['id' => $id]
        ) > 0;
    }

    public static function archive(int $id): bool
    {
        return Database::execute(
            "UPDATE polls SET status = 'archived' WHERE id = :id",
            ['id' => $id]
        ) > 0;
    }
}

