<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Company') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Company details shown on your public profile and job postings.') }}
        </p>
    </header>

    @if (session('success') && ! str_contains((string) session('success'), 'Professional'))
        <p class="mt-4 text-sm font-medium text-green-600 dark:text-green-400">
            {{ session('success') }}
        </p>
    @endif

    <form
        method="POST"
        action="{{ route('employer.profile.update') }}"
        enctype="multipart/form-data"
        class="app-form mt-6 space-y-6"
    >
        @csrf
        @method('PATCH')

        <div class="flex items-center gap-4">
            @if ($profile->logoUrl())
                <img
                    src="{{ $profile->logoUrl() }}"
                    alt=""
                    class="h-16 w-16 rounded-md object-cover ring-1 ring-gray-200 dark:ring-gray-700"
                />
            @endif

            <div class="min-w-0 flex-1">
                <x-input-label for="logo" :value="__('Company logo')" />
                <input
                    id="logo"
                    name="logo"
                    type="file"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    class="mt-2 block w-full text-sm text-gray-700 dark:text-gray-300"
                />
                <x-input-error class="mt-2" :messages="$errors->get('logo')" />
            </div>
        </div>

        <div>
            <x-input-label for="company_name" :value="__('Company Name')" />

            <x-text-input
                id="company_name"
                name="company_name"
                type="text"
                class="mt-1 block w-full"
                :value="old('company_name', $profile->company_name)"
                required
                maxlength="255"
            />

            <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
        </div>

        <div>
            <x-input-label for="industry" :value="__('Industry')" />
            <x-text-input
                id="industry"
                name="industry"
                type="text"
                class="mt-1 block w-full"
                :value="old('industry', $profile->industry)"
                maxlength="255"
            />
            <x-input-error class="mt-2" :messages="$errors->get('industry')" />
        </div>

        <div>
            <x-input-label for="company_size" :value="__('Company size')" />
            <x-text-input
                id="company_size"
                name="company_size"
                type="text"
                class="mt-1 block w-full"
                :value="old('company_size', $profile->company_size)"
                maxlength="100"
                placeholder="{{ __('e.g. 11-50') }}"
            />
            <x-input-error class="mt-2" :messages="$errors->get('company_size')" />
        </div>

        <div>
            <x-input-label for="company_location" :value="__('Company location')" />
            <x-text-input
                id="company_location"
                name="location"
                type="text"
                class="mt-1 block w-full"
                :value="old('location', $profile->location)"
                maxlength="255"
            />
            <x-input-error class="mt-2" :messages="$errors->get('location')" />
        </div>

        <div>
            <x-input-label for="website" :value="__('Website')" />
            <x-text-input
                id="website"
                name="website"
                type="url"
                class="mt-1 block w-full"
                :value="old('website', $profile->website)"
                maxlength="255"
                placeholder="https://"
            />
            <x-input-error class="mt-2" :messages="$errors->get('website')" />
        </div>

        <div>
            <x-input-label for="company_about" :value="__('About the company')" />
            <textarea
                id="company_about"
                name="about"
                rows="4"
                maxlength="5000"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
            >{{ old('about', $profile->about) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('about')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>
                {{ __('Save') }}
            </x-primary-button>
        </div>
    </form>
</section>
