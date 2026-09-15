@if ($attachments->isNotEmpty())
    <div
        class="mt-4 space-y-3"
        x-data="{ lightbox: null }"
        @keydown.escape.window="lightbox = null"
    >
        <div @class([
            'grid gap-3',
            'sm:grid-cols-2' => $attachments->count() > 1,
        ])>
            @foreach ($attachments as $attachment)
                @if ($attachment->isImage())
                    <button
                        type="button"
                        class="block w-full overflow-hidden border border-[var(--app-border)] text-start"
                        style="border-radius: calc(var(--app-radius) - 0.125rem)"
                        @click="lightbox = {{ \Illuminate\Support\Js::from($attachment->url()) }}"
                    >
                        <img
                            src="{{ $attachment->url() }}"
                            alt="{{ $attachment->original_name }}"
                            class="max-h-96 w-full object-cover motion-safe:transition motion-safe:duration-300 hover:scale-[1.01]"
                            loading="lazy"
                        />
                    </button>
                @else
                    <a
                        href="{{ $attachment->url() }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-3 border border-[var(--app-border)] bg-[var(--app-background)] p-4 text-sm motion-safe:transition hover:bg-[var(--app-surface-hover)]"
                        style="border-radius: calc(var(--app-radius) - 0.125rem)"
                    >
                        <svg class="h-8 w-8 shrink-0 text-[var(--app-primary)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span class="truncate text-[var(--app-text)] opacity-80">{{ $attachment->original_name }}</span>
                    </a>
                @endif
            @endforeach
        </div>

        <div
            x-show="lightbox"
            x-cloak
            class="fixed inset-0 z-[80] flex items-center justify-center bg-black/70 p-4"
            role="dialog"
            aria-modal="true"
            aria-label="{{ __('Attachment preview') }}"
            @click.self="lightbox = null"
        >
            <button
                type="button"
                class="absolute end-4 top-4 rounded border border-white/30 px-3 py-2 text-sm text-white"
                @click="lightbox = null"
            >
                {{ __('Close') }}
            </button>
            <img
                :src="lightbox"
                alt=""
                class="max-h-[90vh] max-w-full object-contain"
            />
        </div>
    </div>
@endif
