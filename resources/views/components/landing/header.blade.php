@php
    $navigation = config('landing.navigation');
@endphp

<header data-landing-header class="sticky top-0 z-50 border-b border-transparent motion-safe:transition motion-safe:duration-300">
    <div class="swiss-container flex items-center justify-between gap-4 py-4 md:py-5">
        <a
            href="{{ url('/') }}"
            class="inline-flex min-h-[44px] items-center text-sm font-medium uppercase tracking-[0.18em] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--landing-ink)]"
        >
            {{ config('app.name', 'CareerHub') }}
        </a>

        <nav class="hidden items-center gap-8 md:flex">
            @foreach ($navigation as $item)
                <a
                    href="{{ $item['href'] }}"
                    class="inline-flex min-h-[44px] items-center text-sm text-[var(--landing-ink)] opacity-70 motion-safe:transition hover:opacity-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--landing-accent)]"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-3 md:flex">
            <a
                href="{{ route('login') }}"
                class="min-h-[44px] px-3 py-2 text-sm opacity-70 motion-safe:transition hover:opacity-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--landing-ink)]"
            >
                {{ __('Log in') }}
            </a>
            <a href="{{ route('register') }}" class="swiss-btn-primary">
                {{ __('Register') }}
            </a>
        </div>

        <button
            type="button"
            class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center border border-[var(--landing-rule)] md:hidden"
            style="border-radius: var(--landing-radius)"
            x-data
            @click="$dispatch('toggle-mobile-nav')"
            aria-label="{{ __('Open menu') }}"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7h16M4 12h16M4 17h16" />
            </svg>
        </button>
    </div>

    <div
        class="border-t border-[var(--landing-rule)] bg-[var(--landing-canvas)] px-4 py-4 md:hidden"
        x-data="{ open: false }"
        @toggle-mobile-nav.window="open = !open"
        x-show="open"
        x-transition
        x-cloak
    >
        <nav class="flex flex-col gap-3 text-sm">
            @foreach ($navigation as $item)
                <a href="{{ $item['href'] }}" class="min-h-[44px] py-2">{{ $item['label'] }}</a>
            @endforeach
            <div class="swiss-rule my-2"></div>
            <a href="{{ route('login') }}" class="min-h-[44px] py-2">{{ __('Log in') }}</a>
            <a href="{{ route('register') }}" class="swiss-btn-primary w-full">{{ __('Register') }}</a>
        </nav>
    </div>
</header>
