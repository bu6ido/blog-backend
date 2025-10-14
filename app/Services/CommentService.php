<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Repositories\CommentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class CommentService
{
    public function __construct(protected CommentRepositoryInterface $commentRepository)
    {
    }

    public function findMany(Request $request, Post $post): LengthAwarePaginator
    {
        return $this->commentRepository->findMany($request, $post);
    }

    public function find(int $id): Comment
    {
        return $this->commentRepository->find($id);
    }

    public function create(array $data, Post $post): Comment
    {
        return $this->commentRepository->create($data, $post);
    }

    public function update(int $id, array $data): bool
    {
        return $this->commentRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->commentRepository->delete($id);
    }
}
