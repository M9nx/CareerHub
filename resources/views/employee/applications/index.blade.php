<x-app.page :title="__('My Applications')" eyebrow="{{ __('Employee') }}" wide>
    <div class="app-card p-6">
        <x-app.flash />

        @if ($applications->isEmpty())
            <p class="app-empty">{{ __('You have not submitted any applications yet.') }}</p>
        @else
            <div class="overflow-x-auto">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>{{ __('Job') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $application)
                            <tr>
                                <td>{{ $application->jobPosting->title }}</td>
                                <td>
                                    @include('employee.applications.partials.status-badge', ['application' => $application])
                                </td>
                                <td class="text-right">
                                    <div class="inline-flex items-center justify-end gap-3">
                                        <a href="{{ route('employee.applications.show', $application) }}" class="app-link">
                                            {{ __('View') }}
                                        </a>

                                        @if (in_array($application->status, [
                                            \App\Enums\ApplicationStatus::Submitted,
                                            \App\Enums\ApplicationStatus::UnderReview,
                                        ], true))
                                            <form method="POST" action="{{ route('employee.applications.cancel', $application) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="swiss-btn-danger">
                                                    {{ __('Cancel') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app.page>
