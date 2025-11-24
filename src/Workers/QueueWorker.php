<?php

namespace App\Workers;

use App\Services\QueueService;
use App\Core\Database;

class QueueWorker
{
    private bool $running = true;
    private string $queue;

    public function __construct(string $queue = 'default')
    {
        $this->queue = $queue;
    }

    public function start(): void
    {
        pcntl_signal(SIGTERM, [$this, 'stop']);
        pcntl_signal(SIGINT, [$this, 'stop']);

        echo "Queue worker started on queue: {$this->queue}\n";

        while ($this->running) {
            pcntl_signal_dispatch();

            $job = QueueService::pop($this->queue);

            if (!$job) {
                sleep(1);
                continue;
            }

            try {
                $this->processJob($job);
                QueueService::complete($job['id']);
            } catch (\Exception $e) {
                error_log("Job failed: " . $e->getMessage());
                QueueService::fail($job['id'], $e->getMessage());
            }
        }

        echo "Queue worker stopped\n";
    }

    private function processJob(array $job): void
    {
        $payload = json_decode($job['payload'], true);
        
        if (!isset($payload['job'])) {
            throw new \Exception('Invalid job payload');
        }

        $jobClass = $payload['job'];
        $data = $payload['data'] ?? [];

        if (!class_exists($jobClass)) {
            throw new \Exception("Job class not found: {$jobClass}");
        }

        $jobInstance = new $jobClass();
        
        if (!method_exists($jobInstance, 'handle')) {
            throw new \Exception("Job class missing handle method: {$jobClass}");
        }

        $jobInstance->handle($data);
    }

    public function stop(): void
    {
        $this->running = false;
    }
}

