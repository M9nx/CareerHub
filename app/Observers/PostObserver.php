<?php

namespace App\Observers;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use App\Services\ActivityLogger;

class PostObserver
{
    public function __construct(private ActivityLogger $activityLogger) {}

    public function created(Post $post): void
    {
        $this->logLifecycleEvent($post);
    }

    public function updated(Post $post): void
    {
        if (! $post->wasChanged('status')) {
            return;
        }

        $this->logLifecycleEvent($post, $this->statusValue($post->getOriginal('status')));
    }

    private function logLifecycleEvent(Post $post, ?string $fromStatus = null): void
    {
        $event = match ($post->status) {
            PostStatus::Published => 'published',
            PostStatus::Hidden => 'hidden',
            default => null,
        };

        if ($event === null) {
            return;
        }

        $this->activityLogger->log('post.'.$event, $post, $this->causer(), array_filter([
            'event' => $event,
            'status' => $this->statusValue($post->status),
            'from_status' => $fromStatus,
            'title' => $post->title,
            'author_id' => $post->author_id,
        ], fn (mixed $value): bool => $value !== null));
    }

    private function causer(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }

    private function statusValue(mixed $status): ?string
    {
        if ($status instanceof PostStatus) {
            return $status->value;
        }

        return is_string($status) ? $status : null;
    }
}
