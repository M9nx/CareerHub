<x-app.shell-layout :title="__('Feed')">
    <div class="app-feed-column">
        <header class="mb-8 space-y-3">
            <p class="swiss-eyebrow">{{ __('Community') }}</p>
            <h1 class="app-page-title">{{ __('Feed') }}</h1>
            <p class="max-w-[60ch] text-base opacity-70">
                {{ __('Posts, jobs, and updates from across CareerHub.') }}
            </p>
        </header>

        @if (session('error'))
            <div class="app-alert-error" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @include('feed.partials.composer')

        @if ($items->total() === 0)
            <div class="app-card p-8 text-center">
                <p class="text-sm opacity-70">
                    {{ __('No published posts available.') }}
                </p>
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
    </div>
</x-app.shell-layout>
