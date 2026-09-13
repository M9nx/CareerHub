@props([
    'index' => null,
    'eyebrow' => null,
    'title',
    'description' => null,
    'align' => 'left',
])

@php
    $alignment = $align === 'center'
        ? 'mx-auto max-w-3xl text-center'
        : 'max-w-2xl';
@endphp

<div {{ $attributes->merge(['class' => "landing-reveal space-y-4 {$alignment}"]) }}>
    @if ($index || $eyebrow)
        <div @class(['flex items-center gap-4', 'justify-center' => $align === 'center'])>
            @if ($index)
                <span class="swiss-eyebrow">{{ $index }}</span>
            @endif
            @if ($index && $eyebrow)
                <div class="swiss-rule w-12"></div>
            @endif
            @if ($eyebrow)
                <span class="swiss-eyebrow">{{ $eyebrow }}</span>
            @endif
        </div>
    @endif

    <h2 class="text-balance text-3xl font-light tracking-tight text-[var(--landing-ink)] md:text-4xl">
        {{ $title }}
    </h2>

    @if ($description)
        <p class="swiss-lead @if($align === 'center') mx-auto @endif">
            {{ $description }}
        </p>
    @endif
</div>
