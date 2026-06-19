<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        for($i = 0; $i < 2; $i++) {
            $user = \App\Models\User::factory()->create([
            'name' => "Test User $i",
            'email' => "test+$i@example.com",
            ]);

            for($j = 0; $j < 2; $j++) {
                $chat = \App\Models\Chat::factory()->create([
                'user_id' => $user->id,
                ]);

                \App\Models\Message::factory(15)->create([
                    'chat_id' => $chat->id,
                ]);
            }

        }
    }
}
