@can('create', \App\Models\Post::class)
    <div
        class="app-card overflow-hidden"
        x-data="{ open: {{ $errors->has('body') || $errors->has('attachments.*') ? 'true' : 'false' }} }"
    >
        <div class="p-4" x-show="!open">
            <button
                type="button"
                class="flex w-full items-center gap-3 text-start"
                @click="open = true; $nextTick(() => $refs.body?.focus())"
            >
                <div class="app-avatar">
                    {{ str(auth()->user()->name)->substr(0, 1)->upper() }}
                </div>
                <span class="flex-1 rounded-full border border-[var(--app-border)] bg-[var(--app-background)] px-4 py-3 text-sm text-[var(--app-text-muted)]">
                    {{ __('Start a post') }}
                </span>
            </button>
        </div>

        <form
            action="{{ route('feed.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="p-5"
            x-show="open"
            x-cloak
        >
            @csrf

            <div class="flex gap-4">
                <div class="app-avatar">
                    {{ str(auth()->user()->name)->substr(0, 1)->upper() }}
                </div>

                <div class="min-w-0 flex-1 space-y-4">
                    <textarea
                        id="body"
                        name="body"
                        rows="4"
                        required
                        placeholder="{{ __('Share an update with the community...') }}"
                        class="app-textarea"
                        x-ref="body"
                        @click.stop
                    >{{ old('body') }}</textarea>

                    @error('body')
                        <p class="text-sm text-[var(--app-danger)]">{{ $message }}</p>
                    @enderror

                    <div class="swiss-rule"></div>

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <label for="attachments" class="app-file-trigger">
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
                                class="app-file-input"
                            />
                            @error('attachments.*')
                                <p class="mt-1 text-sm text-[var(--app-danger)]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                class="swiss-btn-secondary"
                                @click="open = false"
                            >
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="swiss-btn-primary">
                                {{ __('Post') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endcan
