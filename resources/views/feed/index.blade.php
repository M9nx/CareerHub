<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Feed') }}
            </h2>

            @can('create', \App\Models\Post::class)
                @if (auth()->user()->role === \App\Enums\UserRole::Employer)
                    <a
                        href="{{ route('employer.posts.create') }}"
                        class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700"
                    >
                        {{ __('Create Post') }}
                    </a>
                @elseif (auth()->user()->role === \App\Enums\UserRole::Employee)
                    <a
                        href="{{ route('employee.posts.create') }}"
                        class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700"
                    >
                        {{ __('Create Post') }}
                    </a>
                @endif
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if ($posts->total() === 0)
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('No published posts available.') }}
                    </p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach ($posts as $post)
                        @include('feed.partials.post-card', ['post' => $post])
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>