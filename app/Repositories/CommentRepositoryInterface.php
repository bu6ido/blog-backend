<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

interface CommentRepositoryInterface
{
    public function findMany(Request $request, Post $post): LengthAwarePaginator;

    public function find(int $id): Comment;

    public function create($data, Post $post): Comment;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
