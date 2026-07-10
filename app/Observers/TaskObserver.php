<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class TaskObserver
{
    public function saved(Task $task): void
    {
        Cache::forget('dashboard.stats');
    }

    public function deleted(Task $task): void
    {
        Cache::forget('dashboard.stats');
    }
}
