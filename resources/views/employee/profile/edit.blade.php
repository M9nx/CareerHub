<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Career documents') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Upload your CV (PDF) and an optional application image (JPG or PNG).') }}
        </p>
    </header>

    @if (session('success'))
        <p
            class="mt-4 text-sm font-medium text-green-600 dark:text-green-400"
            data-test="career-documents-success"
        >
            {{ session('success') }}
        </p>
    @endif

    <form
        method="POST"
        action="{{ route('employee.profile.update') }}"
        enctype="multipart/form-data"
        class="app-form mt-6 space-y-6"
    >
        @csrf
        @method('PATCH')

        <div>
            <x-input-label for="cv" :value="__('CV')" />

            @if (filled($profile->cv_path))
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Current file:') }}
                    <a
                        href="{{ route('employee.profile.cv.download') }}"
                        class="font-medium text-indigo-600 underline-offset-4 hover:underline dark:text-indigo-400"
                    >
                        {{ basename($profile->cv_path) }}
                    </a>
                    <span class="text-gray-400 dark:text-gray-500">·</span>
                    <a
                        href="{{ route('employee.profile.cv.download') }}"
                        class="font-medium text-indigo-600 underline-offset-4 hover:underline dark:text-indigo-400"
                    >
                        {{ __('Download') }}
                    </a>
                </p>
            @else
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('No CV uploaded yet.') }}
                </p>
            @endif

            <input
                id="cv"
                name="cv"
                type="file"
                accept=".pdf,application/pdf"
                class="mt-2 block w-full text-sm text-gray-700 file:me-4 file:rounded-md file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-xs file:font-semibold file:uppercase file:tracking-widest file:text-white hover:file:bg-gray-700 dark:text-gray-300 dark:file:bg-gray-200 dark:file:text-gray-800 dark:hover:file:bg-white"
            />

            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                {{ __('PDF up to 5 MB.') }}
            </p>

            <x-input-error class="mt-2" :messages="$errors->get('cv')" />
        </div>

        <div>
            <x-input-label for="application_image" :value="__('Application Image')" />

            @if (filled($profile->application_image_path))
                <div class="mt-2">
                    <img
                        src="{{ Storage::disk('public')->url($profile->application_image_path) }}"
                        alt="{{ __('Application image preview') }}"
                        class="h-24 w-24 rounded-md object-cover ring-1 ring-gray-200 dark:ring-gray-700"
                    />
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Current file:') }}
                        {{ basename($profile->application_image_path) }}
                        <span class="text-gray-400 dark:text-gray-500">·</span>
                        <a
                            href="{{ route('employee.profile.application-image.download') }}"
                            class="font-medium text-indigo-600 underline-offset-4 hover:underline dark:text-indigo-400"
                        >
                            {{ __('Download') }}
                        </a>
                    </p>
                </div>
            @else
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('No application image uploaded yet.') }}
                </p>
            @endif

            <input
                id="application_image"
                name="application_image"
                type="file"
                accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                class="mt-2 block w-full text-sm text-gray-700 file:me-4 file:rounded-md file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-xs file:font-semibold file:uppercase file:tracking-widest file:text-white hover:file:bg-gray-700 dark:text-gray-300 dark:file:bg-gray-200 dark:file:text-gray-800 dark:hover:file:bg-white"
            />

            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                {{ __('JPG or PNG up to 2 MB.') }}
            </p>

            <x-input-error class="mt-2" :messages="$errors->get('application_image')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>
                {{ __('Save documents') }}
            </x-primary-button>
        </div>
    </form>
</section>
