<?php

namespace App\Models;

use App\Core\Database;

class Forum
{
    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM forums ORDER BY sort ASC, id ASC");
    }

    public static function findById(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM forums WHERE id = :id", ['id' => $id]);
    }

    /**
     * Get all forum sections with their forums and subforums
     */
    public static function getSectionsWithForums(int $userClass = 0): array
    {
        $sections = Database::fetchAll(
            "SELECT * FROM forum_sections 
             WHERE minclassread <= :user_class 
             ORDER BY sort_order ASC, id ASC",
            ['user_class' => $userClass]
        );

        foreach ($sections as &$section) {
            // Get forums in this section (no parent)
            $section['forums'] = Database::fetchAll(
                "SELECT f.*,
                        (SELECT COUNT(*) FROM topics WHERE forumid = f.id) as topiccount,
                            (SELECT COUNT(*) FROM posts p 
                             INNER JOIN topics t ON p.topicid = t.id 
                             WHERE t.forumid = f.id) as postcount,
                        (SELECT t.lastpost FROM topics t 
                         WHERE t.forumid = f.id 
                         ORDER BY t.lastpost DESC LIMIT 1) as last_post_time,
                        (SELECT u.username FROM topics t 
                         INNER JOIN users u ON t.userid = u.id 
                         WHERE t.forumid = f.id 
                         ORDER BY t.lastpost DESC LIMIT 1) as last_post_user
                 FROM forums f
                 WHERE f.section_id = :section_id 
                   AND (f.parent_id IS NULL OR f.parent_id = 0)
                   AND f.minclassread <= :user_class
                 ORDER BY f.sort ASC, f.id ASC",
                [
                    'section_id' => $section['id'],
                    'user_class' => $userClass,
                ]
            );

            // Get subforums for each forum
            foreach ($section['forums'] as &$forum) {
                $forum['subforums'] = Database::fetchAll(
                    "SELECT f.*,
                            (SELECT COUNT(*) FROM topics WHERE forumid = f.id) as topiccount,
                            (SELECT COUNT(*) FROM posts p 
                             INNER JOIN topics t ON p.topicid = t.id 
                             WHERE t.forumid = f.id) as postcount,
                            (SELECT t.lastpost FROM topics t 
                             WHERE t.forumid = f.id 
                             ORDER BY t.lastpost DESC LIMIT 1) as last_post_time,
                            (SELECT u.username FROM topics t 
                             INNER JOIN users u ON t.userid = u.id 
                             WHERE t.forumid = f.id 
                             ORDER BY t.lastpost DESC LIMIT 1) as last_post_user
                     FROM forums f
                     WHERE f.parent_id = :parent_id
                       AND f.minclassread <= :user_class
                     ORDER BY f.sort ASC, f.id ASC",
                    [
                        'parent_id' => $forum['id'],
                        'user_class' => $userClass,
                    ]
                );
            }
        }

        return $sections;
    }

    public static function getTopics(int $forumId, int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT t.*, u.username as author_name, u.class as author_class,
                       COUNT(DISTINCT p.id) as post_count,
                       MAX(p.added) as last_post_time
                FROM topics t
                LEFT JOIN users u ON t.userid = u.id
                LEFT JOIN posts p ON t.id = p.topicid
                WHERE t.forumid = :forum_id
                GROUP BY t.id
                ORDER BY t.sticky DESC, t.lastpost DESC
                LIMIT :limit OFFSET :offset";
        
        return Database::fetchAll($sql, [
            'forum_id' => $forumId,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    public static function getTopicCount(int $forumId): int
    {
        $result = Database::fetchOne(
            "SELECT COUNT(*) as count FROM topics WHERE forumid = :forum_id",
            ['forum_id' => $forumId]
        );
        return (int) ($result['count'] ?? 0);
    }

    public static function getPostCount(int $forumId): int
    {
        $result = Database::fetchOne(
            "SELECT COUNT(*) as count FROM posts p 
             INNER JOIN topics t ON p.topicid = t.id 
             WHERE t.forumid = :forum_id",
            ['forum_id' => $forumId]
        );
        return (int) ($result['count'] ?? 0);
    }

    /**
     * Update forum statistics (topic count, post count, last post)
     */
    public static function updateStats(int $forumId): void
    {
        $topicCount = self::getTopicCount($forumId);
        $postCount = self::getPostCount($forumId);
        
        // Get last post info
        $lastPost = Database::fetchOne(
            "SELECT t.lastpost, t.userid 
             FROM topics t 
             WHERE t.forumid = :forum_id 
             ORDER BY t.lastpost DESC 
             LIMIT 1",
            ['forum_id' => $forumId]
        );

        $updateData = [
            'topiccount' => $topicCount,
            'postcount' => $postCount,
        ];

        if ($lastPost) {
            $updateData['last_post_time'] = $lastPost['lastpost'];
            $updateData['last_post_user'] = $lastPost['userid'];
        }

        $setClause = [];
        $params = ['id' => $forumId];
        foreach ($updateData as $key => $value) {
            $setClause[] = "{$key} = :{$key}";
            $params[$key] = $value;
        }

        Database::execute(
            "UPDATE forums SET " . implode(', ', $setClause) . " WHERE id = :id",
            $params
        );
    }
}


