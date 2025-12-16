<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_view_tasks_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('tasks.index'));

        $response->assertOk();
        $response->assertViewIs('tasks');
    }

    public function test_user_can_create_task(): void
    {
        $taskData = [
            'title' => 'Test Task',
            'task_date' => today()->toDateString(),
            'priority' => 1,
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('tasks.store'), $taskData);

        $response->assertCreated();
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_task_creation_requires_title(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('tasks.store'), []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['title']);
    }

    public function test_user_can_toggle_task_completion(): void
    {
        $task = Task::factory()->for($this->user)->create(['is_done' => false]);

        $response = $this->actingAs($this->user)
            ->patchJson(route('tasks.toggle', $task));

        $response->assertOk();
        $this->assertTrue($task->fresh()->is_done);
    }

    public function test_user_can_reschedule_task(): void
    {
        $task = Task::factory()->for($this->user)->create([
            'task_date' => today(),
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('tasks.reschedule', $task));

        $response->assertOk();
        $this->assertEquals(
            today()->addDay()->toDateString(),
            $task->fresh()->task_date->toDateString()
        );
    }

    public function test_user_can_delete_task(): void
    {
        $task = Task::factory()->for($this->user)->create();

        $response = $this->actingAs($this->user)
            ->deleteJson(route('tasks.destroy', $task));

        $response->assertOk();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_access_other_users_tasks(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($otherUser)->create();

        $response = $this->actingAs($this->user)
            ->patchJson(route('tasks.toggle', $task));

        $response->assertForbidden();
    }

    public function test_user_cannot_delete_other_users_tasks(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($otherUser)->create();

        $response = $this->actingAs($this->user)
            ->deleteJson(route('tasks.destroy', $task));

        $response->assertForbidden();
    }

    public function test_task_can_have_category(): void
    {
        $category = Category::factory()->for($this->user)->create();

        $response = $this->actingAs($this->user)
            ->postJson(route('tasks.store'), [
                'title' => 'Categorized Task',
                'category_id' => $category->id,
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('tasks', [
            'title' => 'Categorized Task',
            'category_id' => $category->id,
        ]);
    }

    public function test_task_priority_must_be_valid(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('tasks.store'), [
                'title' => 'Test Task',
                'priority' => 5, // Invalid priority
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['priority']);
    }

    public function test_task_date_cannot_be_in_past(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('tasks.store'), [
                'title' => 'Test Task',
                'task_date' => today()->subDay()->toDateString(),
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['task_date']);
    }

    public function test_guest_cannot_access_tasks(): void
    {
        $response = $this->get(route('tasks.index'));

        $response->assertRedirect(route('login'));
    }
}

