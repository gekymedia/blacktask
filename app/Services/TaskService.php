<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class TaskService
{
    /**
     * Get tasks for a specific date for a user.
     */
    public function getTasksForDate(User $user, Carbon $date): Collection
    {
        return $user->tasks()
            ->with('category')
            ->whereDate('task_date', $date)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get today's tasks for a user.
     */
    public function getTodaysTasks(User $user): Collection
    {
        return $this->getTasksForDate($user, today());
    }

    /**
     * Create a new task for a user.
     */
    public function createTask(User $user, array $data): Task
    {
        // Ensure task_date is set
        if (!isset($data['task_date'])) {
            $data['task_date'] = today();
        }

        // Validate category belongs to user if provided
        if (isset($data['category_id'])) {
            $category = $user->categories()->find($data['category_id']);
            if (!$category) {
                throw new \InvalidArgumentException('Category does not belong to user');
            }
        }

        return $user->tasks()->create($data);
    }

    /**
     * Update an existing task.
     */
    public function updateTask(Task $task, array $data): Task
    {
        // Validate category belongs to user if being changed
        if (isset($data['category_id'])) {
            $category = $task->user->categories()->find($data['category_id']);
            if (!$category && $data['category_id'] !== null) {
                throw new \InvalidArgumentException('Category does not belong to user');
            }
        }

        $task->update($data);
        return $task->fresh();
    }

    /**
     * Toggle task completion status.
     */
    public function toggleTask(Task $task): Task
    {
        $wasCompleted = $task->is_done;
        $task->update(['is_done' => !$wasCompleted]);

        // Handle recurrence if task was just completed
        if (!$wasCompleted && $task->recurrence) {
            $this->createRecurringTask($task);
        }

        return $task->fresh();
    }

    /**
     * Reschedule a task to a new date.
     */
    public function rescheduleTask(Task $task, Carbon $newDate): Task
    {
        $task->update(['task_date' => $newDate]);
        return $task->fresh();
    }

    /**
     * Reschedule a task to tomorrow.
     */
    public function rescheduleToTomorrow(Task $task): Task
    {
        return $this->rescheduleTask($task, today()->addDay());
    }

    /**
     * Delete a task.
     */
    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }

    /**
     * Create a recurring task based on recurrence rules.
     */
    protected function createRecurringTask(Task $originalTask): ?Task
    {
        $newTask = $originalTask->replicate();
        $newTask->is_done = false;

        // Calculate next date based on recurrence type
        $nextDate = match ($originalTask->recurrence) {
            'daily' => $originalTask->task_date->addDay(),
            'weekly' => $originalTask->task_date->addWeek(),
            'monthly' => $originalTask->task_date->addMonth(),
            'yearly' => $originalTask->task_date->addYear(),
            default => null,
        };

        if (!$nextDate) {
            return null;
        }

        // Check if we should create the next occurrence
        if ($originalTask->recurrence_ends_at && $nextDate->isAfter($originalTask->recurrence_ends_at)) {
            return null;
        }

        $newTask->task_date = $nextDate;
        $newTask->save();

        return $newTask;
    }

    /**
     * Get task statistics for a user.
     */
    public function getTaskStatistics(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = $user->tasks();

        if ($startDate) {
            $query->where('task_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('task_date', '<=', $endDate);
        }

        $total = $query->count();
        $completed = (clone $query)->where('is_done', true)->count();
        $pending = $total - $completed;
        $completionRate = $total > 0 ? round(($completed / $total) * 100, 2) : 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'completion_rate' => $completionRate,
        ];
    }

    /**
     * Get overdue tasks for a user.
     */
    public function getOverdueTasks(User $user): Collection
    {
        return $user->tasks()
            ->with('category')
            ->where('is_done', false)
            ->where('task_date', '<', today())
            ->orderBy('task_date', 'asc')
            ->get();
    }

    /**
     * Get upcoming tasks for a user.
     */
    public function getUpcomingTasks(User $user, int $days = 7): Collection
    {
        return $user->tasks()
            ->with('category')
            ->where('is_done', false)
            ->whereBetween('task_date', [today()->addDay(), today()->addDays($days)])
            ->orderBy('task_date', 'asc')
            ->get();
    }
}

