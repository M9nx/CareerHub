<x-app.page :title="__('My Job Postings')" eyebrow="{{ __('Employer') }}" wide>
    <x-slot name="actions">
        <a href="{{ route('employer.jobs.create') }}" class="swiss-btn-primary">
            {{ __('Create Job') }}
        </a>
    </x-slot>

    <div class="app-card p-6">
        <x-app.flash />

        @if ($jobs->isEmpty())
            <p class="app-empty">{{ __("You don't have any job postings yet.") }}</p>
        @else
            <div class="overflow-x-auto">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Created') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jobs as $job)
                            <tr>
                                <td>{{ $job->title }}</td>
                                <td>
                                    <span class="app-badge">{{ $job->status->name }}</span>
                                </td>
                                <td class="tabular-nums opacity-70">{{ $job->created_at->format('Y-m-d') }}</td>
                                <td class="text-right">
                                    <a href="{{ route('employer.jobs.edit', $job) }}" class="app-link">
                                        {{ __('Edit') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app.page>
