<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ARCHYX') }}</title>

        <!-- Favicon (default, will be overridden by JS if siteConfig.favicon is set) -->
        <link id="dynamic-favicon" rel="icon" href="{{ asset('favicon.ico') }}" />

        {{-- SEO Meta Tags --}}
        <meta name="description" content="{{ $seo['description'] ?? '' }}">
        <meta name="keywords" content="{{ $seo['keywords'] ?? '' }}">
        <meta name="author" content="{{ $seo['author'] ?? '' }}">

        {{-- Open Graph / Facebook --}}
        <meta property="og:type" content="{{ $seo['og_type'] ?? 'website' }}">
        <meta property="og:title" content="{{ $seo['og_title'] ?: ($seo['title'] ?? config('app.name')) }}">
        <meta property="og:description" content="{{ $seo['og_description'] ?: ($seo['description'] ?? '') }}">
        @if(!empty($seo['og_image']))
            <meta property="og:image" content="{{ asset('storage/' . $seo['og_image']) }}">
        @endif
        <meta property="og:url" content="{{ $seo['canonical_url'] ?: url()->current() }}">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="{{ $seo['twitter_card'] ?? 'summary' }}">
        @if(!empty($seo['twitter_site']))
            <meta name="twitter:site" content="{{ $seo['twitter_site'] }}">
        @endif
        <meta name="twitter:title" content="{{ $seo['og_title'] ?: ($seo['title'] ?? config('app.name')) }}">
        <meta name="twitter:description" content="{{ $seo['og_description'] ?: ($seo['description'] ?? '') }}">
        @if(!empty($seo['og_image']))
            <meta name="twitter:image" content="{{ asset('storage/' . $seo['og_image']) }}">
        @endif

        {{-- Canonical URL --}}
        @if(!empty($seo['canonical_url']))
            <link rel="canonical" href="{{ $seo['canonical_url'] }}">
        @endif

        {{-- RSS Feed --}}
        @if(!empty($seo['rss_feed']))
            <link rel="alternate" type="application/rss+xml" href="{{ url('/feed') }}" title="RSS Feed">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js'])
        @inertiaHead

        {{-- Google Analytics --}}
        @if(!empty($seo['google_analytics']))
            @php $gaId = $seo['google_analytics']; @endphp
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{{ $gaId }}');
            </script>
        @endif

        {{-- Baidu Analytics --}}
        @if(!empty($seo['baidu_analytics']))
            @php $baId = $seo['baidu_analytics']; @endphp
            <script>
                var _hmt = _hmt || [];
                (function() {
                    var hm = document.createElement("script");
                    hm.src = "https://hm.baidu.com/hm.js?{{ $baId }}";
                    var s = document.getElementsByTagName("script")[0];
                    s.parentNode.insertBefore(hm, s);
                })();
            </script>
        @endif
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
