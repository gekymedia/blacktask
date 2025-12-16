<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $colors = [
            '#ef4444', // red
            '#f59e0b', // amber
            '#10b981', // green
            '#3b82f6', // blue
            '#6366f1', // indigo
            '#8b5cf6', // purple
            '#ec4899', // pink
        ];

        return [
            'user_id' => User::factory(),
            'name' => fake()->words(2, true),
            'color' => fake()->randomElement($colors),
        ];
    }
}

