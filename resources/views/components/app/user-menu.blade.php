@php
    use App\Enums\UserRole;

    $user = auth()->user();
@endphp

<div x-data="{ open: false }" class="relative">
    <button
        type="button"
        @click="open = !open"
        @click.outside="open = false"
        class="inline-flex min-h-[44px] items-center gap-2 text-sm text-[var(--app-text)] opacity-70 motion-safe:transition hover:opacity-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--app-primary)]"
        aria-haspopup="true"
        :aria-expanded="open.toString()"
    >
        <span class="app-avatar h-8 w-8 text-xs">{{ str($user->name)->substr(0, 1)->upper() }}</span>
        <span class="hidden md:inline">{{ __('Me') }}</span>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <div
        x-show="open"
        x-transition
        x-cloak
        class="absolute end-0 z-50 mt-2 w-56 border border-[var(--app-border)] bg-[var(--app-surface)] py-2 shadow-lg"
        style="border-radius: var(--app-radius)"
        role="menu"
    >
        <div class="border-b border-[var(--app-border)] px-4 py-3">
            <p class="text-sm font-medium text-[var(--app-text)]">{{ $user->name }}</p>
            <p class="text-xs text-[var(--app-text-muted)]">{{ $user->role?->label() }}</p>
        </div>

        <a
            href="{{ route('profile.edit') }}"
            class="block px-4 py-2 text-sm opacity-70 motion-safe:transition hover:bg-[var(--app-surface-hover)] hover:opacity-100"
            role="menuitem"
        >
            {{ __('Profile') }}
        </a>

        <a
            href="{{ route('saved.index') }}"
            class="block px-4 py-2 text-sm opacity-70 motion-safe:transition hover:bg-[var(--app-surface-hover)] hover:opacity-100"
            role="menuitem"
        >
            {{ __('Saved') }}
        </a>

        <a
            href="{{ route('notifications.index') }}"
            class="block px-4 py-2 text-sm opacity-70 motion-safe:transition hover:bg-[var(--app-surface-hover)] hover:opacity-100"
            role="menuitem"
        >
            {{ __('Notifications') }}
        </a>

        @if ($user->role === UserRole::Employer)
            @if (Route::has('employer.dashboard'))
                <a href="{{ route('employer.dashboard') }}" class="block px-4 py-2 text-sm opacity-70 hover:bg-[var(--app-surface-hover)] hover:opacity-100" role="menuitem">
                    {{ __('Dashboard') }}
                </a>
            @endif
            @if (Route::has('employer.jobs.create'))
                <a href="{{ route('employer.jobs.create') }}" class="block px-4 py-2 text-sm opacity-70 hover:bg-[var(--app-surface-hover)] hover:opacity-100" role="menuitem">
                    {{ __('Post a job') }}
                </a>
            @endif
            @if (Route::has('employer.posts.index'))
                <a href="{{ route('employer.posts.index') }}" class="block px-4 py-2 text-sm opacity-70 hover:bg-[var(--app-surface-hover)] hover:opacity-100" role="menuitem">
                    {{ __('My posts') }}
                </a>
            @endif
        @elseif ($user->role === UserRole::Employee)
            @if (Route::has('employee.dashboard'))
                <a href="{{ route('employee.dashboard') }}" class="block px-4 py-2 text-sm opacity-70 hover:bg-[var(--app-surface-hover)] hover:opacity-100" role="menuitem">
                    {{ __('Dashboard') }}
                </a>
            @endif
            @if (Route::has('employee.posts.index'))
                <a href="{{ route('employee.posts.index') }}" class="block px-4 py-2 text-sm opacity-70 hover:bg-[var(--app-surface-hover)] hover:opacity-100" role="menuitem">
                    {{ __('My posts') }}
                </a>
            @endif
        @endif

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="block w-full px-4 py-2 text-start text-sm opacity-70 motion-safe:transition hover:bg-[var(--app-surface-hover)] hover:opacity-100"
                role="menuitem"
            >
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</div>
