<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        $users->each(function (User $user) {
            Post::factory()
                ->count(rand(2, 5))
                ->create([
                    'user_id' => $user->id,
                ]);
        });
    }
}
