<x-app.page :title="__('Network')" :eyebrow="__('My network')">
    @if (session('success'))
        <div class="app-card mb-6 border border-[var(--app-border)] p-4 text-sm text-[var(--app-success)]">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="app-alert-error mb-6" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="space-y-8">
        <section class="space-y-4">
            <div class="flex items-end justify-between gap-3">
                <h2 class="text-sm font-medium uppercase tracking-[0.18em] text-[var(--app-text-muted)]">
                    {{ __('Invitations') }}
                </h2>
                @if ($pendingCount > 0)
                    <span class="app-badge">{{ $pendingCount }}</span>
                @endif
            </div>

            @forelse ($incoming as $invitation)
                <article class="app-card flex flex-wrap items-center justify-between gap-4 p-4">
                    <a href="{{ route('people.show', $invitation->requester) }}" class="flex min-w-0 items-center gap-3">
                        @if ($invitation->requester->avatarUrl())
                            <img src="{{ $invitation->requester->avatarUrl() }}" alt="" class="h-11 w-11 rounded-full object-cover ring-1 ring-[var(--app-border)]" />
                        @else
                            <div class="app-avatar">{{ str($invitation->requester->name)->substr(0, 1)->upper() }}</div>
                        @endif
                        <div class="min-w-0">
                            <p class="truncate font-medium text-[var(--app-text)]">{{ $invitation->requester->name }}</p>
                            <p class="truncate text-sm text-[var(--app-text-muted)]">
                                {{ $invitation->requester->headline ?: $invitation->requester->role?->label() }}
                            </p>
                        </div>
                    </a>
                    <div class="flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('network.connections.accept', $invitation) }}">
                            @csrf
                            <button type="submit" class="app-btn-primary">{{ __('Accept') }}</button>
                        </form>
                        <form method="POST" action="{{ route('network.connections.reject', $invitation) }}">
                            @csrf
                            <button type="submit" class="app-btn-secondary">{{ __('Ignore') }}</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="app-card p-6 text-center">
                    <p class="app-empty">{{ __('No pending invitations.') }}</p>
                </div>
            @endforelse
        </section>

        <section class="space-y-4">
            <h2 class="text-sm font-medium uppercase tracking-[0.18em] text-[var(--app-text-muted)]">
                {{ __('Sent requests') }}
            </h2>

            @forelse ($outgoing as $request)
                <article class="app-card flex flex-wrap items-center justify-between gap-4 p-4">
                    <a href="{{ route('people.show', $request->addressee) }}" class="flex min-w-0 items-center gap-3">
                        @if ($request->addressee->avatarUrl())
                            <img src="{{ $request->addressee->avatarUrl() }}" alt="" class="h-11 w-11 rounded-full object-cover ring-1 ring-[var(--app-border)]" />
                        @else
                            <div class="app-avatar">{{ str($request->addressee->name)->substr(0, 1)->upper() }}</div>
                        @endif
                        <div class="min-w-0">
                            <p class="truncate font-medium text-[var(--app-text)]">{{ $request->addressee->name }}</p>
                            <p class="truncate text-sm text-[var(--app-text-muted)]">{{ __('Pending') }}</p>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('network.connections.withdraw', $request) }}">
                        @csrf
                        <button type="submit" class="app-btn-secondary">{{ __('Withdraw') }}</button>
                    </form>
                </article>
            @empty
                <div class="app-card p-6 text-center">
                    <p class="app-empty">{{ __('No sent requests.') }}</p>
                </div>
            @endforelse
        </section>

        <section class="space-y-4">
            <h2 class="text-sm font-medium uppercase tracking-[0.18em] text-[var(--app-text-muted)]">
                {{ __('Connections') }}
            </h2>

            @forelse ($connections as $row)
                @php($person = $row['person'])
                @php($connection = $row['connection'])
                <article class="app-card flex flex-wrap items-center justify-between gap-4 p-4">
                    <a href="{{ route('people.show', $person) }}" class="flex min-w-0 items-center gap-3">
                        @if ($person->avatarUrl())
                            <img src="{{ $person->avatarUrl() }}" alt="" class="h-11 w-11 rounded-full object-cover ring-1 ring-[var(--app-border)]" />
                        @else
                            <div class="app-avatar">{{ str($person->name)->substr(0, 1)->upper() }}</div>
                        @endif
                        <div class="min-w-0">
                            <p class="truncate font-medium text-[var(--app-text)]">{{ $person->name }}</p>
                            <p class="truncate text-sm text-[var(--app-text-muted)]">
                                {{ $person->headline ?: $person->role?->label() }}
                            </p>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('network.connections.destroy', $connection) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="app-btn-secondary">{{ __('Remove') }}</button>
                    </form>
                </article>
            @empty
                <div class="app-card p-6 text-center">
                    <p class="app-empty">{{ __('No connections yet. Send a request from someone’s profile.') }}</p>
                </div>
            @endforelse
        </section>

        <section class="space-y-4">
            <h2 class="text-sm font-medium uppercase tracking-[0.18em] text-[var(--app-text-muted)]">
                {{ __('People you may know') }}
            </h2>

            @forelse ($suggestions as $person)
                <article class="app-card flex flex-wrap items-center justify-between gap-4 p-4">
                    <a href="{{ route('people.show', $person) }}" class="flex min-w-0 items-center gap-3">
                        @if ($person->avatarUrl())
                            <img src="{{ $person->avatarUrl() }}" alt="" class="h-11 w-11 rounded-full object-cover ring-1 ring-[var(--app-border)]" />
                        @else
                            <div class="app-avatar">{{ str($person->name)->substr(0, 1)->upper() }}</div>
                        @endif
                        <div class="min-w-0">
                            <p class="truncate font-medium text-[var(--app-text)]">{{ $person->name }}</p>
                            <p class="truncate text-sm text-[var(--app-text-muted)]">
                                {{ $person->headline ?: $person->role?->label() }}
                            </p>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('network.connect', $person) }}">
                        @csrf
                        <button type="submit" class="app-btn-primary">{{ __('Connect') }}</button>
                    </form>
                </article>
            @empty
                <div class="app-card p-6 text-center">
                    <p class="app-empty">{{ __('No suggestions right now.') }}</p>
                </div>
            @endforelse
        </section>
    </div>
</x-app.page>
