@props([
    'person',
    'connection' => null,
])

@php
    use App\Enums\ConnectionStatus;
@endphp

@if (auth()->id() !== $person->id)
    <div class="mt-4 flex flex-wrap items-center gap-2">
        @if ($connection === null || in_array($connection->status, [ConnectionStatus::Rejected, ConnectionStatus::Withdrawn], true))
            @can('create', \App\Models\Connection::class)
                <form method="POST" action="{{ route('network.connect', $person) }}">
                    @csrf
                    <button type="submit" class="app-btn-primary">
                        {{ __('Connect') }}
                    </button>
                </form>
            @endcan
        @elseif ($connection->status === ConnectionStatus::Pending && (int) $connection->addressee_id === (int) auth()->id())
            <form method="POST" action="{{ route('network.connections.accept', $connection) }}">
                @csrf
                <button type="submit" class="app-btn-primary">{{ __('Accept') }}</button>
            </form>
            <form method="POST" action="{{ route('network.connections.reject', $connection) }}">
                @csrf
                <button type="submit" class="app-btn-secondary">{{ __('Ignore') }}</button>
            </form>
        @elseif ($connection->status === ConnectionStatus::Pending && (int) $connection->requester_id === (int) auth()->id())
            <span class="app-badge">{{ __('Pending') }}</span>
            <form method="POST" action="{{ route('network.connections.withdraw', $connection) }}">
                @csrf
                <button type="submit" class="app-btn-secondary">{{ __('Withdraw') }}</button>
            </form>
        @elseif ($connection->status === ConnectionStatus::Accepted)
            <span class="app-badge">{{ __('Connected') }}</span>
            <form method="POST" action="{{ route('network.connections.destroy', $connection) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="app-btn-secondary">{{ __('Remove') }}</button>
            </form>
        @endif
    </div>
@endif
