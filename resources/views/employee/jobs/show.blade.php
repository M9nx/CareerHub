<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <div class="mb-6">
                        <h1 class="text-3xl font-semibold text-gray-900">
                            {{ $jobPosting->title }}
                        </h1>

                        @if ($jobPosting->employer?->employerProfile?->company_name)
                            <p class="mt-2 text-sm text-gray-600">
                                {{ $jobPosting->employer->employerProfile->company_name }}
                            </p>
                        @endif
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Job Description
                        </h2>

                        <div class="mt-3 whitespace-pre-line text-gray-700">
                            {{ $jobPosting->description }}
                        </div>
                    </div>

                    <div class="mt-8 flex items-center gap-4">
                        <button
                            type="button"
                            disabled
                            class="cursor-not-allowed rounded-md bg-gray-400 px-4 py-2 text-sm font-semibold text-white"
                        >
                            Apply
                        </button>

                        <a
                            href="{{ route('employee.jobs.index') }}"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-900"
                        >
                            Back to Jobs
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>