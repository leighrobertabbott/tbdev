<?php

namespace App\Models;

use App\Core\Database;

class Post
{
    public static function findById(int $id): ?array
    {
        $sql = "SELECT p.*, u.username, u.class, t.forumid as forum, t.subject as topic_subject
                FROM posts p
                LEFT JOIN users u ON p.userid = u.id
                LEFT JOIN topics t ON p.topicid = t.id
                WHERE p.id = :id";
        return Database::fetchOne($sql, ['id' => $id]);
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO posts (topicid, userid, added, body) 
                VALUES (:topicid, :userid, :added, :body)";
        
        $params = [
            'topicid' => $data['topic'],
            'userid' => $data['author'],
            'added' => time(),
            'body' => $data['body'],
        ];

        Database::execute($sql, $params);
        $postId = (int) Database::lastInsertId();

        // Update topic last post
        Topic::updateLastPost($data['topic']);

        // Update forum stats
        $topic = Topic::findById($data['topic']);
        if ($topic && isset($topic['forumid'])) {
            Database::execute(
                "UPDATE forums SET postcount = postcount + 1 WHERE id = :id",
                ['id' => $topic['forumid']]
            );
        }

        return $postId;
    }

    public static function update(int $id, string $body, int $editedBy): bool
    {
        return Database::execute(
            "UPDATE posts SET body = :body, editedby = :editedby, editedat = :editedat WHERE id = :id",
            [
                'id' => $id,
                'body' => $body,
                'editedby' => $editedBy,
                'editedat' => time(),
            ]
        ) > 0;
    }

    public static function delete(int $id): bool
    {
        $post = self::findById($id);
        if (!$post) {
            return false;
        }

        Database::execute("DELETE FROM posts WHERE id = :id", ['id' => $id]);

        // Update forum stats
        $topic = Topic::findById($post['topic'] ?? 0);
        if ($topic && isset($topic['forumid'])) {
            Database::execute(
                "UPDATE forums SET postcount = postcount - 1 WHERE id = :id",
                ['id' => $topic['forumid']]
            );
        }

        return true;
    }
}


