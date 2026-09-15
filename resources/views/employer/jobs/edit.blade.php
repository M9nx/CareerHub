<x-app.page :title="__('Edit Job Posting')" eyebrow="{{ __('Employer') }}" narrow>
    <div class="app-card p-6">
        <x-app.flash />

        <form method="POST" action="{{ route('employer.jobs.update', $job) }}" class="app-form space-y-6">
            @csrf
            @method('PATCH')

            <div>
                <x-input-label for="title" :value="__('Title')" />
                <x-text-input id="title" name="title" type="text" class="block w-full" :value="old('title', $job->title)" required autofocus maxlength="255" />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="description" :value="__('Description')" />
                <textarea id="description" name="description" rows="8" required>{{ old('description', $job->description) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('description')" />
            </div>

            <div>
                <x-input-label for="status" :value="__('Status')" />
                <select id="status" name="status" required>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(old('status', $job->status->value) === $status->value)>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('status')" />
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('employer.jobs.index') }}" class="swiss-btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="swiss-btn-primary">{{ __('Update Job') }}</button>
            </div>
        </form>

        @if ($job->status === \App\Enums\JobPostingStatus::Draft)
            <form method="POST" action="{{ route('employer.jobs.publish', $job) }}" class="mt-6">
                @csrf
                <button type="submit" class="swiss-btn-primary">{{ __('Publish') }}</button>
            </form>
        @endif

        @if ($job->status === \App\Enums\JobPostingStatus::Published)
            <form method="POST" action="{{ route('employer.jobs.close', $job) }}" class="mt-6">
                @csrf
                <button type="submit" class="swiss-btn-secondary">{{ __('Close') }}</button>
            </form>
        @endif

        <div class="app-divider">
            <form method="POST" action="{{ route('employer.jobs.destroy', $job) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="swiss-btn-danger">{{ __('Delete Job') }}</button>
            </form>
        </div>
    </div>
</x-app.page>
