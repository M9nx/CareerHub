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

                    <div class="mt-8">
                        <form method="POST" action="{{ route('employee.applications.store') }}" class="space-y-6">
                            @csrf

                            <input
                                type="hidden"
                                name="job_posting_id"
                                value="{{ $jobPosting->id }}"
                            >

                            <div>
                                <x-input-label for="cover_letter" :value="__('Cover Letter')" />
                                <textarea
                                    id="cover_letter"
                                    name="cover_letter"
                                    rows="6"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                                >{{ old('cover_letter') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('cover_letter')" />
                            </div>

                            <x-input-error class="mt-2" :messages="$errors->get('job_posting_id')" />

                            <div class="flex items-center gap-4">
                                <x-primary-button>
                                    {{ __('Apply') }}
                                </x-primary-button>

                                <a
                                    href="{{ route('employee.jobs.index') }}"
                                    class="text-sm font-semibold text-indigo-600 underline-offset-4 hover:underline dark:text-indigo-400"
                                >
                                    {{ __('Back to Jobs') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
