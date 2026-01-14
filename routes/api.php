<?php

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * API Routes - For external integrations and calendar features
 * All routes require Sanctum authentication
 */

Route::middleware('auth:sanctum')->group(function () {
    
    // Get user information
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Get user by phone number (for GekyChat integration)
    Route::get('/users/by-phone', function (Request $request) {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $user = \App\Models\User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found with this phone number'
            ], 404);
        }

        // Generate API token if not exists
        if (!$user->currentAccessToken()) {
            $token = $user->createToken('gekychat-integration')->plainTextToken;
        } else {
            $token = $user->currentAccessToken()->token;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'api_token' => $token
            ]
        ]);
    });

    // Get tasks (formatted for calendar/API consumption)
    Route::get('/tasks', function (Request $request) {
        $startDate = $request->query('start');
        $endDate = $request->query('end');
        
        $query = $request->user()->tasks()->with('category');
        
        if ($startDate) {
            $query->where('task_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('task_date', '<=', $endDate);
        }
        
        return $query->get()->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->task_date->format('Y-m-d'),
                'color' => $task->category->color ?? '#3b82f6',
                'allDay' => true,
                'extendedProps' => [
                    'priority' => $task->priority,
                    'is_done' => $task->is_done,
                    'category' => $task->category?->name,
                ]
            ];
        });
    });

    // Get task statistics
    Route::get('/tasks/statistics', function (Request $request) {
        $user = $request->user();
        
        return [
            'total' => $user->tasks()->count(),
            'completed' => $user->tasks()->where('is_done', true)->count(),
            'pending' => $user->tasks()->where('is_done', false)->count(),
            'overdue' => $user->tasks()
                ->where('is_done', false)
                ->where('task_date', '<', today())
                ->count(),
        ];
    });

    // Get categories
    Route::get('/categories', function (Request $request) {
        return $request->user()->categories;
    });

    // Create task via API (for external integrations)
    Route::post('/tasks', function (Request $request) {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'task_date' => ['nullable', 'date'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'priority' => ['nullable', 'integer', 'between:0,2'],
            'reminder_at' => ['nullable', 'date'],
        ]);

        // Set default task_date to today if not provided
        if (!isset($validated['task_date'])) {
            $validated['task_date'] = today()->toDateString();
        }

        // Set default priority if not provided
        if (!isset($validated['priority'])) {
            $validated['priority'] = 1; // Medium priority
        }

        $task = $request->user()->tasks()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'task' => $task->load('category')
        ], 201);
    });

    // Update task via API (for external integrations)
    Route::patch('/tasks/{task}', function (Request $request, \App\Models\Task $task) {
        // Ensure task belongs to authenticated user
        if ($task->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'task_date' => ['sometimes', 'date'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'priority' => ['sometimes', 'integer', 'between:0,2'],
            'reminder_at' => ['nullable', 'date'],
            'is_done' => ['sometimes', 'boolean'],
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'task' => $task->load('category')
        ]);
    });

    // Delete task via API (for external integrations)
    Route::delete('/tasks/{task}', function (Request $request, \App\Models\Task $task) {
        // Ensure task belongs to authenticated user
        if ($task->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    });
});