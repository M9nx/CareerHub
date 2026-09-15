<x-app.page :title="__('Profile')">
    <div class="space-y-6">
        <div class="app-card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        @if ($employerProfile)
            <div class="app-card p-6 sm:p-8">
                <div class="max-w-xl">
                    @include('employer.profile.edit', ['profile' => $employerProfile])
                </div>
            </div>
        @endif

        @if ($employeeProfile)
            <div class="app-card p-6 sm:p-8">
                <div class="max-w-xl">
                    @include('employee.profile.edit', ['profile' => $employeeProfile])
                </div>
            </div>
        @endif

        <div class="app-card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="app-card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app.page>
