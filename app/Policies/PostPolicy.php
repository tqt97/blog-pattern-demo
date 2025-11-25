<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function publish(User $user, Post $post): bool
    {
        return $user->hasAnyRole([
            User::ROLE_ADMIN,
            User::ROLE_EDITOR,
        ]);
    }

    public function update(User $user, Post $post): bool
    {
        return $user->isAdmin() || $user->id === $post->author_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->isAdmin();
    }
}
