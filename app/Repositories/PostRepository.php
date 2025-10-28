<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class PostRepository implements PostRepositoryInterface
{
    public function findMany(Request $request): LengthAwarePaginator
    {
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

        return $query;
    }

    public function find(int $id): Post
    {
        return Post::findOrFail($id);
    }

    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $post = Post::lockForUpdate()->findOrFail($id);
        $result = $post->update($data);

        return $result;
    }

    public function delete(int $id): bool
    {
        $post = Post::lockForUpdate()->findOrFail($id);
        $result = $post->delete();

        return $result;
    }
}
