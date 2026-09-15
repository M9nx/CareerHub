<x-app.page :title="__('Application Details')" eyebrow="{{ __('Employer') }}" narrow>
    <div class="app-card p-6">
        <x-app.flash />

        <p class="text-sm opacity-60">{{ $application->jobPosting->title }}</p>

        <h2 class="mt-4 text-xl font-normal">{{ $application->employee->name }}</h2>

        <p class="mt-2 text-sm opacity-70">
            {{ str($application->status->name)->headline() }}
        </p>

        <div class="app-divider">
            <h3 class="text-lg font-normal">{{ __('Cover Letter') }}</h3>
            <div class="mt-3 max-w-[60ch] whitespace-pre-line leading-relaxed opacity-80">
                {{ $application->cover_letter ?: __('No cover letter was provided.') }}
            </div>
        </div>

        <div class="mt-8">
            <a href="{{ route('employer.applications.index') }}" class="app-link-muted">
                {{ __('Back to Applications') }}
            </a>
        </div>
    </div>
</x-app.page>
