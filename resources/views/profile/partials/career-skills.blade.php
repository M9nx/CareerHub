<section>
    <header>
        <h2 class="text-lg font-medium text-[var(--app-text)]">
            {{ __('Skills') }}
        </h2>
        <p class="mt-1 text-sm text-[var(--app-text-muted)]">
            {{ __('Skills listed on your public profile.') }}
        </p>
    </header>

    <ul class="mt-6 flex flex-wrap gap-2">
        @forelse ($skills as $skill)
            <li class="inline-flex items-center gap-2 rounded-md border border-[var(--app-border)] px-3 py-1.5 text-sm text-[var(--app-text)]">
                <span>{{ $skill->name }}</span>
                <form
                    method="POST"
                    action="{{ route('profile.skills.destroy', $skill) }}"
                    onsubmit="return confirm(@js(__('Remove this skill?')))"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-[var(--app-text-muted)] hover:text-[var(--app-danger)]" aria-label="{{ __('Remove :skill', ['skill' => $skill->name]) }}">
                        &times;
                    </button>
                </form>
            </li>
        @empty
            <li class="text-sm text-[var(--app-text-muted)]">
                {{ __('No skills yet.') }}
            </li>
        @endforelse
    </ul>

    <form
        method="POST"
        action="{{ route('profile.skills.store') }}"
        class="app-form mt-6 flex flex-col gap-3 border-t border-[var(--app-border)] pt-6 sm:flex-row sm:items-end"
    >
        @csrf
        <div class="min-w-0 flex-1">
            <x-input-label for="skill_name" :value="__('Add a skill')" />
            <x-text-input
                id="skill_name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old('name')"
                maxlength="100"
                required
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>
        <x-primary-button>{{ __('Add skill') }}</x-primary-button>
    </form>
</section>
