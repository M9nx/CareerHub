@props([
    'jobs' => collect(),
])

<section class="app-sidebar-card">
    <h2 class="app-sidebar-title">{{ __('Recent jobs') }}</h2>

    @forelse ($jobs as $job)
        <article @class(['border-t border-[var(--app-border)] pt-3' => ! $loop->first, 'pb-3' => ! $loop->last])>
            @php
                $href = auth()->user()?->role === \App\Enums\UserRole::Employee
                    && Route::has('employee.jobs.show')
                    ? route('employee.jobs.show', $job)
                    : (Route::has('employer.jobs.edit') && $job->employer_id === auth()->id()
                        ? route('employer.jobs.edit', $job)
                        : null);
            @endphp

            @if ($href)
                <a href="{{ $href }}" class="font-medium text-[var(--app-text)] underline-offset-4 hover:underline">
                    {{ $job->title }}
                </a>
            @else
                <p class="font-medium text-[var(--app-text)]">{{ $job->title }}</p>
            @endif

            <p class="mt-1 text-xs text-[var(--app-text-muted)]">
                {{ $job->employer?->employerProfile?->company_name
                    ?? $job->employer?->name
                    ?? __('Employer') }}
            </p>
        </article>
    @empty
        <p class="app-empty">{{ __('No published jobs yet.') }}</p>
    @endforelse
</section>
