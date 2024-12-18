<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $content = fake()->unique()->paragraph();
        return [
            'content' => [
                [
                    "type" => "text",
                    "content" => $content
                ]
            ],
            'user_id' => User::inRandomOrder()->first(),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
