<x-app.page :title="__('Search')" :eyebrow="__('Discover')">
    <form method="GET" action="{{ route('search.index') }}" class="app-form mb-8 flex flex-wrap gap-3">
        <input
            type="search"
            name="q"
            value="{{ $query }}"
            placeholder="{{ __('Search people, jobs, companies, posts…') }}"
            class="min-w-[16rem] flex-1"
            autofocus
        >
        <select name="type">
            <option value="" @selected($type === '')>{{ __('All') }}</option>
            <option value="people" @selected($type === 'people')>{{ __('People') }}</option>
            <option value="jobs" @selected($type === 'jobs')>{{ __('Jobs') }}</option>
            <option value="companies" @selected($type === 'companies')>{{ __('Companies') }}</option>
            <option value="posts" @selected($type === 'posts')>{{ __('Posts') }}</option>
        </select>
        <button type="submit" class="swiss-btn-primary">{{ __('Search') }}</button>
    </form>

    @if ($query === '')
        <div class="app-card p-8 text-center">
            <p class="app-empty">{{ __('Enter a keyword to search CareerHub.') }}</p>
        </div>
    @elseif ($isEmpty)
        <div class="app-card p-8 text-center">
            <p class="app-empty">{{ __('No results for “:query”.', ['query' => $query]) }}</p>
        </div>
    @else
        <div class="space-y-8">
            @if (($sections['people'] ?? null)?->isNotEmpty())
                <section class="space-y-3">
                    <h2 class="text-sm font-medium uppercase tracking-[0.18em] text-[var(--app-text-muted)]">
                        {{ __('People') }}
                    </h2>
                    @foreach ($sections['people'] as $person)
                        <a href="{{ route('people.show', $person) }}" class="app-card flex items-center gap-3 p-4 hover:bg-[var(--app-surface-hover)]">
                            @if ($person->avatarUrl())
                                <img src="{{ $person->avatarUrl() }}" alt="" class="h-11 w-11 rounded-full object-cover ring-1 ring-[var(--app-border)]" />
                            @else
                                <div class="app-avatar">{{ str($person->name)->substr(0, 1)->upper() }}</div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-medium text-[var(--app-text)]">{{ $person->name }}</p>
                                <p class="truncate text-sm text-[var(--app-text-muted)]">
                                    {{ $person->headline ?: $person->role?->label() }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </section>
            @endif

            @if (($sections['jobs'] ?? null)?->isNotEmpty())
                <section class="space-y-3">
                    <h2 class="text-sm font-medium uppercase tracking-[0.18em] text-[var(--app-text-muted)]">
                        {{ __('Jobs') }}
                    </h2>
                    @foreach ($sections['jobs'] as $job)
                        <a
                            href="{{ auth()->user()?->role === \App\Enums\UserRole::Employee ? route('employee.jobs.show', $job) : route('feed.index') }}"
                            class="app-card block p-4 hover:bg-[var(--app-surface-hover)]"
                        >
                            <p class="font-medium text-[var(--app-text)]">{{ $job->title }}</p>
                            <p class="mt-1 text-sm text-[var(--app-text-muted)]">
                                {{ $job->employer?->employerProfile?->company_name }}
                                @if (filled($job->location))
                                    · {{ $job->location }}
                                @endif
                            </p>
                        </a>
                    @endforeach
                </section>
            @endif

            @if (($sections['companies'] ?? null)?->isNotEmpty())
                <section class="space-y-3">
                    <h2 class="text-sm font-medium uppercase tracking-[0.18em] text-[var(--app-text-muted)]">
                        {{ __('Companies') }}
                    </h2>
                    @foreach ($sections['companies'] as $company)
                        <a href="{{ route('people.show', $company->user) }}" class="app-card block p-4 hover:bg-[var(--app-surface-hover)]">
                            <p class="font-medium text-[var(--app-text)]">{{ $company->company_name }}</p>
                            <p class="mt-1 text-sm text-[var(--app-text-muted)]">
                                {{ $company->industry ?: $company->location }}
                            </p>
                        </a>
                    @endforeach
                </section>
            @endif

            @if (($sections['posts'] ?? null)?->isNotEmpty())
                <section class="space-y-3">
                    <h2 class="text-sm font-medium uppercase tracking-[0.18em] text-[var(--app-text-muted)]">
                        {{ __('Posts') }}
                    </h2>
                    @foreach ($sections['posts'] as $post)
                        <a href="{{ route('feed.index') }}#post-{{ $post->id }}" class="app-card block p-4 hover:bg-[var(--app-surface-hover)]">
                            <p class="text-sm text-[var(--app-text-muted)]">{{ $post->author?->name }}</p>
                            @if (filled($post->title))
                                <p class="mt-1 font-medium text-[var(--app-text)]">
                                    {{ \Illuminate\Support\Str::limit($post->title, 120) }}
                                </p>
                            @endif
                            <p class="mt-1 text-sm text-[var(--app-text)] opacity-90">
                                {{ \Illuminate\Support\Str::limit($post->body, 160) }}
                            </p>
                        </a>
                    @endforeach
                </section>
            @endif
        </div>
    @endif
</x-app.page>
