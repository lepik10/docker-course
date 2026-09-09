<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessTask implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = 10;

    public function __construct(
        public Task $task
    ) {
    }

    public function handle(): void
    {
        \Log::info('Обрабатываем Task: ' . $this->task->title);
    }
}