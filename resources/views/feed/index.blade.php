<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Feed') }}
            </h2>

            @can('create', \App\Models\Post::class)
                @if (auth()->user()?->role === \App\Enums\UserRole::Employer)
                    <a
                        href="{{ route('employer.posts.create') }}"
                        class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white"
                    >
                        {{ __('Create Post') }}
                    </a>
                @elseif (auth()->user()?->role === \App\Enums\UserRole::Employee)
                    <a
                        href="{{ route('employee.posts.create') }}"
                        class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white"
                    >
                        {{ __('Create Post') }}
                    </a>
                @endif
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-6 overflow-hidden bg-white p-4 shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <p class="text-sm text-red-600 dark:text-red-400">
                        {{ session('error') }}
                    </p>
                </div>
            @endif

            @if ($items->total() === 0)
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('No published posts available.') }}
                    </p>
                </div>
            @else
                <div class="relative">
                    <div
                        class="absolute bottom-0 left-[1.125rem] top-0 w-px bg-gray-200 dark:bg-gray-700"
                        aria-hidden="true"
                    ></div>

                    <div class="space-y-8">
                        @foreach ($items as $item)
                            <div class="relative pl-12">
                                <div
                                    class="absolute left-3 top-6 h-3 w-3 rounded-full border-2 border-white bg-indigo-500 shadow dark:border-gray-900"
                                    aria-hidden="true"
                                ></div>

                                <p class="mb-3 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                    {{ $item->occurredAt->timezone(config('app.timezone'))->format('M j, Y g:i A') }}
                                </p>

                                @include('feed.partials.timeline-item', ['item' => $item])
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
