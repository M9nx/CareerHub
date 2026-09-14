<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Edit Employee Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <p class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </p>
                    @endif

                    <form method="POST" action="{{ route('employee.profile.update') }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div>
                            <x-input-label :value="__('CV')" />

                            <div class="mt-1 rounded-md border border-gray-300 bg-gray-50 p-4 text-sm text-gray-600 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                {{ $profile->cv_path
                                    ? __('CV uploaded')
                                    : __('CV upload will be available in a later phase.') }}
                            </div>
                        </div>

                        <div>
                            <x-input-label :value="__('Application Image')" />

                            <div class="mt-1 rounded-md border border-gray-300 bg-gray-50 p-4 text-sm text-gray-600 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                {{ $profile->application_image_path
                                    ? __('Application image uploaded')
                                    : __('Application image upload will be available in a later phase.') }}
                            </div>
                        </div>

                        <div>
                            <x-primary-button>
                                {{ __('Update Profile') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
