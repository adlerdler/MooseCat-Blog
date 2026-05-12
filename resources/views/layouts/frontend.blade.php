<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Archyx Blog - 探索建筑与技术的边界，构建未来的数字空间体验">
    <title>@yield('title') - ARCHYX</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- SEO Meta -->
    @yield('meta')

    <!-- Vite Resources -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-construct-paper text-construct-black selection:bg-construct-red selection:text-white">
    <!-- Vue App Mount Point -->
    <div id="app">
        @yield('content')
    </div>
</body>
</html>