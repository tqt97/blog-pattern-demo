<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return true;
    }

    public function publish(User $user, Post $post): bool
    {
        return true;
    }

    public function unpublish(User $user, Post $post): bool
    {
        return true;
    }

    public function bulk(User $user, string $action): bool
    {
        return match ($action) {
            'delete', 'restore' => true, // or check role
            'force_delete' => true || $user->isAdmin(),
            'publish', 'unpublish' => true || $user->hasRole('editor'),
            default => false,
        };
    }
}
