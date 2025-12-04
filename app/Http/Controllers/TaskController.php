<?php
// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    // app/Http/Controllers/TaskController.php
public function index()
{
    $tasks = auth()->user()->tasks()
                ->whereDate('task_date', today())
                ->latest()
                ->get();
                
    return view('tasks', compact('tasks'));
}

public function store(Request $request)
{
    $task = auth()->user()->tasks()->create([
        'title' => $request->title,
        'task_date' => today()
    ]);
    
    return response()->json($task, 201);
}
    // Toggle task completion status
  // app/Http/Controllers/TaskController.php
public function toggle(Task $task)
{
    if ($task->is_done && $task->recurrence) {
        // Clone the task for recurrence
        $newTask = $task->replicate();
        $newTask->is_done = false;
        
        switch($task->recurrence) {
            case 'daily':
                $newTask->task_date = $task->task_date->addDay();
                break;
            case 'weekly':
                $newTask->task_date = $task->task_date->addWeek();
                break;
            case 'monthly':
                $newTask->task_date = $task->task_date->addMonth();
                break;
            case 'yearly':
                $newTask->task_date = $task->task_date->addYear();
                break;
        }
        
        if (!$task->recurrence_ends_at || $newTask->task_date <= $task->recurrence_ends_at) {
            $newTask->save();
        }
    }
    
    $task->update(['is_done' => !$task->is_done]);
    
    return response()->json(['success' => true]);
}

    // Reschedule task to tomorrow
    public function reschedule(Task $task)
    {
        $task->update(['task_date' => today()->addDay()]);
        return response()->json(['success' => true]);
    }

    // Delete a task
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['success' => true]);
    }
}