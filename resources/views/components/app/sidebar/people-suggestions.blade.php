@props([
    'people' => collect(),
])

<section class="app-sidebar-card">
    <h2 class="app-sidebar-title">{{ __('People posting') }}</h2>

    @forelse ($people as $person)
        <div @class(['flex items-center gap-3 border-t border-[var(--app-border)] pt-3' => ! $loop->first, 'pb-3' => ! $loop->last, 'flex items-center gap-3' => $loop->first])>
            <div class="app-avatar h-9 w-9 text-xs">
                {{ str($person->name)->substr(0, 1)->upper() }}
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-[var(--app-text)]">{{ $person->name }}</p>
                <p class="text-xs text-[var(--app-text-muted)]">
                    {{ $person->role?->label() ?? __('Member') }}
                </p>
            </div>
        </div>
    @empty
        <p class="app-empty">{{ __('No other authors to suggest yet.') }}</p>
    @endforelse
</section>
