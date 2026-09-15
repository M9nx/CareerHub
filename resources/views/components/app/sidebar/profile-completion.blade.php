@props([
    'checks' => [],
])

@php
    $checks = collect($checks);
    $done = $checks->where('complete', true)->count();
    $total = $checks->count();
@endphp

<section class="app-sidebar-card">
    <h2 class="app-sidebar-title">{{ __('Profile strength') }}</h2>

    @if ($total === 0)
        <p class="app-empty">{{ __('No profile checklist available.') }}</p>
    @else
        <p class="mb-3 text-sm text-[var(--app-text-muted)]">
            {{ __(':done of :total complete', ['done' => $done, 'total' => $total]) }}
        </p>

        <ul class="space-y-2 text-sm">
            @foreach ($checks as $check)
                <li class="flex items-start gap-2">
                    <span
                        class="mt-0.5 inline-flex h-4 w-4 shrink-0 items-center justify-center rounded-full text-[10px] {{ $check['complete'] ? 'bg-[var(--app-success)] text-white' : 'border border-[var(--app-border)] text-[var(--app-text-muted)]' }}"
                        aria-hidden="true"
                    >
                        @if ($check['complete'])
                            ✓
                        @endif
                    </span>
                    <span @class(['opacity-50 line-through' => $check['complete']])>
                        {{ $check['label'] }}
                    </span>
                </li>
            @endforeach
        </ul>

        <a href="{{ route('profile.edit') }}" class="app-link mt-4 inline-flex min-h-[44px] items-center text-sm">
            {{ __('Improve profile') }}
        </a>
    @endif
</section>
