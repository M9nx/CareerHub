@php
    $features = config('landing.features');
@endphp

<section id="features" class="swiss-section bg-[var(--landing-canvas-alt)]">
    <div class="swiss-container space-y-12">
        <x-landing.section-heading
            index="02"
            eyebrow="Platform modules"
            title="Everything you need to hire and get hired"
            description="Bento modules map directly to CareerHub workflows — post roles, track applications, stay visible."
            align="center"
        />

        <div class="landing-bento">
            @foreach ($features as $index => $feature)
                <article @class([
                    'group landing-bento-tile landing-reveal',
                    'landing-bento-tile--hero' => $feature['span'] === 'hero',
                    'landing-reveal-delay-1' => $index === 1,
                    'landing-reveal-delay-2' => $index === 2,
                ])>
                    <div class="landing-bento-tile__media" aria-hidden="true">
                        @if (isset($feature['video']))
                            <video
                                data-landing-tile-video
                                class="h-full w-full object-cover"
                                muted
                                loop
                                playsinline
                                preload="none"
                                poster="{{ asset($feature['poster']) }}"
                            >
                                <source src="{{ asset($feature['video']) }}" type="video/mp4">
                            </video>
                        @else
                            <img
                                src="{{ asset($feature['image']) }}"
                                alt=""
                                width="1000"
                                height="667"
                                loading="lazy"
                                decoding="async"
                            >
                        @endif
                    </div>

                    <div class="landing-bento-tile__body text-white">
                        <p class="text-xs font-medium uppercase tracking-[0.2em] opacity-75">
                            {{ $feature['key'] }}
                        </p>
                        <h3 @class([
                            'mt-2 font-normal tracking-tight',
                            'text-2xl md:text-3xl' => $feature['span'] === 'hero',
                            'text-xl' => $feature['span'] !== 'hero',
                        ])>
                            {{ $feature['title'] }}
                        </h3>
                        <p class="mt-2 max-w-[42ch] text-sm leading-relaxed opacity-85">
                            {{ $feature['description'] }}
                        </p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
