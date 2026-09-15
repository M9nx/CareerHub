@if ($attachments->isNotEmpty())
    <div class="mt-4 grid gap-3 sm:grid-cols-2">
        @foreach ($attachments as $attachment)
            @if ($attachment->isImage())
                <a
                    href="{{ $attachment->url() }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="block overflow-hidden border border-[var(--landing-rule)]"
                    style="border-radius: calc(var(--landing-radius) - 0.125rem)"
                >
                    <img
                        src="{{ $attachment->url() }}"
                        alt="{{ $attachment->original_name }}"
                        class="max-h-72 w-full object-cover motion-safe:transition motion-safe:duration-300 hover:scale-[1.02]"
                    />
                </a>
            @else
                <a
                    href="{{ $attachment->url() }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center gap-3 border border-[var(--landing-rule)] bg-[var(--landing-canvas)] p-4 text-sm motion-safe:transition hover:bg-black/[0.03] dark:hover:bg-white/[0.04]"
                    style="border-radius: calc(var(--landing-radius) - 0.125rem)"
                >
                    <svg class="h-8 w-8 shrink-0 text-[var(--landing-accent)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <span class="truncate opacity-80">{{ $attachment->original_name }}</span>
                </a>
            @endif
        @endforeach
    </div>
@endif
