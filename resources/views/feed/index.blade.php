<x-app.page :title="__('Feed')" eyebrow="{{ __('Community') }}" narrow>
    <p class="-mt-4 mb-8 max-w-[60ch] text-base opacity-70">
        {{ __('Posts, jobs, and updates from across CareerHub.') }}
    </p>

    @if (session('error'))
        <div class="app-alert-error" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @include('feed.partials.composer')

    @if ($items->total() === 0)
        <div class="app-card p-8 text-center">
            <p class="app-empty">{{ __('No published posts available.') }}</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($items as $item)
                @include('feed.partials.timeline-item', ['item' => $item])
            @endforeach
        </div>

        <div class="mt-8">
            {{ $items->links() }}
        </div>
    @endif
</x-app.page>
