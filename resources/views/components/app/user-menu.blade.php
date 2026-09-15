<div x-data="{ open: false }" class="relative">
    <button
        type="button"
        @click="open = !open"
        @click.outside="open = false"
        class="inline-flex min-h-[44px] items-center gap-2 text-sm opacity-70 motion-safe:transition hover:opacity-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--landing-accent)]"
        aria-haspopup="true"
        :aria-expanded="open"
    >
        <span class="app-avatar text-xs">{{ str(auth()->user()->name)->substr(0, 1)->upper() }}</span>
        <span>{{ auth()->user()->name }}</span>
        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <div
        x-show="open"
        x-transition
        x-cloak
        class="absolute end-0 z-50 mt-2 w-48 border border-[var(--landing-rule)] bg-[var(--landing-card)] py-2 shadow-lg"
        style="border-radius: var(--landing-radius)"
    >
        <a
            href="{{ route('profile.edit') }}"
            class="block px-4 py-2 text-sm opacity-70 motion-safe:transition hover:bg-black/[0.03] hover:opacity-100 dark:hover:bg-white/[0.04]"
        >
            {{ __('Profile') }}
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="block w-full px-4 py-2 text-start text-sm opacity-70 motion-safe:transition hover:bg-black/[0.03] hover:opacity-100 dark:hover:bg-white/[0.04]"
            >
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</div>
