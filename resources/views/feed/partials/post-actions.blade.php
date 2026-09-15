@php
    $commentCount = $post->comments->count();
    $openComments = $errors->has('body') && (int) old('comment_post_id') === $post->id;
@endphp

<div
    class="mt-4 border-t border-[var(--app-border)] pt-4"
    x-data="{ open: {{ $openComments || $commentCount > 0 ? 'true' : 'false' }} }"
>
    <div class="flex flex-wrap items-center gap-2">
        @can('react', $post)
            <form action="{{ route('feed.posts.react', $post) }}" method="POST">
                @csrf
                <button
                    type="submit"
                    @class(['app-action', 'is-active' => $liked])
                    aria-pressed="{{ $liked ? 'true' : 'false' }}"
                >
                    <svg class="h-5 w-5" fill="{{ $liked ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.633 10.25c.806 0 1.533-.278 2.107-.756l.478-.396c.915-.756 2.227-1.036 3.432-.78 1.653.356 2.826 1.857 2.826 3.582v.364c0 .621-.504 1.125-1.125 1.125H9.75a3 3 0 0 1-3-3v-.364c0-1.725 1.173-3.226 2.826-3.582.42-.09.857-.045 1.247.13" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    {{ $liked ? __('Liked') : __('Like') }}
                    @if ($likeCount > 0)
                        <span class="tabular-nums opacity-70">{{ $likeCount }}</span>
                    @endif
                </button>
            </form>
        @endcan

        <button
            type="button"
            class="app-action"
            @click="open = !open"
            :aria-expanded="open.toString()"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12c0 4.556-4.03 8.25-9 8.25a9.76 9.76 0 0 1-2.555-.337A5.97 5.97 0 0 1 3 18.75c0-.99.24-1.925.66-2.754C2.61 14.72 2.25 13.41 2.25 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
            </svg>
            {{ __('Comment') }}
            @if ($commentCount > 0)
                <span class="tabular-nums opacity-70">{{ $commentCount }}</span>
            @endif
        </button>

        @can('share', $post)
            <details class="group">
                <summary class="app-action cursor-pointer list-none">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314a2.25 2.25 0 1 0-3.256 3.256L7.217 10.907Zm0 0 9.566 5.314a2.25 2.25 0 0 0 3.256-3.256l-9.566-5.314" />
                    </svg>
                    {{ __('Share') }}
                </summary>

                <form action="{{ route('feed.posts.share', $post) }}" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <textarea
                        name="comment"
                        rows="2"
                        placeholder="{{ __('Add a comment (optional)') }}"
                        class="app-textarea min-h-[4rem] border border-[var(--app-border)] bg-[var(--app-background)] p-3"
                        style="border-radius: calc(var(--app-radius) - 0.125rem)"
                    >{{ old('comment') }}</textarea>

                    <button type="submit" class="app-btn-secondary">
                        {{ __('Share to feed') }}
                    </button>
                </form>
            </details>
        @endcan

        @php($isSaved = in_array($post->id, $savedPostIds ?? [], true))
        @if ($isSaved)
            <form method="POST" action="{{ route('saved.posts.destroy', $post) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="app-action is-active">{{ __('Saved') }}</button>
            </form>
        @else
            <form method="POST" action="{{ route('saved.posts.store', $post) }}">
                @csrf
                <button type="submit" class="app-action">{{ __('Save') }}</button>
            </form>
        @endif
    </div>

    <div class="mt-4 space-y-4" x-show="open" x-cloak>
        @forelse ($post->comments as $comment)
            <div class="flex gap-3">
                <div class="app-avatar h-9 w-9 text-xs">
                    {{ str($comment->user?->name ?? '?')->substr(0, 1)->upper() }}
                </div>

                <div class="min-w-0 flex-1 rounded-lg bg-[var(--app-surface-hover)] px-3 py-2">
                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                        <p class="text-sm font-medium text-[var(--app-text)]">
                            {{ $comment->user?->name ?? __('Unknown') }}
                        </p>
                        <time
                            datetime="{{ $comment->created_at?->toIso8601String() }}"
                            class="text-xs text-[var(--app-text-muted)]"
                        >
                            {{ $comment->created_at?->diffForHumans() }}
                        </time>
                    </div>

                    <p class="mt-1 whitespace-pre-line text-sm leading-relaxed text-[var(--app-text)] opacity-90">
                        {{ $comment->body }}
                    </p>

                    @can('delete', $comment)
                        <form
                            action="{{ route('feed.posts.comments.destroy', [$post, $comment]) }}"
                            method="POST"
                            class="mt-2"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-[var(--app-danger)] opacity-80 hover:opacity-100">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        @empty
            <p class="text-sm text-[var(--app-text-muted)]">
                {{ __('Be the first to comment.') }}
            </p>
        @endforelse

        @can('comment', $post)
            <form action="{{ route('feed.posts.comments.store', $post) }}" method="POST" class="flex gap-3">
                @csrf
                <input type="hidden" name="comment_post_id" value="{{ $post->id }}">
                <div class="app-avatar h-9 w-9 text-xs">
                    {{ str(auth()->user()->name)->substr(0, 1)->upper() }}
                </div>
                <div class="min-w-0 flex-1 space-y-2">
                    <textarea
                        name="body"
                        rows="2"
                        required
                        maxlength="2000"
                        placeholder="{{ __('Add a comment...') }}"
                        class="app-textarea min-h-[3.5rem] border border-[var(--app-border)] bg-[var(--app-background)] p-3 text-sm"
                        style="border-radius: calc(var(--app-radius) - 0.125rem)"
                    >{{ (int) old('comment_post_id') === $post->id ? old('body') : '' }}</textarea>

                    @if ($errors->has('body') && (int) old('comment_post_id') === $post->id)
                        <p class="text-sm text-[var(--app-danger)]">{{ $errors->first('body') }}</p>
                    @endif

                    <button type="submit" class="app-btn-primary">
                        {{ __('Post comment') }}
                    </button>
                </div>
            </form>
        @endcan
    </div>
</div>
