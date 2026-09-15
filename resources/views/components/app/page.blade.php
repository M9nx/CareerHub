@php
    $columnClass = match (true) {
        $narrow => 'app-page-column app-page-column--narrow',
        $wide => 'app-page-column app-page-column--wide',
        default => 'app-page-column',
    };
@endphp

<x-app.shell-layout :title="$title">
    <div class="{{ $columnClass }}">
        <header class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div class="space-y-2">
                @if ($eyebrow)
                    <p class="swiss-eyebrow">{{ $eyebrow }}</p>
                @endif

                <h1 class="app-page-title">{{ $title }}</h1>
            </div>

            @isset($actions)
                <div class="flex flex-wrap items-center gap-3">
                    {{ $actions }}
                </div>
            @endisset
        </header>

        {{ $slot }}
    </div>
</x-app.shell-layout>
