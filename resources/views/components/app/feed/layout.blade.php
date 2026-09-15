@props([
    'title' => null,
])

<div class="app-feed-layout">
    <aside class="app-feed-layout__left" aria-label="{{ __('Profile summary') }}">
        {{ $left }}
    </aside>

    <div class="app-feed-layout__center">
        {{ $slot }}
    </div>

    <aside class="app-feed-layout__right" aria-label="{{ __('Suggestions') }}">
        {{ $right }}
    </aside>
</div>
