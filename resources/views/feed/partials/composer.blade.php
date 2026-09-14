@can('create', \App\Models\Post::class)
    <div class="mb-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form
            action="{{ route('feed.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="p-6"
        >
            @csrf

            <div class="flex gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200">
                    {{ str(auth()->user()->name)->substr(0, 1)->upper() }}
                </div>

                <div class="flex-1 space-y-4">
                    <textarea
                        id="body"
                        name="body"
                        rows="3"
                        required
                        placeholder="{{ __('Share an update with the community...') }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
                    >{{ old('body') }}</textarea>

                    @error('body')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    <div>
                        <label
                            for="attachments"
                            class="inline-flex cursor-pointer items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                            </svg>
                            {{ __('Add photo or PDF') }}
                        </label>
                        <input
                            id="attachments"
                            name="attachments[]"
                            type="file"
                            multiple
                            accept=".jpg,.jpeg,.png,.pdf"
                            class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200 dark:text-gray-400 dark:file:bg-gray-700 dark:file:text-gray-200"
                        />
                        @error('attachments.*')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                        >
                            {{ __('Post') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endcan
