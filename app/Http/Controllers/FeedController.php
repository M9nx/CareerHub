<?php

namespace App\Http\Controllers;

use App\Actions\SharePostToFeed;
use App\Actions\StorePostAttachments;
use App\Actions\TogglePostReaction;
use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Http\Requests\Feed\ShareTimelinePostRequest;
use App\Http\Requests\Feed\StorePostCommentRequest;
use App\Http\Requests\Feed\StoreTimelinePostRequest;
use App\Models\JobPosting;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;
use App\Support\Timeline\TimelineQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FeedController extends Controller
{
    public function index(Request $request): View
    {
        $viewer = $request->user();
        $viewer->loadMissing(['employerProfile', 'employeeProfile']);

        $items = (new TimelineQuery($request))->paginate($viewer);

        $suggestedJobs = JobPosting::query()
            ->published()
            ->with(['employer.employerProfile'])
            ->latest('published_at')
            ->limit(5)
            ->get();

        $suggestedPeople = User::query()
            ->whereKeyNot($viewer->id)
            ->whereIn('id', Post::published()->select('author_id'))
            ->orderBy('name')
            ->limit(5)
            ->get();

        $profileChecks = match ($viewer->role) {
            UserRole::Employee => [
                ['label' => __('Name on account'), 'complete' => filled($viewer->name)],
                ['label' => __('Headline'), 'complete' => filled($viewer->headline)],
                ['label' => __('Location'), 'complete' => filled($viewer->location)],
                ['label' => __('Profile photo'), 'complete' => filled($viewer->avatar_path)],
                ['label' => __('CV uploaded'), 'complete' => filled($viewer->employeeProfile?->cv_path)],
                ['label' => __('Application image'), 'complete' => filled($viewer->employeeProfile?->application_image_path)],
            ],
            UserRole::Employer => [
                ['label' => __('Name on account'), 'complete' => filled($viewer->name)],
                ['label' => __('Headline'), 'complete' => filled($viewer->headline)],
                ['label' => __('Location'), 'complete' => filled($viewer->location)],
                ['label' => __('Profile photo'), 'complete' => filled($viewer->avatar_path)],
                ['label' => __('Company name'), 'complete' => filled($viewer->employerProfile?->company_name)],
                ['label' => __('Company industry'), 'complete' => filled($viewer->employerProfile?->industry)],
            ],
            default => [
                ['label' => __('Name on account'), 'complete' => filled($viewer->name)],
            ],
        };

        return view('feed.index', compact('items', 'suggestedJobs', 'suggestedPeople', 'profileChecks'));
    }

    public function store(
        StoreTimelinePostRequest $request,
        StorePostAttachments $storePostAttachments,
    ): RedirectResponse {
        $validated = $request->validated();

        $post = Post::create([
            'author_id' => $request->user()->id,
            'author_role' => $request->user()->role,
            'title' => $validated['title'] ?? Str::limit($validated['body'], 80),
            'body' => $validated['body'],
            'status' => PostStatus::Published,
            'is_active' => true,
        ]);

        if (! empty($validated['attachments'])) {
            $storePostAttachments->handle($post, $validated['attachments']);
        }

        return redirect()->route('feed.index');
    }

    public function react(Request $request, Post $post, TogglePostReaction $togglePostReaction): RedirectResponse
    {
        Gate::authorize('react', $post);

        $togglePostReaction->handle($request->user(), $post);

        return redirect()->route('feed.index');
    }

    public function share(
        ShareTimelinePostRequest $request,
        Post $post,
        SharePostToFeed $sharePostToFeed,
    ): RedirectResponse {
        $sharePostToFeed->handle(
            $request->user(),
            $post,
            $request->validated('comment'),
        );

        return redirect()->route('feed.index');
    }

    public function storeComment(StorePostCommentRequest $request, Post $post): RedirectResponse
    {
        PostComment::create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
        ]);

        return redirect()
            ->route('feed.index')
            ->withFragment('post-'.$post->id);
    }

    public function destroyComment(Request $request, Post $post, PostComment $comment): RedirectResponse
    {
        abort_unless($comment->post_id === $post->id, 404);

        Gate::authorize('delete', $comment);

        $comment->delete();

        return redirect()
            ->route('feed.index')
            ->withFragment('post-'.$post->id);
    }
}
