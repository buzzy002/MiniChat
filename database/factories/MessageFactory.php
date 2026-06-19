<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $index = 0;
        $roles = ['user', 'LLM'];
        $role = $roles[$index % 2];
        $index++;

        return [
            'content' => $this->faker->paragraph(rand(1, 10), true),
            'role' => $role,
            'chat_id'=> \App\Models\Chat::factory(),
        ];
    }
}
