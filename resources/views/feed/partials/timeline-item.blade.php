@php
    use App\Enums\UserRole;
    use App\Support\Timeline\TimelineItemType;
@endphp

@switch($item->type)
    @case(TimelineItemType::Post)
        @php
            $post = $item->subject;
            $author = $post->author;
            $liked = auth()->check() && $post->reactions->contains(
                fn ($reaction) => $reaction->user_id === auth()->id(),
            );
            $likeCount = $post->reactions->count();
            $sharedPost = $post->sharedPost;
        @endphp

        <article class="app-card overflow-hidden">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="app-avatar">
                        {{ str($author?->name ?? '?')->substr(0, 1)->upper() }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                            <p class="font-medium text-[var(--landing-ink)]">
                                {{ $author?->name ?? __('Unknown author') }}
                            </p>
                            <span class="opacity-30">·</span>
                            <time
                                datetime="{{ $item->occurredAt->toIso8601String() }}"
                                class="text-sm opacity-50"
                            >
                                {{ $item->occurredAt->timezone(config('app.timezone'))->format('M j, Y g:i A') }}
                            </time>
                        </div>

                        <div class="mt-1 flex items-center gap-2">
                            <span class="app-badge">
                                {{ $post->author_role?->label() ?? __('Author') }}
                            </span>
                        </div>
                    </div>
                </div>

                @if ($post->title && ! $post->isShared())
                    <h2 class="mt-5 text-lg font-normal leading-snug text-[var(--landing-ink)]">
                        {{ $post->title }}
                    </h2>
                @endif

                <p class="mt-4 max-w-[60ch] whitespace-pre-line text-base leading-relaxed opacity-80">
                    {{ $post->body }}
                </p>

                @if ($sharedPost)
                    <div class="app-embed">
                        <p class="swiss-eyebrow">
                            {{ __('Originally by :name', ['name' => $sharedPost->author?->name ?? __('Unknown author')]) }}
                        </p>

                        @if ($sharedPost->title)
                            <p class="mt-3 font-medium">
                                {{ $sharedPost->title }}
                            </p>
                        @endif

                        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed opacity-80">
                            {{ $sharedPost->body }}
                        </p>

                        @include('feed.partials.attachments', ['attachments' => $sharedPost->attachments])
                    </div>
                @endif

                @include('feed.partials.attachments', ['attachments' => $post->attachments])

                <div class="mt-6 flex flex-wrap items-center gap-2 border-t border-[var(--landing-rule)] pt-4">
                    @can('react', $post)
                        <form action="{{ route('feed.posts.react', $post) }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                @class(['app-action', 'is-active' => $liked])
                            >
                                <svg class="h-5 w-5" fill="{{ $liked ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.633 10.25c.806 0 1.533-.278 2.107-.756l.478-.396c.915-.756 2.227-1.036 3.432-.78 1.653.356 2.826 1.857 2.826 3.582v.364c0 .621-.504 1.125-1.125 1.125H9.75a3 3 0 0 1-3-3v-.364c0-1.725 1.173-3.226 2.826-3.582.42-.09.857-.045 1.247.13" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                                {{ __('Like') }}
                                @if ($likeCount > 0)
                                    <span class="tabular-nums">({{ $likeCount }})</span>
                                @endif
                            </button>
                        </form>
                    @endcan

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
                                    class="app-textarea min-h-[4rem] border border-[var(--landing-rule)] bg-[var(--landing-canvas)] p-3"
                                    style="border-radius: calc(var(--landing-radius) - 0.125rem)"
                                >{{ old('comment') }}</textarea>

                                <button type="submit" class="swiss-btn-secondary">
                                    {{ __('Share to feed') }}
                                </button>
                            </form>
                        </details>
                    @endcan
                </div>
            </div>
        </article>
        @break

    @case(TimelineItemType::JobPublished)
        @php($job = $item->subject)

        <article class="app-card overflow-hidden">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="app-avatar">
                        {{ str($item->actor?->name ?? '?')->substr(0, 1)->upper() }}
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="swiss-eyebrow">{{ __('Job published') }}</p>

                        <div class="mt-2 flex flex-wrap items-start justify-between gap-3">
                            <div>
                                @if (auth()->user()?->role === UserRole::Employer && $job->employer_id === auth()->id())
                                    <a
                                        href="{{ route('employer.jobs.edit', $job) }}"
                                        class="text-lg font-normal text-[var(--landing-ink)] underline-offset-4 hover:underline"
                                    >
                                        {{ $job->title }}
                                    </a>
                                @elseif (auth()->user()?->role === UserRole::Employee)
                                    <a
                                        href="{{ route('employee.jobs.show', $job) }}"
                                        class="text-lg font-normal text-[var(--landing-ink)] underline-offset-4 hover:underline"
                                    >
                                        {{ $job->title }}
                                    </a>
                                @else
                                    <h2 class="text-lg font-normal text-[var(--landing-ink)]">
                                        {{ $job->title }}
                                    </h2>
                                @endif

                                <p class="mt-1 text-sm opacity-50">
                                    {{ __('By :name', ['name' => $item->actor?->name ?? __('Unknown employer')]) }}
                                </p>
                            </div>

                            <span class="app-badge">{{ __('Employer') }}</span>
                        </div>
                    </div>
                </div>

                <p class="mt-4 max-w-[60ch] text-sm leading-relaxed opacity-70">
                    {{ \Illuminate\Support\Str::limit($job->description, 280) }}
                </p>
            </div>
        </article>
        @break

    @case(TimelineItemType::ApplicationEvent)
        @php($application = $item->subject)

        <article class="app-card overflow-hidden">
            <div class="p-6">
                <p class="swiss-eyebrow">{{ __('Application update') }}</p>

                <div class="mt-3 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-normal text-[var(--landing-ink)]">
                            {{ $application->jobPosting?->title ?? __('Unknown job') }}
                        </h2>

                        <p class="mt-1 text-sm opacity-50">
                            @if (auth()->user()?->role === UserRole::Employer)
                                {{ __('Applicant: :name', ['name' => $item->actor?->name ?? __('Unknown applicant')]) }}
                            @else
                                {{ __('Your application') }}
                            @endif
                        </p>
                    </div>

                    <span class="app-badge">
                        {{ str($application->status->name)->headline() }}
                    </span>
                </div>

                <div class="mt-5">
                    @if (auth()->user()?->role === UserRole::Employer)
                        <a
                            href="{{ route('employer.applications.show', $application) }}"
                            class="text-sm font-medium text-[var(--landing-accent)] underline-offset-4 hover:underline"
                        >
                            {{ __('Review application') }}
                        </a>
                    @elseif (auth()->user()?->role === UserRole::Employee)
                        <a
                            href="{{ route('employee.applications.show', $application) }}"
                            class="text-sm font-medium text-[var(--landing-accent)] underline-offset-4 hover:underline"
                        >
                            {{ __('View application') }}
                        </a>
                    @endif
                </div>
            </div>
        </article>
        @break
@endswitch
