<section>
    <header>
        <h2 class="text-lg font-medium text-[var(--app-text)]">
            {{ __('Professional identity') }}
        </h2>

        <p class="mt-1 text-sm text-[var(--app-text-muted)]">
            {{ __('Headline, location, and about text appear on your public CareerHub profile.') }}
        </p>
    </header>

    @if (session('success') && str_contains(session('success'), 'Professional'))
        <p class="mt-4 text-sm font-medium text-[var(--app-success)]">
            {{ session('success') }}
        </p>
    @endif

    <form
        method="POST"
        action="{{ route('profile.professional.update') }}"
        enctype="multipart/form-data"
        class="app-form mt-6 space-y-6"
    >
        @csrf
        @method('PATCH')

        <div class="flex items-center gap-4">
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

            <div class="min-w-0 flex-1">
                <x-input-label for="avatar" :value="__('Profile photo')" />
                <input
                    id="avatar"
                    name="avatar"
                    type="file"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    class="mt-2 block w-full text-sm"
                />
                <p class="mt-2 text-xs text-[var(--app-text-muted)]">
                    {{ __('JPG, PNG, or WebP up to 2 MB.') }}
                </p>
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            </div>
        </div>

        <div>
            <x-input-label for="headline" :value="__('Headline')" />
            <x-text-input
                id="headline"
                name="headline"
                type="text"
                class="mt-1 block w-full"
                :value="old('headline', $user->headline)"
                maxlength="255"
                placeholder="{{ __('e.g. Product designer · Open to work') }}"
            />
            <x-input-error class="mt-2" :messages="$errors->get('headline')" />
        </div>

        <div>
            <x-input-label for="location" :value="__('Location')" />
            <x-text-input
                id="location"
                name="location"
                type="text"
                class="mt-1 block w-full"
                :value="old('location', $user->location)"
                maxlength="255"
                placeholder="{{ __('City, country') }}"
            />
            <x-input-error class="mt-2" :messages="$errors->get('location')" />
        </div>

        <div>
            <x-input-label for="about" :value="__('About')" />
            <textarea
                id="about"
                name="about"
                rows="5"
                maxlength="5000"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
            >{{ old('about', $user->about) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('about')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>
                {{ __('Save professional profile') }}
            </x-primary-button>
        </div>
    </form>
</section>
