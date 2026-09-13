<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">
                    Available Jobs
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Browse available job opportunities.
                </p>
            </div>

            <div class="space-y-4">
                @forelse ($jobs as $job)
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">

                            <h2 class="text-xl font-semibold text-gray-900">
                                {{ $job->title }}
                            </h2>

                            @if ($job->employer?->employerProfile?->company_name)
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ $job->employer->employerProfile->company_name }}
                                </p>
                            @endif

                            <div class="mt-4">
                                <a
                                    href="{{ route('employee.jobs.show', $job) }}"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-900"
                                >
                                    View Job
                                </a>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            No jobs are currently available.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $jobs->links() }}
            </div>

        </div>
    </div>
</x-app-layout>