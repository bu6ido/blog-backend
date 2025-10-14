<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

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
        return $this->postRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->postRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->postRepository->delete($id);
    }
}
