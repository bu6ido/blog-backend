<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    public function __construct(protected PostService $postService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Post::class);

        $result = $this->postService->findMany($request);

        return PostResource::collection(
            $result
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $this->authorize('create', Post::class);

        $post = $this->postService->create($request->validated());

        return response()->json(['ok' => true, 'id' => $post->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): PostResource
    {
        /** @todo $post = Post::with('comments')->findOrFail($post->id); */
        $this->authorize('view', $post);

        //$post = $this->postService->find($post->id);

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);

        $result = $this->postService->update($post->id, $request->validated());

        return response()->json(['ok' => $result]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete', $post);

        $result = $this->postService->delete($post->id);

        return response()->json(['ok' => $result]);
    }
}
