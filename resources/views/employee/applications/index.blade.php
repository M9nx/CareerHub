<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('My Applications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <p class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </p>
                    @endif

                    @if ($applications->isEmpty())
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __('You have not submitted any applications yet.') }}
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr class="text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        <th class="px-4 py-3">{{ __('Job') }}</th>
                                        <th class="px-4 py-3">{{ __('Status') }}</th>
                                        <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($applications as $application)
                                        <tr>
                                            <td class="px-4 py-3">
                                                {{ $application->jobPosting->title }}
                                            </td>

                                            <td class="px-4 py-3">
                                                @include(
                                                    'employee.applications.partials.status-badge',
                                                    ['application' => $application]
                                                )
                                            </td>

                                            <td class="px-4 py-3 text-right">
                                                @if (in_array($application->status, [
                                                    \App\Enums\ApplicationStatus::Submitted,
                                                    \App\Enums\ApplicationStatus::UnderReview,
                                                ], true))
                                                    <form
                                                        method="POST"
                                                        action="{{ route('employee.applications.cancel', $application) }}"
                                                    >
                                                        @csrf
                                                        @method('PATCH')

                                                        <x-danger-button>
                                                            {{ __('Cancel') }}
                                                        </x-danger-button>
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
            </div>
        </div>
    </div>
</x-app-layout>