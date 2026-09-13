<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    <x-nav-link
        :href="route('employee.dashboard')"
        :active="request()->routeIs('employee.dashboard')"
    >
        {{ __('Dashboard') }}
    </x-nav-link>

    <x-nav-link
        :href="route('feed.index')"
        :active="request()->routeIs('feed.index')"
    >
        {{ __('Feed') }}
    </x-nav-link>

    @if (Route::has('employee.jobs.index'))
        <x-nav-link
            :href="route('employee.jobs.index')"
            :active="request()->routeIs('employee.jobs*')"
        >
            {{ __('Jobs') }}
        </x-nav-link>
    @endif

    @if (Route::has('employee.applications'))
        <x-nav-link
            :href="route('employee.applications')"
            :active="request()->routeIs('employee.applications*')"
        >
            {{ __('Applications') }}
        </x-nav-link>
    @endif

    @if (Route::has('employee.posts'))
        <x-nav-link
            :href="route('employee.posts')"
            :active="request()->routeIs('employee.posts*')"
        >
            {{ __('Posts') }}
        </x-nav-link>
    @endif

    <x-nav-link
        :href="route('profile.edit')"
        :active="request()->routeIs('profile.*')"
    >
        {{ __('Profile') }}
    </x-nav-link>
</div>
