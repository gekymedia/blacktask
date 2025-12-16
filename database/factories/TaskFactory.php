<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'is_done' => fake()->boolean(30), // 30% chance of being done
            'task_date' => fake()->dateTimeBetween('-7 days', '+7 days'),
            'priority' => fake()->numberBetween(0, 2),
            'category_id' => null,
            'reminder_at' => null,
            'recurrence' => null,
            'recurrence_ends_at' => null,
        ];
    }

    /**
     * Indicate that the task is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_done' => true,
        ]);
    }

    /**
     * Indicate that the task is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_done' => false,
        ]);
    }

    /**
     * Indicate that the task has high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 2,
        ]);
    }

    /**
     * Indicate that the task is for today.
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'task_date' => today(),
        ]);
    }

    /**
     * Indicate that the task is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'task_date' => fake()->dateTimeBetween('-30 days', '-1 day'),
            'is_done' => false,
        ]);
    }

    /**
     * Indicate that the task has a category.
     */
    public function withCategory(): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => Category::factory(),
        ]);
    }

    /**
     * Indicate that the task is recurring.
     */
    public function recurring(string $type = 'daily', ?\DateTime $endsAt = null): static
    {
        return $this->state(fn (array $attributes) => [
            'recurrence' => $type,
            'recurrence_ends_at' => $endsAt,
        ]);
    }
}

