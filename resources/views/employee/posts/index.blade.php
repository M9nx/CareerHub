<x-app.page :title="__('My Posts')" eyebrow="{{ __('Employee') }}">
    <x-slot name="actions">
        @can('create', App\Models\Post::class)
            <a href="{{ route('employee.posts.create') }}" class="swiss-btn-primary">
                {{ __('Create Post') }}
            </a>
        @endcan
    </x-slot>

    <div class="app-card p-6">
        <x-app.flash />

        @if ($posts->isEmpty())
            <p class="app-empty">{{ __('You have not created any posts yet.') }}</p>
        @else
            <div class="overflow-x-auto">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($posts as $post)
                            <tr>
                                <td>{{ $post->title }}</td>
                                <td>{{ str($post->status->name)->headline() }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('employee.posts.edit', $post) }}" class="app-link">
                                            {{ __('Edit') }}
                                        </a>
                                        <form method="POST" action="{{ route('employee.posts.destroy', $post) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="swiss-btn-danger">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app.page>
