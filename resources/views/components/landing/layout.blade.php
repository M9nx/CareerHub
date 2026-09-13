@props([
    'title' => null,
    'description' => null,
])

@php
    $meta = config('landing.meta');
    $pageTitle = $title ?? $meta['title'];
    $pageDescription = $description ?? $meta['description'];
    $canonical = url()->current();
    $socialImage = asset($meta['social_image']);

    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => config('app.name'),
        'url' => $canonical,
        'description' => $pageDescription,
        'potentialAction' => [
            '@type' => 'RegisterAction',
            'target' => route('register'),
        ],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="light dark">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $pageTitle }}</title>
        <meta name="description" content="{{ $pageDescription }}">
        <link rel="canonical" href="{{ $canonical }}">

        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $pageDescription }}">
        <meta property="og:url" content="{{ $canonical }}">
        <meta property="og:image" content="{{ $socialImage }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $pageTitle }}">
        <meta name="twitter:description" content="{{ $pageDescription }}">
        <meta name="twitter:image" content="{{ $socialImage }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/landing.js'])

        {{-- Without JS the reveal animations never run, so ship the page fully visible. --}}
        <noscript>
            <style>
                .landing-loader { display: none !important; }
                .landing-reveal { opacity: 1 !important; transform: none !important; }
            </style>
        </noscript>

        <script type="application/ld+json">
            {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    </head>
    <body class="landing-shell bg-[var(--landing-canvas)] font-sans text-[var(--landing-ink)] antialiased">
        <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[110] focus:bg-[var(--landing-accent)] focus:px-4 focus:py-2 focus:text-white">
            {{ __('Skip to content') }}
        </a>

        {{ $slot }}
    </body>
</html>
