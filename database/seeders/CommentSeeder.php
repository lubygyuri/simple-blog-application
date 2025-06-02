<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = Post::all();
        $users = User::all();

        $posts->each(function (Post $post) use ($users) {
           Comment::factory()
               ->count(rand(0, 8))
               ->sequence(fn() => ['user_id' => $users->random()->id])
               ->create(['post_id' => $post->id]);
        });
    }
}
