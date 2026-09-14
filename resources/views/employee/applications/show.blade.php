```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Application Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="mb-4 text-lg font-semibold">
                        {{ $application->jobPosting->title }}
                    </h3>

                    <p class="mb-6">
                        <span class="font-medium">{{ __('Current Status:') }}</span>
                        {{ str($application->status->name)->headline() }}
                    </p>

                    @include(
                        'employee.applications.partials.timeline',
                        ['application' => $application]
                    )
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```
