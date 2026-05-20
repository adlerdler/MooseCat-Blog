<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- 默认 SEO（Vue 组件会动态覆盖） --}}
    <title>@yield('title', 'ARCHYX')</title>
    <meta name="description" content="Exploring digital constructivism through articles, videos, and projects">
    <meta name="keywords" content="architecture, digital archive, constructivism, design, technology">
    <link rel="canonical" href="{{ config('app.url') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-construct-paper text-construct-black selection:bg-construct-red selection:text-white">
    <div id="app">
        @yield('content')
    </div>
</body>
</html>