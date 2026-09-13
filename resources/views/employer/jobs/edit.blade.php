<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h1 class="text-2xl font-semibold">Edit Job Posting</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Update your job posting.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 rounded-md bg-red-100 p-4 text-sm text-red-800">
                            <ul class="list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('employer.jobs.update', $job) }}"
                        class="space-y-6"
                    >
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                Title
                            </label>

                            <input
                                id="title"
                                name="title"
                                type="text"
                                value="{{ old('title', $job->title) }}"
                                required
                                maxlength="255"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                            >
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="8"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                            >{{ old('description', $job->description) }}</textarea>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                Status
                            </label>

                            <select
                                id="status"
                                name="status"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                            >
                                @foreach (['draft', 'published', 'closed', 'archived'] as $status)
                                    <option
                                        value="{{ $status }}"
                                        @selected(old('status', $job->status->value) === $status)
                                    >
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a
                                href="{{ route('employer.jobs.index') }}"
                                class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700"
                            >
                                Update Job
                            </button>
                        </div>
                    </form>

                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <form
                            method="POST"
                            action="{{ route('employer.jobs.destroy', $job) }}"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500"
                            >
                                Delete Job
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>