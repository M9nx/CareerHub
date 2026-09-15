<x-app.page :title="__('Available Jobs')" eyebrow="{{ __('Employee') }}" wide>
    <form method="GET" action="{{ route('employee.jobs.index') }}" class="app-form mb-6 flex flex-wrap gap-3">
        <input
            type="search"
            name="search"
            value="{{ request('search') }}"
            placeholder="{{ __('Search jobs by title…') }}"
            class="min-w-[16rem] flex-1"
        >
        <button type="submit" class="swiss-btn-primary">{{ __('Search') }}</button>
    </form>

    @forelse ($jobs as $job)
        <article class="app-card mb-4 p-6">
            <h2 class="text-lg font-normal">{{ $job->title }}</h2>

            @if ($job->employer?->employerProfile?->company_name)
                <p class="mt-1 text-sm opacity-60">
                    {{ $job->employer->employerProfile->company_name }}
                </p>
            @endif

            <div class="mt-4">
                <a href="{{ route('employee.jobs.show', $job) }}" class="app-link">
                    {{ __('View Job') }}
                </a>
            </div>
        </article>
    @empty
        <div class="app-card p-6">
            <p class="app-empty">{{ __('No jobs are currently available.') }}</p>
        </div>
    @endforelse

    @if ($jobs->hasPages())
        <div class="mt-6">
            {{ $jobs->links() }}
        </div>
    @endif
</x-app.page>
