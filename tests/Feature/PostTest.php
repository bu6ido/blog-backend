<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PostTest extends TestCase
{
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $usersCount = User::count();
        $this->user = ($usersCount >= 1) ?
            User::firstOrFail() :
            User::factory()->create();
        $this->assertDatabaseHas('users', [
            'email' => $this->user?->email,
        ]);
        $this->assertDatabaseCount('users', 1);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    private function getPost(int $postId): Post
    {
        $post = Post::findOrFail($postId);

        return $post;
    }

    private function createPost(string $title): int
    {
        $response = $this->actingAs($this->user)
            ->postJson(
                route('posts.store'),
                [
                    'title' => $title,
                    'content' => Str::random(512),
                    'user_id' => $this->user?->id,
                ]
            );

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json->where('ok', true)
                ->whereType('id', 'integer')
                ->etc()
            );

        return $response->getData()->id;
    }

    /**
     * A basic feature test example.
     */
    public function test_can_create_new_post(): void
    {
        $this->createPost('Test Post 1');
    }

    public function test_can_show_existing_post(): void
    {
        $newId = $this->createPost('Test Post 2');

        $response = $this->actingAs($this->user)
            ->getJson(
                route('posts.show', [
                    'post' => $newId,
                    'view_type' => 'show',
                ])
            );

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json->has('data')
                ->first(fn (AssertableJson $json2) => $json2->where('id', $newId)
                    ->whereType('id', 'integer')
                    ->whereType('title', 'string')
                    ->whereType('content', 'string')
                    ->whereType('user_id', 'integer')
                    ->etc()
                )
                ->etc()
            );
    }

    public function test_can_update_existing_post(): void
    {
        $newId = $this->createPost('Test Post 3');

        $response = $this->actingAs($this->user)
            ->putJson(
                route('posts.update', ['post' => $newId]),
                [
                    'id' => $newId,
                    'title' => 'Test Post 3 - Updated',
                    'content' => Str::random(512),
                    'user_id' => $this->user?->id,
                ]
            );

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json->where('ok', true)
                ->etc()
            );
    }

    public function test_can_delete_existing_post(): void
    {
        $newId = $this->createPost('Test Post 4');

        $response = $this->actingAs($this->user)
            ->deleteJson(
                route('posts.destroy', ['post' => $newId]),
            );

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json->where('ok', true)
                ->etc()
            );
    }

    public function test_can_list_all_posts(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(
                route('posts.index')
            );

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json->has('data')
                ->first(fn (AssertableJson $json2) => $json2->each(fn (AssertableJson $json3) => $json3->whereType('id', 'integer')
                    ->whereType('title', 'string')
                            //->whereType('content', 'string')
                    ->whereType('user_id', 'integer')
                    ->etc()
                )
                )
                ->etc()
            );
    }
}
