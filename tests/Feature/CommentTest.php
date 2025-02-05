<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CommentTest extends TestCase
{
    private User $user;

    private Post $post;

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

        $user = $this->user;
        $postsCount = Post::count();
        $this->post = ($postsCount >= 1) ?
            Post::firstOrFail() :
            Post::factory()->state(
                fn (array $state) => [
                    'user_id' => $user?->id,
                ])->create();
        $this->assertDatabaseHas('posts', [
            'title' => $this->post?->title,
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    private function getComment(int $commentId): Comment
    {
        $comment = Comment::findOrFail($commentId);

        return $comment;
    }

    private function createComment(string $content): int
    {
        $response = $this->actingAs($this->user)
            ->postJson(
                route('posts.comments.store', ['post' => $this->post?->id]),
                [
                    'content' => $content,
                    'post_id' => $this->post?->id,
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
    public function test_can_create_new_comment(): void
    {
        $this->createComment('This is a test Comment 1');
    }

    public function test_can_show_existing_comment(): void
    {
        $newId = $this->createComment('This is a test Comment 2');

        $response = $this->actingAs($this->user)
            ->getJson(
                route('comments.show', [
                    'comment' => $newId,
                ])
            );

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json->has('data')
                ->first(fn (AssertableJson $json2) => $json2->where('id', $newId)
                    ->whereType('id', 'integer')
                    ->whereType('content', 'string')
                    ->whereType('post_id', 'integer')
                    ->whereType('user_id', 'integer')
                    ->etc()
                )
                ->etc()
            );
    }

    public function test_can_update_existing_comment(): void
    {
        $newId = $this->createComment('This is a test Comment 3');

        $response = $this->actingAs($this->user)
            ->putJson(
                route('comments.update', ['comment' => $newId]),
                [
                    'id' => $newId,
                    'content' => 'This is a test Comment 3 - Updated',
                    'post_id' => $this->post?->id,
                    'user_id' => $this->user?->id,
                ]
            );

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json->where('ok', true)
                ->etc()
            );
    }

    public function test_can_delete_existing_comment(): void
    {
        $newId = $this->createComment('This is a test Comment 4');

        $response = $this->actingAs($this->user)
            ->deleteJson(
                route('comments.destroy', ['comment' => $newId]),
            );

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json->where('ok', true)
                ->etc()
            );
    }

    public function test_can_list_all_comments_of_a_post(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(
                route('posts.comments.index', ['post' => $this->post?->id])
            );

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json->has('data')
                ->first(fn (AssertableJson $json2) => $json2->each(fn (AssertableJson $json3) => $json3->whereType('id', 'integer')
                    ->whereType('content', 'string')
                    ->whereType('post_id', 'integer')
                    ->whereType('user_id', 'integer')
                    ->etc()
                )
                )
                ->etc()
            );
    }
}
