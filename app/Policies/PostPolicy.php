<?php

namespace App\Policies;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user can view any posts.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the post.
     */
    public function view(User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create posts.
     */
    public function create(User $user): bool
    {
        return ! $user->isBlockedFromPosts();
    }

    /**
     * Determine whether the user can update the post.
     */
    public function update(User $user, Post $post): bool
    {
        return $post->author_id === $user->id;
    }

    /**
     * Determine whether the user can delete the post.
     */
    public function delete(User $user, Post $post): bool
    {
        return $post->author_id === $user->id;
    }

    /**
     * Determine whether the user can restore the post.
     */
    public function restore(User $user, Post $post): bool
    {
        return $post->author_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the post.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return $post->author_id === $user->id;
    }

    public function react(User $user, Post $post): bool
    {
        return ! $user->isBlockedFromPosts();
    }

    public function share(User $user, Post $post): bool
    {
        return ! $user->isBlockedFromPosts();
    }

    public function comment(User $user, Post $post): bool
    {
        return ! $user->isBlockedFromPosts()
            && $post->status === PostStatus::Published
            && $post->is_active;
    }
}
