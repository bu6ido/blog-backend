<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class CommentRepository implements CommentRepositoryInterface
{
    public function findMany(Request $request, Post $post): LengthAwarePaginator
    {
        $search = $request->input('query');
        $postId = $post?->id; //$request->input('post_id');
        $rowsPerPage = $request->input('rowsPerPage', 5);
        $sortBy = $request->input('sortBy');
        $sortDir = $request->input('sortDir', 'asc');

        $query = Comment::with(['user', 'post'])
            ->withAggregate('user', 'name')
            ->withAggregate('post', 'title');
        if (! empty($search)) {
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
        if (! empty($postId)) {
            $query = $query->where('post_id', $postId);
        }
        if (! empty($sortBy) && ! empty($sortDir)) {
            $query = $query->orderBy($sortBy, $sortDir);
        }
        $query = $query
            ->paginate($rowsPerPage)
            ->withQueryString();

        return $query;
    }

    public function find(int $id): Comment
    {
        $comment = Comment::with(['post', 'user'])->findOrFail($id);

        return $comment;
    }

    public function create($data, Post $post): Comment
    {
        return Comment::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $comment = Comment::lockForUpdate()->findOrFail($id);
        $result = $comment->update($data);

        return $result;
    }

    public function delete(int $id): bool
    {
        $comment = Comment::lockForUpdate()->findOrFail($id);
        $result = $comment->delete();

        return $result;
    }
}
