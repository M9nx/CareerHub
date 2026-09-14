<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Feed') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($posts->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-600">
                        No published posts available.
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