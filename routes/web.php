<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Authentication routes
require __DIR__ . '/auth.php';

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Task routes (web interface)
Route::middleware('auth')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::post('/tasks/{task}/reschedule', [TaskController::class, 'reschedule'])->name('tasks.reschedule');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
});

// routes/web.php
Route::prefix('api')->middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', function (\Illuminate\Http\Request $request) {
        return $request->user()
            ->tasks()
            ->with('category')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'start' => $task->task_date->format('Y-m-d'),
                    'color' => $task->category->color ?? '#3b82f6',
                    'extendedProps' => [
                        'priority' => $task->priority,
                        'is_done' => $task->is_done
                    ]
                ];
            });
    });
});