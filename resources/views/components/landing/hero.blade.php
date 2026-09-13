@php
    $hero = config('landing.hero');
@endphp

<section class="swiss-section relative overflow-hidden border-b border-[var(--landing-rule)] bg-[var(--landing-canvas-alt)]">
    {{-- Aurora UI: animated mesh confined to the top band, behind headline + CTAs --}}
    <div class="landing-aurora-band" aria-hidden="true">
        <div class="landing-aurora-blob landing-aurora-blob--violet"></div>
    </div>

    <div class="swiss-container relative z-10">
        <div class="swiss-grid items-center">
            {{-- Swiss 12-col asymmetric split: 7 / 5 --}}
            <div class="landing-reveal col-span-12 space-y-8 lg:col-span-7 lg:pr-6">
                <div class="flex items-center gap-4">
                    <span class="swiss-eyebrow">{{ $hero['index'] }}</span>
                    <div class="swiss-rule flex-1"></div>
                    <span class="swiss-eyebrow">{{ $hero['eyebrow'] }}</span>
                </div>

                <h1 class="swiss-display">
                    {{ $hero['headline'] }}
                </h1>

                <p class="swiss-lead">
                    {{ $hero['subheadline'] }}
                </p>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="{{ route('register', ['role' => 'Employer']) }}" class="swiss-btn-primary">
                        {{ __('Register as Employer') }}
                    </a>
                    <a href="{{ route('register', ['role' => 'Employee']) }}" class="swiss-btn-secondary">
                        {{ __('Register as Employee') }}
                    </a>
                </div>

                <p class="text-sm opacity-50">
                    {{ __('Free to join · No credit card required') }}
                </p>
            </div>

            <div class="landing-reveal landing-reveal-delay-1 col-span-12 lg:col-span-5">
                <figure class="landing-video-frame aspect-[4/5] lg:aspect-[4/5]">
                    <video
                        data-landing-hero-video
                        class="h-full w-full object-cover"
                        autoplay
                        muted
                        loop
                        playsinline
                        preload="metadata"
                        poster="{{ asset($hero['poster']) }}"
                        aria-label="{{ $hero['video_alt'] }}"
                    >
                        <source src="{{ asset($hero['video']) }}" type="video/mp4">
                    </video>

                    <div class="landing-video-scrim" aria-hidden="true"></div>

                    <figcaption class="absolute inset-x-0 bottom-0 p-5">
                        <div class="landing-glass flex items-center justify-between gap-4 px-4 py-3">
                            <div>
                                <p class="text-[0.65rem] font-medium uppercase tracking-[0.2em] text-white/70">
                                    {{ $hero['overlay_label'] }}
                                </p>
                                <p class="mt-1 text-sm font-medium text-white">
                                    {{ $hero['overlay_value'] }}
                                </p>
                            </div>
                            <span class="landing-pulse" aria-hidden="true"></span>
                        </div>
                    </figcaption>
                </figure>
            </div>
        </div>
    </div>
</section>
