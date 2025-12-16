<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TaskService $taskService;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskService = app(TaskService::class);
        $this->user = User::factory()->create();
    }

    public function test_get_todays_tasks_returns_only_today_tasks(): void
    {
        Task::factory()->for($this->user)->create(['task_date' => today()]);
        Task::factory()->for($this->user)->create(['task_date' => today()]);
        Task::factory()->for($this->user)->create(['task_date' => today()->addDay()]);

        $tasks = $this->taskService->getTodaysTasks($this->user);

        $this->assertCount(2, $tasks);
    }

    public function test_create_task_sets_default_date(): void
    {
        $task = $this->taskService->createTask($this->user, [
            'title' => 'Test Task'
        ]);

        $this->assertEquals(today()->toDateString(), $task->task_date->toDateString());
    }

    public function test_create_task_validates_category_ownership(): void
    {
        $otherUser = User::factory()->create();
        $category = Category::factory()->for($otherUser)->create();

        $this->expectException(\InvalidArgumentException::class);

        $this->taskService->createTask($this->user, [
            'title' => 'Test Task',
            'category_id' => $category->id,
        ]);
    }

    public function test_update_task_validates_category_ownership(): void
    {
        $task = Task::factory()->for($this->user)->create();
        $otherUser = User::factory()->create();
        $category = Category::factory()->for($otherUser)->create();

        $this->expectException(\InvalidArgumentException::class);

        $this->taskService->updateTask($task, [
            'category_id' => $category->id,
        ]);
    }

    public function test_reschedule_to_tomorrow_updates_date(): void
    {
        $task = Task::factory()->for($this->user)->create([
            'task_date' => today()
        ]);

        $updatedTask = $this->taskService->rescheduleToTomorrow($task);

        $this->assertEquals(
            today()->addDay()->toDateString(),
            $updatedTask->task_date->toDateString()
        );
    }

    public function test_get_overdue_tasks_returns_past_incomplete_tasks(): void
    {
        Task::factory()->for($this->user)->create([
            'task_date' => today()->subDays(2),
            'is_done' => false,
        ]);
        Task::factory()->for($this->user)->create([
            'task_date' => today()->subDay(),
            'is_done' => false,
        ]);
        Task::factory()->for($this->user)->create([
            'task_date' => today()->subDay(),
            'is_done' => true, // Completed, should not be included
        ]);
        Task::factory()->for($this->user)->create([
            'task_date' => today(), // Today, should not be included
            'is_done' => false,
        ]);

        $overdueTasks = $this->taskService->getOverdueTasks($this->user);

        $this->assertCount(2, $overdueTasks);
    }

    public function test_get_upcoming_tasks_returns_future_tasks(): void
    {
        Task::factory()->for($this->user)->create([
            'task_date' => today()->addDays(2),
            'is_done' => false,
        ]);
        Task::factory()->for($this->user)->create([
            'task_date' => today()->addDays(5),
            'is_done' => false,
        ]);
        Task::factory()->for($this->user)->create([
            'task_date' => today()->addDays(10), // Beyond default 7 days
            'is_done' => false,
        ]);

        $upcomingTasks = $this->taskService->getUpcomingTasks($this->user);

        $this->assertCount(2, $upcomingTasks);
    }

    public function test_get_task_statistics_calculates_correctly(): void
    {
        Task::factory()->for($this->user)->create(['is_done' => true]);
        Task::factory()->for($this->user)->create(['is_done' => true]);
        Task::factory()->for($this->user)->create(['is_done' => true]);
        Task::factory()->for($this->user)->create(['is_done' => false]);

        $stats = $this->taskService->getTaskStatistics($this->user);

        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(3, $stats['completed']);
        $this->assertEquals(1, $stats['pending']);
        $this->assertEquals(75.0, $stats['completion_rate']);
    }

    public function test_delete_task_removes_from_database(): void
    {
        $task = Task::factory()->for($this->user)->create();

        $result = $this->taskService->deleteTask($task);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}

