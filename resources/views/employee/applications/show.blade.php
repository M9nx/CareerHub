<x-app.page :title="__('Application Details')" eyebrow="{{ __('Employee') }}" narrow>
    <div class="app-card p-6">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-lg font-normal">{{ $application->jobPosting->title }}</h2>
            <a href="{{ route('employee.applications.index') }}" class="app-link-muted">
                {{ __('Back to Applications') }}
            </a>
        </div>

        <p class="mb-6 text-sm opacity-80">
            <span class="font-medium">{{ __('Current Status:') }}</span>
            {{ str($application->status->name)->headline() }}
        </p>

        @include('employee.applications.partials.timeline', ['application' => $application])
    </div>
</x-app.page>
