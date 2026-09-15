<x-app.page :title="__('Available Jobs')" eyebrow="{{ __('Employee') }}" wide>
    <form method="GET" action="{{ route('employee.jobs.index') }}" class="app-form mb-6 grid gap-3 md:grid-cols-4">
        <input
            type="search"
            name="search"
            value="{{ request('search') }}"
            placeholder="{{ __('Keyword…') }}"
            class="md:col-span-2"
        >
        <input
            type="search"
            name="location"
            value="{{ request('location') }}"
            placeholder="{{ __('Location…') }}"
        >
        <select name="employment_type">
            <option value="">{{ __('Any type') }}</option>
            @foreach ($employmentTypes as $type)
                <option value="{{ $type->value }}" @selected(request('employment_type') === $type->value)>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
        <div class="md:col-span-4">
            <button type="submit" class="swiss-btn-primary">{{ __('Filter jobs') }}</button>
        </div>
    </form>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,22rem)_minmax(0,1fr)]">
        <div class="space-y-3">
            @forelse ($jobs as $job)
                <a
                    href="{{ route('employee.jobs.index', array_filter([
                        'search' => request('search'),
                        'location' => request('location'),
                        'employment_type' => request('employment_type'),
                        'selected' => $job->id,
                        'page' => request('page'),
                    ])) }}"
                    @class([
                        'app-card block p-4 motion-safe:transition hover:bg-[var(--app-surface-hover)]',
                        'ring-2 ring-[var(--app-primary)]' => $selected?->id === $job->id,
                    ])
                >
                    <h2 class="text-base font-medium text-[var(--app-text)]">{{ $job->title }}</h2>
                    @if ($job->employer?->employerProfile?->company_name)
                        <p class="mt-1 text-sm text-[var(--app-text-muted)]">
                            {{ $job->employer->employerProfile->company_name }}
                        </p>
                    @endif
                    <div class="mt-2 flex flex-wrap gap-2 text-xs text-[var(--app-text-muted)]">
                        @if (filled($job->location))
                            <span>{{ $job->location }}</span>
                        @endif
                        @if ($job->employment_type)
                            <span>{{ $job->employment_type->label() }}</span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="app-card p-6">
                    <p class="app-empty">{{ __('No jobs are currently available.') }}</p>
                </div>
            @endforelse

            @if ($jobs->hasPages())
                <div class="pt-2">
                    {{ $jobs->links() }}
                </div>
            @endif
        </div>

        <div class="min-w-0">
            @if ($selected)
                <article class="app-card p-6 sm:p-8 xl:sticky xl:top-24">
                    <h2 class="text-2xl font-medium text-[var(--app-text)]">{{ $selected->title }}</h2>

                    @if ($selected->employer?->employerProfile?->company_name)
                        <p class="mt-2 text-sm text-[var(--app-text-muted)]">
                            {{ $selected->employer->employerProfile->company_name }}
                        </p>
                    @endif

                    <div class="mt-3 flex flex-wrap gap-2 text-sm text-[var(--app-text-muted)]">
                        @if (filled($selected->location))
                            <span>{{ $selected->location }}</span>
                        @endif
                        @if ($selected->employment_type)
                            <span>{{ $selected->employment_type->label() }}</span>
                        @endif
                    </div>

                    <div class="swiss-rule my-6"></div>

                    <h3 class="text-sm font-medium uppercase tracking-[0.18em] text-[var(--app-text-muted)]">
                        {{ __('Job Description') }}
                    </h3>
                    <div class="mt-3 max-w-[65ch] whitespace-pre-line text-sm leading-relaxed opacity-90">
                        {{ $selected->description }}
                    </div>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('employee.jobs.show', $selected) }}" class="swiss-btn-primary">
                            {{ __('Open & apply') }}
                        </a>
                    </div>
                </article>
            @else
                <div class="app-card p-8 text-center xl:sticky xl:top-24">
                    <p class="app-empty">{{ __('Select a job to preview details.') }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app.page>
