// routes/api.php
Route::get('/tasks', function() {
    return auth()->user()->tasks()->get()->map(function($task) {
        return [
            'id' => $task->id,
            'title' => $task->title,
            'start' => $task->task_date,
            'color' => $task->category->color ?? '#3b82f6',
            'extendedProps' => [
                'priority' => $task->priority
            ]
        ];
    });
})->middleware('auth:sanctum');