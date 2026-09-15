@props([
    'people' => collect(),
])

<section class="app-sidebar-card">
    <h2 class="app-sidebar-title">{{ __('People posting') }}</h2>

    @forelse ($people as $person)
        <a
            href="{{ route('people.show', $person) }}"
            @class(['flex items-center gap-3 border-t border-[var(--app-border)] pt-3' => ! $loop->first, 'pb-3' => ! $loop->last, 'flex items-center gap-3' => $loop->first])
        >
            @if ($person->avatarUrl())
                <img
                    src="{{ $person->avatarUrl() }}"
                    alt=""
                    class="h-9 w-9 rounded-full object-cover ring-1 ring-[var(--app-border)]"
                />
            @else
                <div class="app-avatar h-9 w-9 text-xs">
                    {{ str($person->name)->substr(0, 1)->upper() }}
                </div>
            @endif
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-[var(--app-text)]">{{ $person->name }}</p>
                <p class="truncate text-xs text-[var(--app-text-muted)]">
                    {{ $person->headline ?: ($person->role?->label() ?? __('Member')) }}
                </p>
            </div>
        </a>
    @empty
        <p class="app-empty">{{ __('No other authors to suggest yet.') }}</p>
    @endforelse
</section>
