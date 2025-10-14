<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function __construct(protected CommentService $commentService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Post $post): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Comment::class);

        $result = $this->commentService->findMany($request, $post);

        return CommentResource::collection(
            $result
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request, Post $post): JsonResponse
    {
        $this->authorize('create', Comment::class);

        $comment = $this->commentService->create($request->validated(), $post);

        return response()->json(['ok' => true, 'id' => $comment->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment): CommentResource
    {
        $this->authorize('view', $comment);

        $comment = $this->commentService->find($comment->id);

        return new CommentResource($comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        $this->authorize('update', $comment);

        $result = $this->commentService->update($comment->id, $request->validated());

        return response()->json(['ok' => $result]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $result = $this->commentService->delete($comment->id);

        return response()->json(['ok' => $result]);
    }
}
