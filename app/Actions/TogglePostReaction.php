<?php

namespace App\Actions;

use App\Enums\PostReactionType;
use App\Models\Post;
use App\Models\PostReaction;
use App\Models\User;

class TogglePostReaction
{
    public function handle(User $user, Post $post, PostReactionType $type = PostReactionType::Like): bool
    {
        $existing = PostReaction::query()
            ->where('post_id', $post->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing !== null) {
            $existing->delete();

            return false;
        }

        PostReaction::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'type' => $type,
        ]);

        return true;
    }
}
