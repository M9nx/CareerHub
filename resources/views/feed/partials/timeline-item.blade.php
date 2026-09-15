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

        <article id="post-{{ $post->id }}" class="app-card overflow-hidden">
            <div class="p-4 sm:p-5">
                <div class="flex items-start gap-3">
                    @if ($author?->avatarUrl())
                        <a href="{{ route('people.show', $author) }}" class="shrink-0">
                            <img
                                src="{{ $author->avatarUrl() }}"
                                alt=""
                                class="h-11 w-11 rounded-full object-cover ring-1 ring-[var(--app-border)]"
                            />
                        </a>
                    @else
                        <a href="{{ $author ? route('people.show', $author) : '#' }}" class="app-avatar shrink-0">
                            {{ str($author?->name ?? '?')->substr(0, 1)->upper() }}
                        </a>
                    @endif

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                            @if ($author)
                                <a href="{{ route('people.show', $author) }}" class="font-medium text-[var(--app-text)] hover:underline">
                                    {{ $author->name }}
                                </a>
                            @else
                                <p class="font-medium text-[var(--app-text)]">
                                    {{ __('Unknown author') }}
                                </p>
                            @endif
                            <span class="opacity-30">·</span>
                            <time
                                datetime="{{ $item->occurredAt->toIso8601String() }}"
                                class="text-sm text-[var(--app-text-muted)]"
                            >
                                {{ $item->occurredAt->timezone(config('app.timezone'))->diffForHumans() }}
                            </time>
                        </div>

                        @if (filled($author?->headline))
                            <p class="mt-1 text-sm text-[var(--app-text-muted)]">
                                {{ $author->headline }}
                            </p>
                        @endif

                        <div class="mt-1 flex items-center gap-2">
                            <span class="app-badge">
                                {{ $post->author_role?->label() ?? __('Author') }}
                            </span>
                        </div>
                    </div>
                </div>

                @if ($post->title && ! $post->isShared())
                    <h2 class="mt-4 text-base font-medium leading-snug text-[var(--app-text)]">
                        {{ $post->title }}
                    </h2>
                @endif

                <p class="mt-3 max-w-[60ch] whitespace-pre-line text-sm leading-relaxed text-[var(--app-text)] opacity-90">
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

                @include('feed.partials.post-actions', [
                    'post' => $post,
                    'liked' => $liked,
                    'likeCount' => $likeCount,
                    'savedPostIds' => $savedPostIds ?? [],
                ])
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
