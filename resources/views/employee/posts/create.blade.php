<x-app.page :title="__('Create Post')" eyebrow="{{ __('Employee') }}" narrow>
    <div class="app-card p-6">
        <form method="POST" action="{{ route('employee.posts.store') }}" class="app-form space-y-6">
            @csrf

            <div>
                <x-input-label for="title" :value="__('Title')" />
                <x-text-input id="title" name="title" type="text" class="block w-full" :value="old('title')" required autofocus maxlength="255" />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="body" :value="__('Body')" />
                <textarea id="body" name="body" rows="8" required>{{ old('body') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('body')" />
            </div>

            <div>
                <label for="publish" class="inline-flex items-center gap-2 text-sm opacity-80">
                    <input id="publish" name="publish" type="checkbox" value="1" @checked(old('publish'))>
                    {{ __('Publish post') }}
                </label>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('employee.posts.index') }}" class="swiss-btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="swiss-btn-primary">{{ __('Create Post') }}</button>
            </div>
        </form>
    </div>
</x-app.page>
