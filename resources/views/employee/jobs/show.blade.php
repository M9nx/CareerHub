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
                        <form method="POST" action="{{ route('employee.applications.store') }}">
                            @csrf

                            <input
                                type="hidden"
                                name="job_posting_id"
                                value="{{ $jobPosting->id }}"
                            >

                            <div>
                                <label
                                    for="cover_letter"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    {{ __('Cover Letter') }}
                                </label>

                                <textarea
                                    id="cover_letter"
                                    name="cover_letter"
                                    rows="6"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                >{{ old('cover_letter') }}</textarea>

                                @error('cover_letter')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            @error('job_posting_id')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            <div class="mt-6 flex items-center gap-4">
                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500"
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
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
