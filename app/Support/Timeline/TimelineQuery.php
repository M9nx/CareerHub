<?php

namespace App\Support\Timeline;

use App\Enums\UserRole;
use App\Models\Application;
use App\Models\JobPosting;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;

class TimelineQuery
{
    public function __construct(private Request $request) {}

    public function paginate(User $viewer, int $perPage = 10): LengthAwarePaginator
    {
        $items = $this->posts()
            ->concat($this->publishedJobs())
            ->concat($this->applicationEvents($viewer))
            ->sortByDesc(fn (TimelineItem $item) => [$item->occurredAt->timestamp, $this->subjectKey($item)])
            ->values();

        $page = max(1, (int) $this->request->query('page', 1));
        $total = $items->count();
        $slice = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new Paginator(
            $slice,
            $total,
            $perPage,
            $page,
            [
                'path' => $this->request->url(),
                'query' => $this->request->query(),
            ],
        );
    }

    /**
     * @return Collection<int, TimelineItem>
     */
    private function posts(): Collection
    {
        return Post::published()
            ->with([
                'author',
                'attachments',
                'reactions',
                'sharedPost.author',
                'sharedPost.attachments',
            ])
            ->get()
            ->map(fn (Post $post): TimelineItem => TimelineItem::forPost($post));
    }

    /**
     * @return Collection<int, TimelineItem>
     */
    private function publishedJobs(): Collection
    {
        return JobPosting::query()
            ->published()
            ->with('employer')
            ->get()
            ->map(fn (JobPosting $jobPosting): TimelineItem => TimelineItem::forJobPosting($jobPosting));
    }

    /**
     * @return Collection<int, TimelineItem>
     */
    private function applicationEvents(User $viewer): Collection
    {
        $query = Application::query()
            ->with(['employee', 'jobPosting']);

        $applications = match ($viewer->role) {
            UserRole::Employer => $query
                ->whereHas('jobPosting', fn ($jobQuery) => $jobQuery->where('employer_id', $viewer->id))
                ->get(),
            UserRole::Employee => $query
                ->where('employee_id', $viewer->id)
                ->get(),
            default => collect(),
        };

        return $applications->map(
            fn (Application $application): TimelineItem => TimelineItem::forApplication($application),
        );
    }

    private function subjectKey(TimelineItem $item): string
    {
        return sprintf('%s:%s', $item->type->value, $item->subject->getKey());
    }
}
