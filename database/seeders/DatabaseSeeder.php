<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        for ($i=1; $i<=5; $i++) {
          $user = User::factory()->create([
            'name' => "Test User $i",
            'email' => "test$i@example.com",
            'password' => Hash::make('123456'),
          ]);
          
          for ($j=1; $j<=5; $j++) {
            $post = Post::factory()->create([
              'title' => "Test title $j for User $i",
              'user_id' => $user?->id,
            ]);

            Comment::factory()->create([
                'content' => "This is a test comment for User $i and Post $j", 
                'post_id' => $post?->id, 
                'user_id' => $user?->id, 
            ]);
          }
        }
        
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
