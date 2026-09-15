<?php

namespace App\Policies;

use App\Models\PostComment;
use App\Models\User;

class PostCommentPolicy
{
    public function create(User $user): bool
    {
        return ! $user->isBlockedFromPosts();
    }

    public function delete(User $user, PostComment $postComment): bool
    {
        return $postComment->user_id === $user->id && ! $user->isBlockedFromPosts();
    }
}
