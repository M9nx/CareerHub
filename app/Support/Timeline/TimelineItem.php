<?php

namespace App\Support\Timeline;

use App\Models\Application;
use App\Models\JobPosting;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Carbon;

final readonly class TimelineItem
{
    public function __construct(
        public TimelineItemType $type,
        public Carbon $occurredAt,
        public Post|JobPosting|Application $subject,
        public ?User $actor = null,
    ) {}

    public static function forPost(Post $post): self
    {
        return new self(
            type: TimelineItemType::Post,
            occurredAt: $post->created_at,
            subject: $post,
            actor: $post->author,
        );
    }

    public static function forJobPosting(JobPosting $jobPosting): self
    {
        return new self(
            type: TimelineItemType::JobPublished,
            occurredAt: $jobPosting->published_at ?? $jobPosting->created_at,
            subject: $jobPosting,
            actor: $jobPosting->employer,
        );
    }

    public static function forApplication(Application $application): self
    {
        return new self(
            type: TimelineItemType::ApplicationEvent,
            occurredAt: $application->updated_at,
            subject: $application,
            actor: $application->employee,
        );
    }
}
