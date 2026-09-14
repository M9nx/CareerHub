<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Applications') }}
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
                            {{ __('No applications have been submitted yet.') }}
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr class="text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        <th class="px-4 py-3">{{ __('Applicant') }}</th>
                                        <th class="px-4 py-3">{{ __('Job') }}</th>
                                        <th class="px-4 py-3">{{ __('Status') }}</th>
                                        <th class="px-4 py-3">{{ __('Review') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($applications as $application)
                                        @php
                                            $nextStatuses = $statusTransition->allowedStatuses($application);
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-3">
                                                <a
                                                    href="{{ route('employer.applications.show', $application) }}"
                                                    class="font-semibold text-gray-900 underline-offset-4 hover:underline dark:text-gray-100"
                                                >
                                                    {{ $application->employee->name }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ $application->jobPosting->title }}
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ str($application->status->name)->headline() }}
                                            </td>
                                            <td class="px-4 py-3">
                                                @if ($nextStatuses !== [])
                                                    <form
                                                        method="POST"
                                                        action="{{ route('employer.applications.update', $application) }}"
                                                        class="flex items-center gap-3"
                                                    >
                                                        @csrf
                                                        @method('PATCH')

                                                        <select
                                                            name="status"
                                                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                                                        >
                                                            @foreach ($nextStatuses as $status)
                                                                <option value="{{ $status->value }}">
                                                                    {{ str($status->name)->headline() }}
                                                                </option>
                                                            @endforeach
                                                        </select>

                                                        <x-primary-button>
                                                            {{ __('Update') }}
                                                        </x-primary-button>
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
