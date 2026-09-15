@php
    use App\Enums\UserRole;

    $user = auth()->user();

    $homeRoute = route('feed.index');

    $navItems = array_values(array_filter([
        [
            'label' => __('Home'),
            'href' => route('feed.index'),
            'active' => request()->routeIs('feed.*'),
        ],
        $user->role === UserRole::Employer && Route::has('employer.jobs.index')
            ? [
                'label' => __('Jobs'),
                'href' => route('employer.jobs.index'),
                'active' => request()->routeIs('employer.jobs*'),
            ]
            : null,
        $user->role === UserRole::Employee && Route::has('employee.jobs.index')
            ? [
                'label' => __('Jobs'),
                'href' => route('employee.jobs.index'),
                'active' => request()->routeIs('employee.jobs*'),
            ]
            : null,
        $user->role === UserRole::Employer && Route::has('employer.applications.index')
            ? [
                'label' => __('Applications'),
                'href' => route('employer.applications.index'),
                'active' => request()->routeIs('employer.applications*'),
            ]
            : null,
        $user->role === UserRole::Employee && Route::has('employee.applications.index')
            ? [
                'label' => __('Applications'),
                'href' => route('employee.applications.index'),
                'active' => request()->routeIs('employee.applications*'),
            ]
            : null,
    ]));
@endphp

<header class="sticky top-0 z-50 border-b border-[var(--app-border)] bg-[var(--landing-canvas-translucent)] backdrop-blur-md">
    <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-2.5 md:px-6">
        <a
            href="{{ $homeRoute }}"
            class="inline-flex min-h-[44px] shrink-0 items-center text-sm font-medium uppercase tracking-[0.18em] text-[var(--app-text)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--app-primary)]"
        >
            {{ config('app.name', 'CareerHub') }}
        </a>

        <div
            class="app-header-search"
            role="search"
            aria-label="{{ __('Search') }}"
        >
            <svg class="h-4 w-4 shrink-0 opacity-60" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14Z" />
            </svg>
            <span>{{ __('Search professionals, jobs, companies…') }}</span>
        </div>

        <nav class="ms-auto hidden items-center gap-1 sm:flex" aria-label="{{ __('Main navigation') }}">
            @foreach ($navItems as $item)
                <a
                    href="{{ $item['href'] }}"
                    @class(['app-nav-link px-3', 'is-active' => $item['active']])
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="hidden sm:block">
            <x-app.user-menu />
        </div>

        <button
            type="button"
            class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center border border-[var(--app-border)] sm:hidden"
            style="border-radius: var(--app-radius)"
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
        class="border-t border-[var(--app-border)] px-4 py-3 sm:hidden"
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

            @if ($user->role === UserRole::Employer && Route::has('employer.jobs.create'))
                <a href="{{ route('employer.jobs.create') }}" class="min-h-[44px] py-2 opacity-70">
                    {{ __('Post a job') }}
                </a>
            @endif

            @if ($user->role === UserRole::Employer && Route::has('employer.dashboard'))
                <a href="{{ route('employer.dashboard') }}" class="min-h-[44px] py-2 opacity-70">
                    {{ __('Dashboard') }}
                </a>
            @elseif ($user->role === UserRole::Employee && Route::has('employee.dashboard'))
                <a href="{{ route('employee.dashboard') }}" class="min-h-[44px] py-2 opacity-70">
                    {{ __('Dashboard') }}
                </a>
            @endif

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
