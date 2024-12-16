<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\User;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Post::class);

        $search = $request->input('query');
        $rowsPerPage = $request->input('rowsPerPage', 5);
        $sortBy = $request->input('sortBy');
        $sortDir = $request->input('sortDir', 'asc');
        
        $query = Post::with('user')->withAggregate('user', 'name')->withCount('comments'); 
        if (!empty($search)) {
            $query = $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%'.$search.'%');
                $q->orWhere('title', 'like', '%'.$search.'%');
                $q->orWhereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', '%'.$search.'%');
                });
            });
        }
        if (!empty($sortBy) && !empty($sortDir)) {
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
     * 
     * @param \App\Http\Requests\StorePostRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePostRequest $request)
    {
        $this->authorize('create', Post::class);

        $post = new Post();
        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->user_id = $request->input('user_id');
        $post->save();

        return response()->json(['ok' => true, 'id' => $post->id]);
    }

    /**
     * Display the specified resource.
     * 
     * @param \App\Models\Post $post
     * @return \App\Http\Resources\PostResource
     */
    public function show(Post $post)
    {
        /** @todo $post = Post::with('comments')->findOrFail($post->id); */        
        
        $this->authorize('view', $post);
        
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param \App\Http\Requests\UpdatePostRequest $request
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->user_id = $request->input('user_id');
        $post->save();
        
        return response()->json(['ok' => true]);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->comments()->delete();
        
        $post->delete();

        return response()->json(['ok' => true]);
    }
}
