<?php

namespace Database\Factories;

use App\Models\Chat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Chat>
 */
class ChatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'title' => $this->faker->sentence(3),
            'user_id' => \App\Models\User::factory(),
            'favorite_ai' => $this->faker->randomElement(['deepseek/deepseek-v4-flash', 'openai/gpt-5-mini'])
        ];
    }
}
