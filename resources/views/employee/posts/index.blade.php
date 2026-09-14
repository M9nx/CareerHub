<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('My Posts') }}
            </h2>

            @can('create', App\Models\Post::class)
                <a
                    href="{{ route('employee.posts.create') }}"
                    class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white"
                >
                    {{ __('Create Post') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <p class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </p>
                    @endif

                    @if ($posts->isEmpty())
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __('You have not created any posts yet.') }}
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr class="text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        <th class="px-4 py-3">{{ __('Title') }}</th>
                                        <th class="px-4 py-3">{{ __('Status') }}</th>
                                        <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($posts as $post)
                                        <tr>
                                            <td class="px-4 py-3">
                                                {{ $post->title }}
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ str($post->status->name)->headline() }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-end gap-3">
                                                    <a
                                                        href="{{ route('employee.posts.edit', $post) }}"
                                                        class="text-sm font-semibold text-indigo-600 underline-offset-4 hover:underline dark:text-indigo-400"
                                                    >
                                                        {{ __('Edit') }}
                                                    </a>

                                                    <form
                                                        method="POST"
                                                        action="{{ route('employee.posts.destroy', $post) }}"
                                                    >
                                                        @csrf
                                                        @method('DELETE')

                                                        <x-danger-button>
                                                            {{ __('Delete') }}
                                                        </x-danger-button>
                                                    </form>
                                                </div>
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
