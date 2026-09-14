<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Available Jobs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('employee.jobs.index') }}" class="mb-6">
                <div class="flex gap-3">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="{{ __('Search jobs by title...') }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                    >

                    <button
                        type="submit"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400"
                    >
                        {{ __('Search') }}
                    </button>
                </div>
            </form>

            @forelse ($jobs as $job)
                <article class="mb-4 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold">
                            {{ $job->title }}
                        </h3>

                        @if ($job->employer?->employerProfile?->company_name)
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ $job->employer->employerProfile->company_name }}
                            </p>
                        @endif

                        <div class="mt-4">
                            <a
                                href="{{ route('employee.jobs.show', $job) }}"
                                class="text-sm font-semibold text-indigo-600 underline-offset-4 hover:underline dark:text-indigo-400"
                            >
                                {{ __('View Job') }}
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-600 dark:text-gray-400">
                        {{ __('No jobs are currently available.') }}
                    </div>
                </div>
            @endforelse

            @if ($jobs->hasPages())
                <div class="mt-6">
                    {{ $jobs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
