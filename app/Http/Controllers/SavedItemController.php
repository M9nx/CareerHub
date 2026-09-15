<?php

namespace App\Http\Controllers;

use App\Enums\JobPostingStatus;
use App\Enums\PostStatus;
use App\Models\JobPosting;
use App\Models\Post;
use App\Models\SavedJob;
use App\Models\SavedPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavedItemController extends Controller
{
    public function index(Request $request): View
    {
        $savedJobs = SavedJob::query()
            ->where('user_id', $request->user()->id)
            ->with(['jobPosting.employer.employerProfile'])
            ->latest()
            ->get();

        $savedPosts = SavedPost::query()
            ->where('user_id', $request->user()->id)
            ->with(['post.author'])
            ->latest()
            ->get();

        return view('saved.index', compact('savedJobs', 'savedPosts'));
    }

    public function storeJob(Request $request, JobPosting $job): RedirectResponse
    {
        abort_unless(
            $job->status === JobPostingStatus::Published && $job->is_active,
            404,
        );

        SavedJob::query()->firstOrCreate([
            'user_id' => $request->user()->id,
            'job_posting_id' => $job->id,
        ]);

        return back()->with('success', __('Job saved.'));
    }

    public function destroyJob(Request $request, JobPosting $job): RedirectResponse
    {
        SavedJob::query()
            ->where('user_id', $request->user()->id)
            ->where('job_posting_id', $job->id)
            ->delete();

        return back()->with('success', __('Job removed from saved items.'));
    }

    public function storePost(Request $request, Post $post): RedirectResponse
    {
        abort_unless($post->status === PostStatus::Published && $post->is_active, 404);

        SavedPost::query()->firstOrCreate([
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
        ]);

        return back()->with('success', __('Post saved.'));
    }

    public function destroyPost(Request $request, Post $post): RedirectResponse
    {
        SavedPost::query()
            ->where('user_id', $request->user()->id)
            ->where('post_id', $post->id)
            ->delete();

        return back()->with('success', __('Post removed from saved items.'));
    }
}
