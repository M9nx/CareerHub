<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Application Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <p class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </p>
                    @endif

                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $application->jobPosting->title }}
                    </p>

                    <h3 class="mt-4 text-lg font-semibold">
                        {{ $application->employee->name }}
                    </h3>

                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ str($application->status->name)->headline() }}
                    </p>

                    <div class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                        <h4 class="text-lg font-semibold">
                            {{ __('Cover Letter') }}
                        </h4>

                        <div class="mt-3 whitespace-pre-line text-gray-700 dark:text-gray-300">
                            {{ $application->cover_letter ?: __('No cover letter was provided.') }}
                        </div>
                    </div>

                    <div class="mt-8">
                        <a
                            href="{{ route('employer.applications.index') }}"
                            class="text-sm font-semibold text-indigo-600 underline-offset-4 hover:underline dark:text-indigo-400"
                        >
                            {{ __('Back to Applications') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
