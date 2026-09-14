<?php

namespace App\Actions;

use App\Models\User;
use App\Services\ActivityLogger;

class BlockUserFromPosts
{
    public function __construct(private ActivityLogger $activityLogger) {}

    public function handle(User $user, bool $blocked, ?User $causer = null): User
    {
        if ($user->isBlockedFromPosts() === $blocked) {
            return $user;
        }

        $user->update([
            'is_blocked_from_posts' => $blocked,
        ]);

        if ($blocked) {
            $this->activityLogger->log(
                'user.blocked_from_posts',
                $user,
                $causer ?? $this->causer(),
                [
                    'event' => 'blocked_from_posts',
                    'is_blocked_from_posts' => true,
                ],
            );
        }

        return $user;
    }

    private function causer(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
