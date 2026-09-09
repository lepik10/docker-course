<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ProcessTask;

class TaskController extends Controller
{
    public function index()
    {
        return Cache::remember('tasks', 60, function () {
            return Task::all()->toArray();
        });
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task = Task::create($validated);

        Cache::forget('tasks');

        ProcessTask::dispatch($task);

        return $task;
    }

    public function show(Task $task)
    {
        return $task;
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'is_completed' => 'sometimes|boolean',
        ]);

        $task->update($validated);

        Cache::forget('tasks');

        return $task;
    }

    public function destroy(Task $task)
    {
        $task->delete();

        Cache::forget('tasks');

        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }
}