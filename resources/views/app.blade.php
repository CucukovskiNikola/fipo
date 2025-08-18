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

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

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
        
        {{-- Twitter Card Meta Tags --}}
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{{ $page['props']['meta']['title'] ?? config('app.name') }}">
        <meta name="twitter:description" content="{{ $page['props']['meta']['description'] }}">
    @else
        <meta name="description" content="findemich - Connect with trusted local business partners and discover quality services in your area.">
        <meta name="keywords" content="findemich, business partners, local services, verified companies, business networking">
        
        {{-- Default Open Graph --}}
        <meta property="og:title" content="{{ config('app.name') }}">
        <meta property="og:description" content="findemich - Connect with trusted local business partners and discover quality services in your area.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ request()->url() }}">
        <meta property="og:site_name" content="findemich">
        
        {{-- Default Twitter Card --}}
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{{ config('app.name') }}">
        <meta name="twitter:description" content="findemich - Connect with trusted local business partners and discover quality services in your area.">
    @endif

    <link rel="preload" as="image" href="{{ asset('images/bg.webp') }}" type="image/webp" fetchpriority="high">
    <link rel="preload" as="image" href="{{ asset('images/logo.svg') }}" type="image/svg+xml" fetchpriority="high">

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">

    @routes
    @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>