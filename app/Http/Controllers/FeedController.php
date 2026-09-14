<?php

namespace App\Http\Controllers;

use App\Actions\SharePostToFeed;
use App\Actions\StorePostAttachments;
use App\Actions\TogglePostReaction;
use App\Enums\PostStatus;
use App\Http\Requests\Feed\ShareTimelinePostRequest;
use App\Http\Requests\Feed\StoreTimelinePostRequest;
use App\Models\Post;
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
        $items = (new TimelineQuery($request))->paginate($request->user());

        return view('feed.index', compact('items'));
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
}
