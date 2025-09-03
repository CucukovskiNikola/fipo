<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark'=> ($appearance ?? 'system') == 'dark'])>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Inline script to detect system dark mode preference and apply it immediately --}}
    <script>
    (function() {
        const appearance = '{{ $appearance ?? "system" }}';

        if (appearance === 'system') {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (prefersDark) {
                document.documentElement.classList.add('dark');
            }
        }
    })();
    </script>

    {{-- Inline style to set the HTML background color based on our theme in app.css --}}
    <style>
    html {
        background-color: oklch(1 0 0);
    }

    html.dark {
        background-color: oklch(0.145 0 0);
    }
    </style>

    <title inertia>{{ config('app.name', 'findemich') }}</title>

    {{-- SEO Meta Tags --}}
    @if(isset($page['props']['meta']))
        <meta name="description" content="{{ $page['props']['meta']['description'] }}">
        <meta name="keywords" content="{{ $page['props']['meta']['keywords'] ?? 'findemich, business partners, local services, verified companies, business networking' }}">
        
        {{-- Open Graph Meta Tags --}}
        <meta property="og:title" content="{{ $page['props']['meta']['title'] ?? config('app.name') }}">
        <meta property="og:description" content="{{ $page['props']['meta']['description'] }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ request()->url() }}">
        <meta property="og:site_name" content="findemich">
        <meta property="og:image" content="{{ asset('/og_image.webp') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
        
        {{-- Twitter Card Meta Tags --}}
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{{ $page['props']['meta']['title'] ?? config('app.name') }}">
        <meta name="twitter:description" content="{{ $page['props']['meta']['description'] }}">
        <meta name="twitter:image" content="{{ asset('/og_image.webp') }}">
    @else
        <meta name="description" content="findemich - Connect with trusted local business partners and discover quality services in your area.">
        <meta name="keywords" content="local business partners, verified companies, business networking, service providers, business directory">

        {{-- Default Open Graph --}}
        <meta property="og:title" content="{{ config('app.name') }}">
        <meta property="og:description" content="findemich - Connect with trusted local business partners and discover quality services in your area.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ request()->url() }}">
        <meta property="og:site_name" content="findemich">
        <meta property="og:image" content="{{ asset('/og_image.webp') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
        
        {{-- Default Twitter Card --}}
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{{ config('app.name') }}">
        <meta name="twitter:description" content="findemich - Connect with trusted local business partners and discover quality services in your area.">
        <meta name="twitter:image" content="{{ asset('/og_image.webp') }}">
    @endif

    <!-- Standard favicon -->
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">

<!-- Apple Touch Icon -->
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<meta name="apple-mobile-web-app-title" content="findemich">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

<!-- Android/Chrome -->
<link rel="manifest" href="/site.webmanifest">

<!-- Safari pinned tab -->
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">

<!-- Microsoft -->
<meta name="msapplication-TileColor" content="#2b5797">
<meta name="msapplication-TileImage" content="/mstile-150x150.png">

<!-- Theme color -->
<meta name="theme-color" content="#ffffff">

<link rel="canonical" href="{{ request()->url() }}">



    <link rel="preload" as="image" href="{{ asset('images/bg.webp') }}" type="image/webp" fetchpriority="high">
    @unless(request()->is('dashboard*'))
    <link rel="preload" as="image" href="{{ asset('images/logo.svg') }}" type="image/svg+xml" fetchpriority="high">
    @endunless

    <!-- Critical resource preloading for LCP optimization -->
    <link rel="preconnect" href="{{ url('/api/translate') }}" crossorigin>
    <link rel="dns-prefetch" href="//tile.openstreetmap.org">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    
    <!-- Resource hints for better performance -->
    <!-- Vite handles module preloading automatically, removing manual preload -->
    
    <!-- Preload critical CSS for immediate render -->
    <style>
        /* Critical CSS for immediate LCP element styling */
        .liquid-glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .aspect-video {
            aspect-ratio: 16 / 9;
        }
        .h-55 {
            height: 13.75rem;
        }
    </style>

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

    @routes
    @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>