<x-app.page :title="__('Create Job Posting')" eyebrow="{{ __('Employer') }}" narrow>
    <div class="app-card p-6">
        <form method="POST" action="{{ route('employer.jobs.store') }}" class="app-form space-y-6">
            @csrf

            <div>
                <x-input-label for="title" :value="__('Title')" />
                <x-text-input id="title" name="title" type="text" class="block w-full" :value="old('title')" required autofocus maxlength="255" />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="description" :value="__('Description')" />
                <textarea id="description" name="description" rows="8" required>{{ old('description') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('description')" />
            </div>

            <div>
                <x-input-label for="status" :value="__('Status')" />
                <select id="status" name="status" required>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(old('status', \App\Enums\JobPostingStatus::Draft->value) === $status->value)>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('status')" />
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('employer.jobs.index') }}" class="swiss-btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="swiss-btn-primary">{{ __('Create Job') }}</button>
            </div>
        </form>
    </div>
</x-app.page>
