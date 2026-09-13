<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Edit Job Posting') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <p class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </p>
                    @endif

                    <form method="POST" action="{{ route('employer.jobs.update', $job) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input
                                id="title"
                                name="title"
                                type="text"
                                class="mt-1 block w-full"
                                :value="old('title', $job->title)"
                                required
                                autofocus
                                maxlength="255"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea
                                id="description"
                                name="description"
                                rows="8"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                            >{{ old('description', $job->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select
                                id="status"
                                name="status"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                            >
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}" @selected(old('status', $job->status->value) === $status->value)>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a
                                href="{{ route('employer.jobs.index') }}"
                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Job') }}
                            </x-primary-button>
                        </div>
                    </form>

                    @if ($job->status === \App\Enums\JobPostingStatus::Draft)
                        <form method="POST" action="{{ route('employer.jobs.publish', $job) }}" class="mt-6">
                            @csrf
                            <x-primary-button>
                                {{ __('Publish') }}
                            </x-primary-button>
                        </form>
                    @endif

                    @if ($job->status === \App\Enums\JobPostingStatus::Published)
                        <form method="POST" action="{{ route('employer.jobs.close', $job) }}" class="mt-6">
                            @csrf
                            <x-primary-button>
                                {{ __('Close') }}
                            </x-primary-button>
                        </form>
                    @endif

                    <div class="mt-8 border-t border-gray-200 pt-6 dark:border-gray-700">
                        <form method="POST" action="{{ route('employer.jobs.destroy', $job) }}">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>
                                {{ __('Delete Job') }}
                            </x-danger-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
