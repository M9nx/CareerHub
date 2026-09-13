<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 flex items-center justify-between">
                        <h1 class="text-2xl font-semibold">
                            My Job Postings
                        </h1>

                        <a
                            href="{{ route('employer.jobs.create') }}"
                            class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700"
                        >
                            Create Job
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 rounded-md bg-green-100 p-4 text-sm text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($jobs->isEmpty())
                        <p class="text-gray-600">
                            You don't have any job postings yet.
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="text-left text-sm font-semibold text-gray-700">
                                        <th class="px-4 py-3">Title</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3">Created</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($jobs as $job)
                                        <tr>
                                            <td class="px-4 py-3">
                                                {{ $job->title }}
                                            </td>

                                            <td class="px-4 py-3">
                                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                                    {{ match ($job->status->value) {
                                                        'published' => 'bg-green-100 text-green-800',
                                                        'draft' => 'bg-yellow-100 text-yellow-800',
                                                        'closed' => 'bg-gray-100 text-gray-800',
                                                        'archived' => 'bg-red-100 text-red-800',
                                                        default => 'bg-gray-100 text-gray-800',
                                                    } }}"
                                                >
                                                    {{ ucfirst($job->status->value) }}
                                                </span>
                                            </td>

                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                {{ $job->created_at->format('Y-m-d') }}
                                            </td>

                                            <td class="px-4 py-3 text-right">
                                                <a
                                                    href="{{ route('employer.jobs.edit', $job) }}"
                                                    class="text-sm font-semibold text-gray-900 hover:underline"
                                                >
                                                    Edit
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