<x-app.page :title="__('Notifications')" :eyebrow="__('Inbox')">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-[var(--app-text-muted)]">
            {{ trans_choice(':count unread|:count unread', $unreadCount, ['count' => $unreadCount]) }}
        </p>

        @if ($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="app-btn-secondary">{{ __('Mark all as read') }}</button>
            </form>
        @endif
    </div>

    @if (session('success'))
        <div class="app-card mb-6 p-4 text-sm text-[var(--app-success)]">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-3">
        @forelse ($notifications as $notification)
            <article @class([
                'app-card p-4',
                'border-[var(--app-primary)]' => $notification->unread(),
            ])>
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-[var(--app-text)]">
                            {{ data_get($notification->data, 'message', __('Notification')) }}
                        </p>
                        <time class="mt-1 block text-xs text-[var(--app-text-muted)]">
                            {{ $notification->created_at?->diffForHumans() }}
                        </time>
                    </div>

                    <form method="POST" action="{{ route('notifications.read', $notification) }}">
                        @csrf
                        <button type="submit" class="app-btn-secondary">
                            {{ $notification->unread() ? __('Open') : __('View') }}
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="app-card p-8 text-center">
                <p class="app-empty">{{ __('No notifications yet.') }}</p>
            </div>
        @endforelse
    </div>

    @if ($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</x-app.page>
