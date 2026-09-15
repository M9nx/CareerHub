<x-app.shell-layout :title="__('Home')">
    <x-app.feed.layout>
        <x-slot:left>
            <x-app.feed.profile-summary />
            <div class="xl:hidden">
                <x-app.sidebar.profile-completion :checks="$profileChecks" />
            </div>
        </x-slot:left>

        <x-slot:right>
            <x-app.sidebar.profile-completion :checks="$profileChecks" />
            <x-app.sidebar.job-suggestions :jobs="$suggestedJobs" />
            <x-app.sidebar.people-suggestions :people="$suggestedPeople" />
        </x-slot:right>

        @if (session('error'))
            <div class="app-alert-error" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <x-app.feed.composer />

        @if ($items->total() === 0)
            <div class="app-card p-8 text-center">
                <p class="app-empty">{{ __('No published posts available.') }}</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($items as $item)
                    @include('feed.partials.timeline-item', ['item' => $item])
                @endforeach
            </div>

            <div class="mt-6">
                {{ $items->links() }}
            </div>
        @endif
    </x-app.feed.layout>
</x-app.shell-layout>
