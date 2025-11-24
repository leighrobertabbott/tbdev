<?php

namespace App\Services;

use App\Core\Database;

class QueueService
{
    /**
     * Push a job to the queue
     */
    public static function push(string $queue, string $jobClass, array $data = [], int $delay = 0): int
    {
        $payload = json_encode([
            'job' => $jobClass,
            'data' => $data,
        ]);

        $availableAt = time() + $delay;

        Database::execute(
            "INSERT INTO jobs (queue, payload, attempts, available_at, created_at) 
             VALUES (:queue, :payload, 0, :available_at, :created_at)",
            [
                'queue' => $queue,
                'payload' => $payload,
                'available_at' => $availableAt,
                'created_at' => time(),
            ]
        );

        return (int) Database::lastInsertId();
    }

    /**
     * Get next job from queue
     */
    public static function pop(string $queue = 'default', int $timeout = 60): ?array
    {
        $now = time();
        
        // Get next available job (without FOR UPDATE for compatibility)
        $job = Database::fetchOne(
            "SELECT * FROM jobs 
             WHERE queue = :queue 
             AND available_at <= :now 
             AND (reserved_at IS NULL OR reserved_at < :timeout)
             ORDER BY id ASC 
             LIMIT 1",
            [
                'queue' => $queue,
                'now' => $now,
                'timeout' => $now - $timeout,
            ]
        );

        if (!$job) {
            return null;
        }

        // Reserve the job
        Database::execute(
            "UPDATE jobs SET reserved_at = :now, attempts = attempts + 1 WHERE id = :id",
            ['id' => $job['id'], 'now' => $now]
        );

        return $job;
    }

    /**
     * Mark job as completed
     */
    public static function complete(int $jobId): void
    {
        Database::execute("DELETE FROM jobs WHERE id = :id", ['id' => $jobId]);
    }

    /**
     * Mark job as failed
     */
    public static function fail(int $jobId, string $exception): void
    {
        $job = Database::fetchOne("SELECT * FROM jobs WHERE id = :id", ['id' => $jobId]);
        
        if ($job) {
            // Move to failed_jobs
            Database::execute(
                "INSERT INTO failed_jobs (queue, payload, exception, failed_at) 
                 VALUES (:queue, :payload, :exception, :failed_at)",
                [
                    'queue' => $job['queue'],
                    'payload' => $job['payload'],
                    'exception' => $exception,
                    'failed_at' => time(),
                ]
            );

            // Delete from jobs
            Database::execute("DELETE FROM jobs WHERE id = :id", ['id' => $jobId]);
        }
    }

    /**
     * Release job back to queue
     */
    public static function release(int $jobId, int $delay = 0): void
    {
        $availableAt = time() + $delay;
        Database::execute(
            "UPDATE jobs SET reserved_at = NULL, available_at = :available_at WHERE id = :id",
            ['id' => $jobId, 'available_at' => $availableAt]
        );
    }

    /**
     * Get queue size
     */
    public static function size(string $queue = 'default'): int
    {
        $result = Database::fetchOne(
            "SELECT COUNT(*) as count FROM jobs WHERE queue = :queue AND available_at <= :now",
            ['queue' => $queue, 'now' => time()]
        );
        return (int) ($result['count'] ?? 0);
    }
}

