<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

interface PostRepositoryInterface
{
    public function findMany(Request $request): LengthAwarePaginator;

    public function find(int $id): Post;

    public function create(array $data): Post;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
