<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostService
{
    public function __construct(protected PostRepositoryInterface $postRepository)
    {
    }

    public function findMany(Request $request): LengthAwarePaginator
    {
        return $this->postRepository->findMany($request);
    }

    public function find(int $id): Post
    {
        return $this->postRepository->find($id);
    }

    public function create(array $data): Post
    {
        $post = DB::transaction(function () use ($data): Post {
            return $this->postRepository->create($data);
        });

        return $post;
    }

    public function update(int $id, array $data): bool
    {
        $result = DB::transaction(function () use ($id, $data): bool {
            return $this->postRepository->update($id, $data);
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

            //return $this->postRepository->delete($id);
        });

        return $result;
    }
}
