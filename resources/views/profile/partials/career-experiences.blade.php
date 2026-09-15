<section>
    <header>
        <h2 class="text-lg font-medium text-[var(--app-text)]">
            {{ __('Experience') }}
        </h2>
        <p class="mt-1 text-sm text-[var(--app-text-muted)]">
            {{ __('Roles and companies that appear on your public profile.') }}
        </p>
    </header>

    <ul class="mt-6 space-y-4">
        @forelse ($experiences as $experience)
            <li class="border-t border-[var(--app-border)] pt-4 first:border-0 first:pt-0">
                <form
                    method="POST"
                    action="{{ route('profile.experiences.update', $experience) }}"
                    class="app-form space-y-4"
                >
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label :for="'experience_title_'.$experience->id" :value="__('Title')" />
                            <x-text-input
                                :id="'experience_title_'.$experience->id"
                                name="title"
                                type="text"
                                class="mt-1 block w-full"
                                :value="old('title', $experience->title)"
                                required
                            />
                        </div>
                        <div>
                            <x-input-label :for="'experience_company_'.$experience->id" :value="__('Company')" />
                            <x-text-input
                                :id="'experience_company_'.$experience->id"
                                name="company"
                                type="text"
                                class="mt-1 block w-full"
                                :value="old('company', $experience->company)"
                                required
                            />
                        </div>
                    </div>

                    <div>
                        <x-input-label :for="'experience_location_'.$experience->id" :value="__('Location')" />
                        <x-text-input
                            :id="'experience_location_'.$experience->id"
                            name="location"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('location', $experience->location)"
                        />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label :for="'experience_started_'.$experience->id" :value="__('Start date')" />
                            <x-text-input
                                :id="'experience_started_'.$experience->id"
                                name="started_at"
                                type="date"
                                class="mt-1 block w-full"
                                :value="old('started_at', $experience->started_at?->format('Y-m-d'))"
                                required
                            />
                        </div>
                        <div>
                            <x-input-label :for="'experience_ended_'.$experience->id" :value="__('End date')" />
                            <x-text-input
                                :id="'experience_ended_'.$experience->id"
                                name="ended_at"
                                type="date"
                                class="mt-1 block w-full"
                                :value="old('ended_at', $experience->ended_at?->format('Y-m-d'))"
                            />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="experience_description_{{ $experience->id }}" :value="__('Description')" />
                        <textarea
                            id="experience_description_{{ $experience->id }}"
                            name="description"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >{{ old('description', $experience->description) }}</textarea>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                    </div>
                </form>

                <form
                    method="POST"
                    action="{{ route('profile.experiences.destroy', $experience) }}"
                    class="mt-3"
                    onsubmit="return confirm(@js(__('Remove this experience?')))"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-[var(--app-danger)] underline-offset-2 hover:underline">
                        {{ __('Remove') }}
                    </button>
                </form>
            </li>
        @empty
            <li class="text-sm text-[var(--app-text-muted)]">
                {{ __('No experience entries yet.') }}
            </li>
        @endforelse
    </ul>

    <form
        method="POST"
        action="{{ route('profile.experiences.store') }}"
        class="app-form mt-8 space-y-4 border-t border-[var(--app-border)] pt-6"
    >
        @csrf
        <h3 class="text-sm font-medium uppercase tracking-[0.18em] text-[var(--app-text-muted)]">
            {{ __('Add experience') }}
        </h3>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="experience_title" :value="__('Title')" />
                <x-text-input id="experience_title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>
            <div>
                <x-input-label for="experience_company" :value="__('Company')" />
                <x-text-input id="experience_company" name="company" type="text" class="mt-1 block w-full" :value="old('company')" required />
                <x-input-error class="mt-2" :messages="$errors->get('company')" />
            </div>
        </div>

        <div>
            <x-input-label for="experience_location" :value="__('Location')" />
            <x-text-input id="experience_location" name="location" type="text" class="mt-1 block w-full" :value="old('location')" />
            <x-input-error class="mt-2" :messages="$errors->get('location')" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="experience_started_at" :value="__('Start date')" />
                <x-text-input id="experience_started_at" name="started_at" type="date" class="mt-1 block w-full" :value="old('started_at')" required />
                <x-input-error class="mt-2" :messages="$errors->get('started_at')" />
            </div>
            <div>
                <x-input-label for="experience_ended_at" :value="__('End date')" />
                <x-text-input id="experience_ended_at" name="ended_at" type="date" class="mt-1 block w-full" :value="old('ended_at')" />
                <x-input-error class="mt-2" :messages="$errors->get('ended_at')" />
            </div>
        </div>

        <div>
            <x-input-label for="experience_description" :value="__('Description')" />
            <textarea
                id="experience_description"
                name="description"
                rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >{{ old('description') }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('description')" />
        </div>

        <x-primary-button>{{ __('Add experience') }}</x-primary-button>
    </form>
</section>
