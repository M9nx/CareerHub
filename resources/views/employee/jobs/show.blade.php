<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ $jobPosting->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if ($jobPosting->employer?->employerProfile?->company_name)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $jobPosting->employer->employerProfile->company_name }}
                        </p>
                    @endif

                    <div class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                        <h3 class="text-lg font-semibold">
                            {{ __('Job Description') }}
                        </h3>

                        <div class="mt-3 whitespace-pre-line text-gray-700 dark:text-gray-300">
                            {{ $jobPosting->description }}
                        </div>
                    </div>

                    <div class="mt-8 flex items-center gap-4">
                        <button
                            type="button"
                            disabled
                            class="inline-flex cursor-not-allowed items-center rounded-md bg-gray-400 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white"
                        >
                            {{ __('Apply') }}
                        </button>

                        <a
                            href="{{ route('employee.jobs.index') }}"
                            class="text-sm font-semibold text-indigo-600 underline-offset-4 hover:underline dark:text-indigo-400"
                        >
                            {{ __('Back to Jobs') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
