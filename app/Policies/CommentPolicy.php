<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Determine whether the user can view any models.
     * 
     * @param \App\Models\User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        //
        return true;        
    }

    /**
     * Determine whether the user can view the model.
     * 
     * @param \App\Models\User $user
     * @param \App\Models\Comment $comment
     * @return bool
     */
    public function view(User $user, Comment $comment): bool
    {
        //
        return true;        
    }

    /**
     * Determine whether the user can create models.
     * 
     * @param \App\Models\User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        //
        return true;        
    }

    /**
     * Determine whether the user can update the model.
     * 
     * @param \App\Models\User $user
     * @param \App\Models\Comment $comment
     * @return bool
     */
    public function update(User $user, Comment $comment): bool
    {
        return $user?->id === $comment?->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     * 
     * @param \App\Models\User $user
     * @param \App\Models\Comment $comment
     * @return bool
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user?->id === $comment?->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     * 
     * @param \App\Models\User $user
     * @param \App\Models\Comment $comment
     * @return bool
     */
    public function restore(User $user, Comment $comment): bool
    {
        return $user?->id === $comment?->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * 
     * @param \App\Models\User $user
     * @param \App\Models\Comment $comment
     * @return bool
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        return $user?->id === $comment?->user_id;
    }
}
