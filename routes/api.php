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
});