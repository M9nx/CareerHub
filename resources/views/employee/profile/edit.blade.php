<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Career documents') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('CV and application image uploads will be available in a later phase.') }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
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
    </div>
</section>
