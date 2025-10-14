<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $post = DB::transaction(function () use ($data): Post {
            $post = Post::create($data);

            return $post;
        });

        return $post;
    }

    public function update(int $id, array $data): bool
    {
        $result = DB::transaction(function () use ($id, $data): bool {
            $post = Post::lockForUpdate()->findOrFail($id);

            $result = $post->update($data);

            return $result;
        });

        return $result;
    }

    public function delete(int $id): bool
    {
        $result = DB::transaction(function () use ($id): bool {
            $post = Post::lockForUpdate()->findOrFail($id);
            $post->comments()->lockForUpdate()->get();

            $post->comments()->delete();
            $result = $post->delete();

            return $result;
        });

        return $result;
    }
}
