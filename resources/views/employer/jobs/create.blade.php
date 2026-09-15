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
                <x-input-label for="location" :value="__('Location')" />
                <x-text-input id="location" name="location" type="text" class="block w-full" :value="old('location')" maxlength="255" />
                <x-input-error class="mt-2" :messages="$errors->get('location')" />
            </div>

            <div>
                <x-input-label for="employment_type" :value="__('Employment type')" />
                <select id="employment_type" name="employment_type">
                    <option value="">{{ __('Select type') }}</option>
                    @foreach (\App\Enums\EmploymentType::cases() as $type)
                        <option value="{{ $type->value }}" @selected(old('employment_type') === $type->value)>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('employment_type')" />
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
