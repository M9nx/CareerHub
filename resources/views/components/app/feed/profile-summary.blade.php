@php
    use App\Enums\UserRole;

    $user = auth()->user();
    $role = $user->role;
    $companyName = $user->employerProfile?->company_name;
    $profileRoute = route('profile.edit');
    $publicProfileRoute = route('people.show', $user);
    $postsRoute = match ($role) {
        UserRole::Employer => Route::has('employer.posts.index') ? route('employer.posts.index') : null,
        UserRole::Employee => Route::has('employee.posts.index') ? route('employee.posts.index') : null,
        default => null,
    };
    $jobsRoute = match ($role) {
        UserRole::Employer => Route::has('employer.jobs.index') ? route('employer.jobs.index') : null,
        UserRole::Employee => Route::has('employee.jobs.index') ? route('employee.jobs.index') : null,
        default => null,
    };
@endphp

<section class="app-sidebar-card">
    <div class="flex flex-col items-center text-center">
        @if ($user->avatarUrl())
            <img
                src="{{ $user->avatarUrl() }}"
                alt=""
                class="h-16 w-16 rounded-full object-cover ring-1 ring-[var(--app-border)]"
            />
        @else
            <div class="app-avatar h-16 w-16 text-lg">
                {{ str($user->name)->substr(0, 1)->upper() }}
            </div>
        @endif

        <h2 class="mt-3 text-base font-medium text-[var(--app-text)]">
            {{ $user->name }}
        </h2>

        @if (filled($user->headline))
            <p class="mt-1 text-sm text-[var(--app-text)] opacity-80">
                {{ $user->headline }}
            </p>
        @else
            <p class="mt-1 text-sm text-[var(--app-text-muted)]">
                {{ $role?->label() }}
            </p>
        @endif

        @if (filled($user->location))
            <p class="mt-1 text-xs text-[var(--app-text-muted)]">
                {{ $user->location }}
            </p>
        @endif

        @if ($companyName)
            <p class="mt-1 text-sm font-medium text-[var(--app-text)] opacity-80">
                {{ $companyName }}
            </p>
        @endif
    </div>

    <div class="swiss-rule my-4"></div>

    <nav class="space-y-1 text-sm" aria-label="{{ __('Shortcuts') }}">
        <a href="{{ $publicProfileRoute }}" class="app-link-muted block min-h-[44px] py-2">
            {{ __('View public profile') }}
        </a>

        <a href="{{ $profileRoute }}" class="app-link-muted block min-h-[44px] py-2">
            {{ __('Profile settings') }}
        </a>

        @if ($postsRoute)
            <a href="{{ $postsRoute }}" class="app-link-muted block min-h-[44px] py-2">
                {{ __('My posts') }}
            </a>
        @endif

        @if ($jobsRoute)
            <a href="{{ $jobsRoute }}" class="app-link-muted block min-h-[44px] py-2">
                {{ $role === UserRole::Employer ? __('My jobs') : __('Browse jobs') }}
            </a>
        @endif
    </nav>
</section>
