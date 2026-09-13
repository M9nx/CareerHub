@php
    $showcase = config('landing.showcase');
@endphp

<section id="showcase" class="swiss-section bg-[var(--landing-canvas)]">
    <div class="swiss-container">
        <div class="swiss-grid items-center">
            <div class="col-span-12 lg:col-span-5">
                <x-landing.section-heading
                    :index="$showcase['index']"
                    :eyebrow="$showcase['eyebrow']"
                    :title="$showcase['headline']"
                    :description="$showcase['body']"
                />

                <ul class="landing-reveal landing-reveal-delay-1 mt-8 space-y-3">
                    @foreach ($showcase['highlights'] as $highlight)
                        <li class="flex items-baseline gap-3 border-t border-[var(--landing-rule)] pt-3 text-sm">
                            <span class="h-1.5 w-1.5 shrink-0 translate-y-[-2px] rounded-full bg-[var(--landing-accent)]" aria-hidden="true"></span>
                            <span class="opacity-70">{{ $highlight }}</span>
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('register') }}" class="swiss-btn-primary landing-reveal landing-reveal-delay-2 mt-8">
                    {{ __('Start free') }}
                </a>
            </div>

            <div class="landing-reveal landing-reveal-delay-1 col-span-12 lg:col-span-7">
                <div class="landing-video-frame aspect-video">
                    <video
                        class="h-full w-full object-cover"
                        controls
                        playsinline
                        preload="none"
                        poster="{{ asset($showcase['poster']) }}"
                    >
                        <source src="{{ asset($showcase['video']) }}" type="video/mp4">
                        <track
                            kind="captions"
                            src="{{ asset($showcase['captions']) }}"
                            srclang="en"
                            label="{{ __('English') }}"
                            default
                        >
                    </video>
                </div>
            </div>
        </div>
    </div>
</section>
