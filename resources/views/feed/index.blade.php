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
                        <article class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $post->title }}
                            </h3>

                            <p class="mt-2 text-sm text-gray-500">
                                By {{ $post->author?->name ?? 'Unknown author' }}
                            </p>

                            <div class="mt-4 text-gray-700">
                                {{ $post->body }}
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>