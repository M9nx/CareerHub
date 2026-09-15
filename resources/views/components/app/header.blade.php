@php
    use App\Enums\UserRole;

    $dashboardRoute = match (auth()->user()->role) {
        UserRole::Employer => route('employer.dashboard'),
        UserRole::Employee => route('employee.dashboard'),
        default => route('dashboard'),
    };

    $navItems = match (auth()->user()->role) {
        UserRole::Employer => array_filter([
            ['label' => __('Dashboard'), 'href' => route('employer.dashboard'), 'active' => request()->routeIs('employer.dashboard')],
            ['label' => __('Feed'), 'href' => route('feed.index'), 'active' => request()->routeIs('feed.index')],
            Route::has('employer.jobs.index') ? ['label' => __('Jobs'), 'href' => route('employer.jobs.index'), 'active' => request()->routeIs('employer.jobs*')] : null,
            Route::has('employer.applications.index') ? ['label' => __('Applications'), 'href' => route('employer.applications.index'), 'active' => request()->routeIs('employer.applications*')] : null,
            Route::has('employer.posts.index') ? ['label' => __('Posts'), 'href' => route('employer.posts.index'), 'active' => request()->routeIs('employer.posts*')] : null,
        ]),
        UserRole::Employee => array_filter([
            ['label' => __('Dashboard'), 'href' => route('employee.dashboard'), 'active' => request()->routeIs('employee.dashboard')],
            ['label' => __('Feed'), 'href' => route('feed.index'), 'active' => request()->routeIs('feed.index')],
            Route::has('employee.jobs.index') ? ['label' => __('Jobs'), 'href' => route('employee.jobs.index'), 'active' => request()->routeIs('employee.jobs*')] : null,
            Route::has('employee.applications.index') ? ['label' => __('Applications'), 'href' => route('employee.applications.index'), 'active' => request()->routeIs('employee.applications*')] : null,
            Route::has('employee.posts.index') ? ['label' => __('Posts'), 'href' => route('employee.posts.index'), 'active' => request()->routeIs('employee.posts*')] : null,
        ]),
        default => [],
    };
@endphp

<header class="sticky top-0 z-50 border-b border-[var(--landing-rule)] bg-[var(--landing-canvas-translucent)] backdrop-blur-md">
    <div class="swiss-container flex items-center justify-between gap-4 py-4">
        <a
            href="{{ $dashboardRoute }}"
            class="inline-flex min-h-[44px] items-center text-sm font-medium uppercase tracking-[0.18em] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--landing-ink)]"
        >
            {{ config('app.name', 'CareerHub') }}
        </a>

        <nav class="hidden items-center gap-6 md:flex" aria-label="{{ __('Main navigation') }}">
            @foreach ($navItems as $item)
                <a
                    href="{{ $item['href'] }}"
                    @class(['app-nav-link', 'is-active' => $item['active']])
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-3 md:flex">
            <x-app.user-menu />
        </div>

        <button
            type="button"
            class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center border border-[var(--landing-rule)] md:hidden"
            style="border-radius: var(--landing-radius)"
            x-data
            @click="$dispatch('toggle-app-nav')"
            aria-label="{{ __('Open menu') }}"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7h16M4 12h16M4 17h16" />
            </svg>
        </button>
    </div>

    <div
        class="border-t border-[var(--landing-rule)] px-4 py-4 md:hidden"
        x-data="{ open: false }"
        @toggle-app-nav.window="open = !open"
        x-show="open"
        x-transition
        x-cloak
    >
        <nav class="flex flex-col gap-1 text-sm" aria-label="{{ __('Mobile navigation') }}">
            @foreach ($navItems as $item)
                <a
                    href="{{ $item['href'] }}"
                    @class(['min-h-[44px] py-2', 'font-medium opacity-100' => $item['active'], 'opacity-70' => ! $item['active']])
                >
                    {{ $item['label'] }}
                </a>
            @endforeach

            <div class="swiss-rule my-3"></div>

            <a href="{{ route('profile.edit') }}" class="min-h-[44px] py-2 opacity-70">
                {{ __('Profile') }}
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="min-h-[44px] py-2 opacity-70">
                    {{ __('Log Out') }}
                </button>
            </form>
        </nav>
    </div>
</header>
