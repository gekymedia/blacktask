<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskRecurrenceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected TaskService $taskService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->taskService = app(TaskService::class);
    }

    public function test_daily_recurring_task_creates_next_occurrence(): void
    {
        $task = Task::factory()->for($this->user)->create([
            'is_done' => false,
            'recurrence' => 'daily',
            'task_date' => today(),
        ]);

        $this->taskService->toggleTask($task);

        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->user->id,
            'title' => $task->title,
            'task_date' => today()->addDay()->toDateString(),
            'is_done' => false,
        ]);
    }

    public function test_weekly_recurring_task_creates_next_occurrence(): void
    {
        $task = Task::factory()->for($this->user)->create([
            'is_done' => false,
            'recurrence' => 'weekly',
            'task_date' => today(),
        ]);

        $this->taskService->toggleTask($task);

        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->user->id,
            'title' => $task->title,
            'task_date' => today()->addWeek()->toDateString(),
            'is_done' => false,
        ]);
    }

    public function test_monthly_recurring_task_creates_next_occurrence(): void
    {
        $task = Task::factory()->for($this->user)->create([
            'is_done' => false,
            'recurrence' => 'monthly',
            'task_date' => today(),
        ]);

        $this->taskService->toggleTask($task);

        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->user->id,
            'title' => $task->title,
            'task_date' => today()->addMonth()->toDateString(),
            'is_done' => false,
        ]);
    }

    public function test_recurring_task_respects_end_date(): void
    {
        $task = Task::factory()->for($this->user)->create([
            'is_done' => false,
            'recurrence' => 'daily',
            'task_date' => today(),
            'recurrence_ends_at' => today()->addDays(2),
        ]);

        // Complete task - should create next occurrence
        $this->taskService->toggleTask($task);
        $this->assertEquals(2, Task::where('user_id', $this->user->id)->count());

        // Complete the next occurrence - should create another
        $nextTask = Task::where('user_id', $this->user->id)
            ->where('task_date', today()->addDay())
            ->first();
        $this->taskService->toggleTask($nextTask);
        $this->assertEquals(3, Task::where('user_id', $this->user->id)->count());

        // Complete the last occurrence - should NOT create another (past end date)
        $lastTask = Task::where('user_id', $this->user->id)
            ->where('task_date', today()->addDays(2))
            ->first();
        $this->taskService->toggleTask($lastTask);
        $this->assertEquals(3, Task::where('user_id', $this->user->id)->count());
    }

    public function test_non_recurring_task_does_not_create_duplicate(): void
    {
        $task = Task::factory()->for($this->user)->create([
            'is_done' => false,
            'recurrence' => null,
            'task_date' => today(),
        ]);

        $this->taskService->toggleTask($task);

        $this->assertEquals(1, Task::where('user_id', $this->user->id)->count());
    }

    public function test_uncompleting_task_does_not_create_recurrence(): void
    {
        $task = Task::factory()->for($this->user)->create([
            'is_done' => true,
            'recurrence' => 'daily',
            'task_date' => today(),
        ]);

        $this->taskService->toggleTask($task);

        $this->assertEquals(1, Task::where('user_id', $this->user->id)->count());
        $this->assertFalse($task->fresh()->is_done);
    }
}

