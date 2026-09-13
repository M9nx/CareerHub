@php
    $stats = config('landing.stats');
    $trust = config('landing.trust');
@endphp

<section class="swiss-section bg-[var(--landing-canvas)]">
    <div class="swiss-container space-y-12">
        <div class="swiss-grid">
            @foreach ($stats as $index => $stat)
                <article @class([
                    'landing-reveal col-span-12 border-t border-[var(--landing-rule)] pt-6 md:col-span-4',
                    'landing-reveal-delay-1' => $index === 1,
                    'landing-reveal-delay-2' => $index === 2,
                ])>
                    <p
                        class="landing-stat-value"
                        data-counter-target="{{ $stat['value'] }}"
                        data-counter-suffix="{{ $stat['suffix'] }}"
                    >
                        0{{ $stat['suffix'] }}
                    </p>
                    <p class="mt-3 max-w-[30ch] text-sm opacity-70">
                        {{ $stat['label'] }}
                    </p>
                </article>
            @endforeach
        </div>

        <div class="landing-reveal space-y-6">
            <div class="flex items-center gap-4">
                <span class="swiss-eyebrow">{{ __('Trusted by growing teams') }}</span>
                <div class="swiss-rule flex-1"></div>
            </div>

            <ul class="swiss-grid gap-y-4">
                @foreach ($trust as $brand)
                    <li class="col-span-6 text-sm font-medium uppercase tracking-[0.14em] opacity-50 md:col-span-2 lg:col-span-2">
                        {{ $brand }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
