<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    use AuthorizesRequests;
    
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display today's tasks.
     */
    public function index(): View
    {
        $tasks = $this->taskService->getTodaysTasks(auth()->user());
        
        return view('tasks-new', compact('tasks'));
    }

    /**
     * Store a newly created task.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        try {
            $task = $this->taskService->createTask(
                auth()->user(),
                $request->validated()
            );
            
            return response()->json($task->load('category'), 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create task. Please try again.'
            ], 500);
        }
    }

    /**
     * Update the specified task.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        try {
            $updatedTask = $this->taskService->updateTask(
                $task,
                $request->validated()
            );
            
            return response()->json($updatedTask->load('category'));
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update task. Please try again.'
            ], 500);
        }
    }

    /**
     * Toggle task completion status.
     */
    public function toggle(Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        try {
            $updatedTask = $this->taskService->toggleTask($task);
            
            return response()->json([
                'success' => true,
                'task' => $updatedTask
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to toggle task. Please try again.'
            ], 500);
        }
    }

    /**
     * Reschedule task to tomorrow.
     */
    public function reschedule(Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        try {
            $updatedTask = $this->taskService->rescheduleToTomorrow($task);
            
            return response()->json([
                'success' => true,
                'task' => $updatedTask
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to reschedule task. Please try again.'
            ], 500);
        }
    }

    /**
     * Delete a task.
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        try {
            $this->taskService->deleteTask($task);
            
            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete task. Please try again.'
            ], 500);
        }
    }
}