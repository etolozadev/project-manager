<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request)
    {
        Task::create([
            ...$request->validated(),
            'order' => Task::where('project_id', $request->project_id)
                           ->where('status', $request->status)
                           ->max('order') + 1,
        ]);

        return redirect()->route('projects.show', $request->project_id)
            ->with('success', 'Tarea creada correctamente.');
    }

    public function edit(Task $task)
    {
        $task->load('project');

        return Inertia::render('Tasks/Edit', [
            'task' => [
                'id'              => $task->id,
                'project_id'      => $task->project_id,
                'project_name'    => $task->project->name,
                'title'           => $task->title,
                'description'     => $task->description,
                'category'        => $task->category,
                'status'          => $task->status,
                'priority'        => $task->priority,
                'due_date'        => $task->due_date?->format('Y-m-d'),
                'estimated_hours' => $task->estimated_hours,
                'actual_hours'    => $task->actual_hours,
                'notes'           => $task->notes,
            ],
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        return redirect()->route('projects.show', $task->project_id)
            ->with('success', 'Tarea actualizada correctamente.');
    }

    public function destroy(Task $task)
    {
        $projectId = $task->project_id;
        $task->delete();

        return redirect()->route('projects.show', $projectId)
            ->with('success', 'Tarea eliminada correctamente.');
    }

    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:backlog,in_progress,review,done'],
        ]);

        $task->update(['status' => $request->status]);
        $task->load('project');

        return response()->json([
            'success'  => true,
            'progress' => $task->project->progress,
        ]);
    }
}
