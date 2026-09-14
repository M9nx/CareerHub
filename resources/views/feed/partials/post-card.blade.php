<article class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                {{ $post->title }}
            </h3>

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ __('By :name', ['name' => $post->author?->name ?? __('Unknown author')]) }}
            </p>
        </div>

        <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-200">
            {{ $post->author_role?->label() ?? __('Author') }}
        </span>
    </div>

    <p class="mt-4 whitespace-pre-line text-gray-700 dark:text-gray-300">
        {{ \Illuminate\Support\Str::limit($post->body, 180) }}
    </p>
</article>
