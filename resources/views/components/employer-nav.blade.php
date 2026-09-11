<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    <x-nav-link
        :href="route('employer.dashboard')"
        :active="request()->routeIs('employer.dashboard')"
    >
        {{ __('Dashboard') }}
    </x-nav-link>

    <x-nav-link
        :href="route('employer.jobs')"
        :active="request()->routeIs('employer.jobs*')"
    >
        {{ __('Jobs') }}
    </x-nav-link>

    <x-nav-link
        :href="route('employer.applications')"
        :active="request()->routeIs('employer.applications*')"
    >
        {{ __('Applications') }}
    </x-nav-link>

    <x-nav-link
        :href="route('employer.posts')"
        :active="request()->routeIs('employer.posts*')"
    >
        {{ __('Posts') }}
    </x-nav-link>

    <x-nav-link
        :href="route('profile.edit')"
        :active="request()->routeIs('profile.*')"
    >
        {{ __('Profile') }}
    </x-nav-link>
</div>