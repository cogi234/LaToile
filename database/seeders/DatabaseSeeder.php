<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            "name" => "admin",
            "password" => Hash::make("admin"),
            "email" => "admin@admin.com",
            "moderator" => true
        ]);
        User::factory()->create([
            "name" => "colin",
            "password" => Hash::make("851c0628"),
            "email" => "colinbougie@gmail.com"
        ]);
        User::factory()->create([
            "name" => "jeremy",
            "password" => Hash::make("jeremy"),
            "email" => "jeremyracine2017@gmail.com"
        ]);


        //Create 10 dummy accounts
        User::factory()->count(10)->create();
        //Create 30 dummy posts
        Post::factory()->count(30)->create();

        $posts = Post::all();
        foreach ($posts as $post) {
            $content = $post->content;
            for ($i = 0; $i < sizeof($content); $i++) {
                $content[$i]['post_id'] = $post->id;
            }
            $post->content = $content;
            $post->save();
        }
    }
}
