<article class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">
                {{ $post->title }}
            </h3>

            <p class="mt-2 text-sm text-gray-500">
                By {{ $post->author?->name ?? 'Unknown author' }}
            </p>
        </div>

        <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
            {{ $post->author?->role?->label() ?? 'Author' }}
        </span>
    </div>

    <p class="mt-4 text-gray-700">
        {{ \Illuminate\Support\Str::limit($post->body, 180) }}
    </p>
</article>