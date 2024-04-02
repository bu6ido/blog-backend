<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Comment::class);
        
        $search = $request->input('query');
        $postId = $request->input('post_id');
        $rowsPerPage = $request->input('rowsPerPage', 5);
        $sortBy = $request->input('sortBy');
        $sortDir = $request->input('sortDir', 'asc');
        
        $query = Comment::with(['user', 'post'])
            ->withAggregate('user', 'name')
            ->withAggregate('post', 'title'); 
        if (!empty($search)) {
            $query = $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%'.$search.'%');
                $q->orWhere('content', 'like', '%'.$search.'%');
                $q->orWhereHas('post', function ($q2) use ($search) {
                    $q2->where('title', 'like', '%'.$search.'%');
                });
                $q->orWhereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', '%'.$search.'%');
                });
            });
        }
        if (!empty($postId)) {
            $query = $query->where('post_id', $postId);
        }
        if (!empty($sortBy) && !empty($sortDir)) {
            $query = $query->orderBy($sortBy, $sortDir);
        }
        $query = $query
            ->paginate($rowsPerPage)
            ->withQueryString();

        return CommentResource::collection(
            $query
        );        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request)
    {
        $this->authorize('create', Comment::class);
                
        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->post_id = $request->input('post_id');
        $comment->user_id = $request->input('user_id');
        $comment->save();

        return response()->json(['ok' => true, 'id' => $comment->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        $this->authorize('view', $comment);

        $comment = Comment::with(['post', 'user'])->findOrFail($comment->id);

        return new CommentResource($comment);                
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $this->authorize('update', $comment);        
        
        $comment->content = $request->input('content');
        $comment->post_id = $request->input('post_id');
        $comment->user_id = $request->input('user_id');
        $comment->save();
        
        return response()->json(['ok' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);        
        
        $comment->delete();
        
        return response()->json(['ok' => true]);        
    }
}

