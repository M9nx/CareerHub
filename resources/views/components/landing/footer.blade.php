@php
    $navigation = config('landing.navigation');
@endphp

<footer class="border-t border-[var(--landing-rule)] bg-[var(--landing-canvas-alt)] py-12 md:py-16">
    <div class="swiss-container">
        <div class="swiss-grid gap-y-10">
            <div class="col-span-12 md:col-span-6">
                <p class="text-sm font-medium uppercase tracking-[0.18em]">
                    {{ config('app.name', 'CareerHub') }}
                </p>
                <p class="mt-4 max-w-[48ch] text-sm leading-relaxed opacity-70">
                    {{ config('landing.meta.description') }}
                </p>
            </div>

            <div class="col-span-6 md:col-span-3">
                <p class="swiss-eyebrow">{{ __('Explore') }}</p>
                <ul class="mt-2 text-sm opacity-70">
                    @foreach ($navigation as $item)
                        <li>
                            <a href="{{ $item['href'] }}" class="landing-footer-link">
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="col-span-6 md:col-span-3">
                <p class="swiss-eyebrow">{{ __('Account') }}</p>
                <ul class="mt-2 text-sm opacity-70">
                    <li><a href="{{ route('login') }}" class="landing-footer-link">{{ __('Log in') }}</a></li>
                    <li><a href="{{ route('register') }}" class="landing-footer-link">{{ __('Register') }}</a></li>
                </ul>
            </div>
        </div>

        <div class="swiss-rule mt-10"></div>
        <p class="mt-6 text-xs opacity-40">
            &copy; {{ date('Y') }} {{ config('app.name', 'CareerHub') }}. {{ __('All rights reserved.') }}
        </p>
    </div>
</footer>
