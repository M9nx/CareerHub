<x-app.page :title="$jobPosting->title" eyebrow="{{ __('Employee') }}" narrow>
    <div class="app-card p-6">
        @if ($jobPosting->employer?->employerProfile?->company_name)
            <p class="text-sm opacity-60">
                {{ $jobPosting->employer->employerProfile->company_name }}
            </p>
        @endif

        <div class="mt-3 flex flex-wrap gap-2 text-sm text-[var(--app-text-muted)]">
            @if (filled($jobPosting->location))
                <span>{{ $jobPosting->location }}</span>
            @endif
            @if ($jobPosting->employment_type)
                <span>{{ $jobPosting->employment_type->label() }}</span>
            @endif
        </div>

        <div class="app-divider">
            <h2 class="text-lg font-normal">{{ __('Job Description') }}</h2>
            <div class="mt-3 max-w-[60ch] whitespace-pre-line leading-relaxed opacity-80">
                {{ $jobPosting->description }}
            </div>
        </div>

        <form method="POST" action="{{ route('employee.applications.store') }}" class="app-form mt-8 space-y-6">
            @csrf
            <input type="hidden" name="job_posting_id" value="{{ $jobPosting->id }}">

            <div>
                <x-input-label for="cover_letter" :value="__('Cover Letter')" />
                <textarea id="cover_letter" name="cover_letter" rows="6">{{ old('cover_letter') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('cover_letter')" />
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('job_posting_id')" />

            <div class="flex flex-wrap items-center gap-4">
                <button type="submit" class="swiss-btn-primary">{{ __('Apply') }}</button>
                <a href="{{ route('employee.jobs.index', ['selected' => $jobPosting->id]) }}" class="app-link-muted">{{ __('Back to Jobs') }}</a>
            </div>
        </form>
    </div>
</x-app.page>
