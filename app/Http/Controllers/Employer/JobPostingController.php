<?php

namespace App\Http\Controllers\Employer;

use App\Enums\JobPostingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\StoreJobPostingRequest;
use App\Http\Requests\Employer\UpdateJobPostingRequest;
use App\Models\JobPosting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class JobPostingController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', JobPosting::class);

        $jobs = JobPosting::query()
            ->whereBelongsTo($request->user(), 'employer')
            ->latest()
            ->get();

        return view('employer.jobs.index', compact('jobs'));
    }

    public function create(): View
    {
        Gate::authorize('create', JobPosting::class);

        return view('employer.jobs.create', [
            'statuses' => JobPostingStatus::cases(),
        ]);
    }

    public function store(StoreJobPostingRequest $request): RedirectResponse
    {
        JobPosting::create([
            'employer_id' => $request->user()->id,
            ...$this->publicationAttributes($request->safe()->only([
                'title',
                'description',
                'location',
                'employment_type',
                'status',
            ])),
        ]);

        return redirect()
            ->route('employer.jobs.index')
            ->with('success', __('Job posting created successfully.'));
    }

    public function edit(JobPosting $job): View
    {
        Gate::authorize('update', $job);

        return view('employer.jobs.edit', [
            'job' => $job,
            'statuses' => JobPostingStatus::cases(),
        ]);
    }

    public function update(
        UpdateJobPostingRequest $request,
        JobPosting $job
    ): RedirectResponse {
        $job->update($this->publicationAttributes(
            $request->safe()->only(['title', 'description', 'location', 'employment_type', 'status']),
            $job,
        ));

        return redirect()
            ->route('employer.jobs.index')
            ->with('success', __('Job posting updated successfully.'));
    }

    public function publish(JobPosting $job): RedirectResponse
    {
        Gate::authorize('update', $job);

        abort_unless($job->status === JobPostingStatus::Draft, 403);

        $job->update([
            'status' => JobPostingStatus::Published,
            'published_at' => now(),
        ]);

        return redirect()
            ->route('employer.jobs.edit', $job)
            ->with('success', __('Job posting published successfully.'));
    }

    public function close(JobPosting $job): RedirectResponse
    {
        Gate::authorize('update', $job);

        abort_unless($job->status === JobPostingStatus::Published, 403);

        $job->update([
            'status' => JobPostingStatus::Closed,
        ]);

        return redirect()
            ->route('employer.jobs.edit', $job)
            ->with('success', __('Job posting closed successfully.'));
    }

    public function destroy(JobPosting $job): RedirectResponse
    {
        Gate::authorize('delete', $job);

        $job->delete();

        return redirect()
            ->route('employer.jobs.index')
            ->with('success', __('Job posting deleted successfully.'));
    }

    /**
     * @param  array{title: string, description: string, location?: string|null, employment_type?: string|null, status: string}  $attributes
     * @return array{title: string, description: string, location?: string|null, employment_type?: string|null, status: string, published_at?: Carbon}
     */
    private function publicationAttributes(array $attributes, ?JobPosting $job = null): array
    {
        if (
            $attributes['status'] === JobPostingStatus::Published->value
            && ($job === null || $job->published_at === null)
        ) {
            $attributes['published_at'] = now();
        }

        return $attributes;
    }
}
