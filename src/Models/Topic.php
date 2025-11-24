<?php

namespace App\Models;

use App\Core\Database;

class Topic
{
    public static function findById(int $id): ?array
    {
        $sql = "SELECT t.*, u.username as author_name, u.class as author_class, f.name as forum_name
                FROM topics t
                LEFT JOIN users u ON t.userid = u.id
                LEFT JOIN forums f ON t.forumid = f.id
                WHERE t.id = :id";
        return Database::fetchOne($sql, ['id' => $id]);
    }

    public static function getPosts(int $topicId, int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT p.*, u.username, u.class, u.avatar
                FROM posts p
                LEFT JOIN users u ON p.userid = u.id
                WHERE p.topicid = :topic_id
                ORDER BY p.added ASC
                LIMIT :limit OFFSET :offset";
        
        return Database::fetchAll($sql, [
            'topic_id' => $topicId,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    public static function create(array $data): int
    {
        $now = time();
        $sql = "INSERT INTO topics (forumid, userid, subject, lastpost, sticky, locked) 
                VALUES (:forumid, :userid, :subject, :lastpost, :sticky, :locked)";
        
        $params = [
            'forumid' => $data['forum'],
            'userid' => $data['author'],
            'subject' => $data['subject'],
            'lastpost' => $now,
            'sticky' => $data['pinned'] ?? 'no',
            'locked' => $data['locked'] ?? 'no',
        ];

        Database::execute($sql, $params);
        $topicId = (int) Database::lastInsertId();

        // Create first post
        Post::create([
            'topic' => $topicId,
            'author' => $data['author'],
            'body' => $data['body'],
        ]);

        // Update forum stats
        Database::execute(
            "UPDATE forums SET topiccount = topiccount + 1 WHERE id = :id",
            ['id' => $data['forum']]
        );

        return $topicId;
    }

    public static function updateLastPost(int $topicId): void
    {
        Database::execute(
            "UPDATE topics SET lastpost = :time WHERE id = :id",
            ['id' => $topicId, 'time' => time()]
        );
    }
}


