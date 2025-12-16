<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display the dashboard with today's and tomorrow's tasks.
     */
    public function index(): View
    {
        $user = auth()->user();

        // Get today's tasks
        $todayTasks = $this->taskService->getTasksForDate($user, today());
        $todayUndone = $todayTasks->where('is_done', false);

        // Get tomorrow's tasks
        $tomorrowTasks = $this->taskService->getTasksForDate($user, today()->addDay());

        // Get overdue tasks
        $overdueTasks = $this->taskService->getOverdueTasks($user);

        return view('dashboard', compact(
            'todayTasks',
            'todayUndone',
            'tomorrowTasks',
            'overdueTasks'
        ));
    }
}

