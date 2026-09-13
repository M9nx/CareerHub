@php
    $steps = config('landing.workflow');
@endphp

<section id="workflow" class="swiss-section bg-[var(--landing-canvas-alt)]">
    <div class="swiss-container space-y-12">
        <x-landing.section-heading
            index="04"
            eyebrow="How it works"
            title="From registration to outcome in three steps"
            description="A linear Swiss workflow — no hidden states, no duplicate tools."
            align="center"
        />

        <ol class="swiss-grid">
            @foreach ($steps as $index => $step)
                <li @class([
                    'landing-reveal col-span-12 border-t border-[var(--landing-rule)] pt-6 md:col-span-4',
                    'landing-reveal-delay-1' => $index === 1,
                    'landing-reveal-delay-2' => $index === 2,
                ])>
                    <p class="text-5xl font-light tabular-nums tracking-tight opacity-20">
                        {{ $step['step'] }}
                    </p>
                    <h3 class="mt-4 text-xl font-normal tracking-tight">
                        {{ $step['title'] }}
                    </h3>
                    <p class="mt-3 max-w-[36ch] text-sm leading-relaxed opacity-70">
                        {{ $step['description'] }}
                    </p>
                </li>
            @endforeach
        </ol>

        <div class="landing-reveal flex flex-col items-center gap-4 border-t border-[var(--landing-rule)] pt-10 sm:flex-row sm:justify-center">
            <a href="{{ route('register', ['role' => 'Employer']) }}" class="swiss-btn-primary">
                {{ __('Register as Employer') }}
            </a>
            <a href="{{ route('register', ['role' => 'Employee']) }}" class="swiss-btn-secondary">
                {{ __('Register as Employee') }}
            </a>
        </div>
    </div>
</section>
