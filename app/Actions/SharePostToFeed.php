<?php

namespace App\Actions;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;

class SharePostToFeed
{
    public function handle(User $user, Post $post, ?string $comment = null): Post
    {
        $rootPost = $post->sharedPost ?? $post;

        return Post::create([
            'author_id' => $user->id,
            'author_role' => $user->role,
            'shared_post_id' => $rootPost->id,
            'title' => $rootPost->title,
            'body' => $comment ?: __('Shared a post.'),
            'status' => PostStatus::Published,
            'is_active' => true,
        ]);
    }
}
