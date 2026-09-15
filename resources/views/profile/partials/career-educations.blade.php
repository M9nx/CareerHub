<section>
    <header>
        <h2 class="text-lg font-medium text-[var(--app-text)]">
            {{ __('Education') }}
        </h2>
        <p class="mt-1 text-sm text-[var(--app-text-muted)]">
            {{ __('Schools and degrees on your public profile.') }}
        </p>
    </header>

    <ul class="mt-6 space-y-4">
        @forelse ($educations as $education)
            <li class="border-t border-[var(--app-border)] pt-4 first:border-0 first:pt-0">
                <form
                    method="POST"
                    action="{{ route('profile.educations.update', $education) }}"
                    class="app-form space-y-4"
                >
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label :for="'education_school_'.$education->id" :value="__('School')" />
                        <x-text-input
                            :id="'education_school_'.$education->id"
                            name="school"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('school', $education->school)"
                            required
                        />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label :for="'education_degree_'.$education->id" :value="__('Degree')" />
                            <x-text-input
                                :id="'education_degree_'.$education->id"
                                name="degree"
                                type="text"
                                class="mt-1 block w-full"
                                :value="old('degree', $education->degree)"
                            />
                        </div>
                        <div>
                            <x-input-label :for="'education_field_'.$education->id" :value="__('Field of study')" />
                            <x-text-input
                                :id="'education_field_'.$education->id"
                                name="field"
                                type="text"
                                class="mt-1 block w-full"
                                :value="old('field', $education->field)"
                            />
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label :for="'education_started_'.$education->id" :value="__('Start date')" />
                            <x-text-input
                                :id="'education_started_'.$education->id"
                                name="started_at"
                                type="date"
                                class="mt-1 block w-full"
                                :value="old('started_at', $education->started_at?->format('Y-m-d'))"
                            />
                        </div>
                        <div>
                            <x-input-label :for="'education_ended_'.$education->id" :value="__('End date')" />
                            <x-text-input
                                :id="'education_ended_'.$education->id"
                                name="ended_at"
                                type="date"
                                class="mt-1 block w-full"
                                :value="old('ended_at', $education->ended_at?->format('Y-m-d'))"
                            />
                        </div>
                    </div>

                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                </form>

                <form
                    method="POST"
                    action="{{ route('profile.educations.destroy', $education) }}"
                    class="mt-3"
                    onsubmit="return confirm(@js(__('Remove this education?')))"
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
                {{ __('No education entries yet.') }}
            </li>
        @endforelse
    </ul>

    <form
        method="POST"
        action="{{ route('profile.educations.store') }}"
        class="app-form mt-8 space-y-4 border-t border-[var(--app-border)] pt-6"
    >
        @csrf
        <h3 class="text-sm font-medium uppercase tracking-[0.18em] text-[var(--app-text-muted)]">
            {{ __('Add education') }}
        </h3>

        <div>
            <x-input-label for="education_school" :value="__('School')" />
            <x-text-input id="education_school" name="school" type="text" class="mt-1 block w-full" :value="old('school')" required />
            <x-input-error class="mt-2" :messages="$errors->get('school')" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="education_degree" :value="__('Degree')" />
                <x-text-input id="education_degree" name="degree" type="text" class="mt-1 block w-full" :value="old('degree')" />
                <x-input-error class="mt-2" :messages="$errors->get('degree')" />
            </div>
            <div>
                <x-input-label for="education_field" :value="__('Field of study')" />
                <x-text-input id="education_field" name="field" type="text" class="mt-1 block w-full" :value="old('field')" />
                <x-input-error class="mt-2" :messages="$errors->get('field')" />
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="education_started_at" :value="__('Start date')" />
                <x-text-input id="education_started_at" name="started_at" type="date" class="mt-1 block w-full" :value="old('started_at')" />
                <x-input-error class="mt-2" :messages="$errors->get('started_at')" />
            </div>
            <div>
                <x-input-label for="education_ended_at" :value="__('End date')" />
                <x-text-input id="education_ended_at" name="ended_at" type="date" class="mt-1 block w-full" :value="old('ended_at')" />
                <x-input-error class="mt-2" :messages="$errors->get('ended_at')" />
            </div>
        </div>

        <x-primary-button>{{ __('Add education') }}</x-primary-button>
    </form>
</section>
