<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Feed') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if ($posts->isEmpty())
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('No published posts available.') }}
                    </p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach ($posts as $post)
                        <article class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $post->title }}
                            </h3>

                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('By :name', ['name' => $post->author?->name ?? __('Unknown author')]) }}
                            </p>

                            <div class="mt-4 text-gray-700 dark:text-gray-300">
                                {{ $post->body }}
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
