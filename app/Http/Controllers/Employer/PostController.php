<?php

namespace App\Http\Controllers\Employer;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Post::class);

        $posts = Post::query()
            ->whereBelongsTo($request->user(), 'author')
            ->latest()
            ->get();

        return view('employer.posts.index', compact('posts'));
    }

    public function create(): View
    {
        Gate::authorize('create', Post::class);

        return view('employer.posts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->isBlockedFromPosts()) {
            return redirect()
                ->back()
                ->with('error', __('You are blocked from creating posts.'));
        }

        Gate::authorize('create', Post::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'publish' => ['sometimes', 'boolean'],
        ]);

        Post::create([
            'author_id' => $request->user()->id,
            'author_role' => $request->user()->role,
            'title' => $validated['title'],
            'body' => $validated['body'],
            'status' => $request->boolean('publish')
                ? PostStatus::Published
                : PostStatus::Draft,
            'is_active' => true,
        ]);

        return redirect()
            ->route('employer.posts.index')
            ->with('success', __('Post created successfully.'));
    }

    public function edit(Post $post): View
    {
        Gate::authorize('update', $post);

        return view('employer.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        Gate::authorize('update', $post);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'publish' => ['sometimes', 'boolean'],
        ]);

        $post->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'status' => $request->boolean('publish')
                ? PostStatus::Published
                : PostStatus::Draft,
        ]);

        return redirect()
            ->route('employer.posts.index')
            ->with('success', __('Post updated successfully.'));
    }

    public function destroy(Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return redirect()
            ->route('employer.posts.index')
            ->with('success', __('Post deleted successfully.'));
    }
}