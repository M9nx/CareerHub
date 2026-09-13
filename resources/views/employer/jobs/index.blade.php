<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('My Job Postings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6 flex items-center justify-end">
                        <a
                            href="{{ route('employer.jobs.create') }}"
                            class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white"
                        >
                            {{ __('Create Job') }}
                        </a>
                    </div>

                    @if (session('success'))
                        <p class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </p>
                    @endif

                    @if ($jobs->isEmpty())
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __("You don't have any job postings yet.") }}
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr class="text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        <th class="px-4 py-3">{{ __('Title') }}</th>
                                        <th class="px-4 py-3">{{ __('Status') }}</th>
                                        <th class="px-4 py-3">{{ __('Created') }}</th>
                                        <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($jobs as $job)
                                        <tr>
                                            <td class="px-4 py-3">
                                                {{ $job->title }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <span @class([
                                                    'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                                                    'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200' => $job->status === \App\Enums\JobPostingStatus::Published,
                                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200' => $job->status === \App\Enums\JobPostingStatus::Draft,
                                                    'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' => $job->status === \App\Enums\JobPostingStatus::Closed,
                                                    'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200' => $job->status === \App\Enums\JobPostingStatus::Archived,
                                                ])>
                                                    {{ $job->status->name }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                                {{ $job->created_at->format('Y-m-d') }}
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <a
                                                    href="{{ route('employer.jobs.edit', $job) }}"
                                                    class="text-sm font-semibold text-gray-900 underline-offset-4 hover:underline dark:text-gray-100"
                                                >
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
            </div>
        </div>
    </div>
</x-app-layout>
