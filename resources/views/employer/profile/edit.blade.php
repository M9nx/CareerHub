<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Company') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Update the company name shown on your job postings.') }}
        </p>
    </header>

    @if (session('success'))
        <p class="mt-4 text-sm font-medium text-green-600 dark:text-green-400">
            {{ session('success') }}
        </p>
    @endif

    <form method="POST" action="{{ route('employer.profile.update') }}" class="app-form mt-6 space-y-6">
        @csrf
        @method('PATCH')

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

        <div class="flex items-center gap-4">
            <x-primary-button>
                {{ __('Save') }}
            </x-primary-button>
        </div>
    </form>
</section>
