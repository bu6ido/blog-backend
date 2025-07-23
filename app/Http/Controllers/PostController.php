<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Post::class);

        $search = $request->input('query');
        $rowsPerPage = $request->input('rowsPerPage', 5);
        $sortBy = $request->input('sortBy');
        $sortDir = $request->input('sortDir', 'asc');

        $query = Post::with('user')->withAggregate('user', 'name')->withCount('comments');
        if (! empty($search)) {
            $query = $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%'.$search.'%');
                $q->orWhere('title', 'like', '%'.$search.'%');
                $q->orWhereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', '%'.$search.'%');
                });
            });
        }
        if (! empty($sortBy) && ! empty($sortDir)) {
            $query = $query->orderBy($sortBy, $sortDir);
        }
        $query = $query
            ->paginate($rowsPerPage)
            ->withQueryString();

        return PostResource::collection(
            $query
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $this->authorize('create', Post::class);

        $post = DB::transaction(function () use ($request): Post {
            $post = Post::create($request->validated());

            return $post;
        });

        return response()->json(['ok' => true, 'id' => $post->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): PostResource
    {
        /** @todo $post = Post::with('comments')->findOrFail($post->id); */
        $this->authorize('view', $post);

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);

        $result = DB::transaction(function () use ($request, $post): bool {
            $result = $post->update($request->validated());

            return $result;
        });

        return response()->json(['ok' => $result]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete', $post);

        $result = DB::transaction(function () use ($post): bool {
            $post->comments()->lockForUpdate()->delete();
            $result = $post->delete();

            return $result;
        });

        return response()->json(['ok' => $result]);
    }
}
