<x-app.page :title="__('Applications')" eyebrow="{{ __('Employer') }}" wide>
    <div class="app-card p-6">
        <x-app.flash />

        @if ($applications->isEmpty())
            <p class="app-empty">{{ __('No applications have been submitted yet.') }}</p>
        @else
            <div class="overflow-x-auto">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>{{ __('Applicant') }}</th>
                            <th>{{ __('Job') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Review') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $application)
                            @php
                                $nextStatuses = $statusTransition->allowedStatuses($application);
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('employer.applications.show', $application) }}" class="app-link">
                                        {{ $application->employee->name }}
                                    </a>
                                </td>
                                <td>{{ $application->jobPosting->title }}</td>
                                <td>{{ str($application->status->name)->headline() }}</td>
                                <td>
                                    @if ($nextStatuses !== [])
                                        <form
                                            method="POST"
                                            action="{{ route('employer.applications.update', $application) }}"
                                            class="app-form flex flex-wrap items-center gap-3"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <select name="status">
                                                @foreach ($nextStatuses as $status)
                                                    <option value="{{ $status->value }}">
                                                        {{ str($status->name)->headline() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="swiss-btn-primary">
                                                {{ __('Update') }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app.page>
