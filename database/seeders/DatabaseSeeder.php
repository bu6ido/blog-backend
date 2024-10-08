<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Sequence;
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
        User::factory()->count(5)->sequence(fn (Sequence $seq1) => [
            'name' => "Test User " . ($seq1->index + 1),
            'email' => "test" . ($seq1->index + 1) . "@example.com",
            'password' => Hash::make('123456'), 
        ])
        ->create()
        ->each(function (User $user) {
            Post::factory()->count(5)->sequence(fn (Sequence $seq2) => [
                'title' => "Test title " . ($seq2->index + 1) . " for User {$user?->name}", // $seq1->index
                'user_id' => $user?->id,
            ])
            ->create()
            ->each(function (Post $post) use ($user) {
                Comment::factory()->count(1)->sequence(fn (Sequence $seq3) => [
                    'content' => "This is a test comment for User {$user?->name} and Post {$post?->title}", // $seq1->index  $seq2->index
                    'post_id' => $post?->id, 
                    'user_id' => $user?->id, 
                ])
                ->create();
            });
        });

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
