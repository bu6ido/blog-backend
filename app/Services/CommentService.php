<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Repositories\CommentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $comment = DB::transaction(function () use ($data, $post): Comment {
            return $this->commentRepository->create($data, $post);
        });

        return $comment;
    }

    public function update(int $id, array $data): bool
    {
        $result = DB::transaction(function () use ($id, $data): bool {
            return $this->commentRepository->update($id, $data);
        });

        return $result;
    }

    public function delete(int $id): bool
    {
        $result = DB::transaction(function () use ($id): bool {
            return $this->commentRepository->delete($id);
        });

        return $result;
    }
}
