<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Archyx Blog - 探索建筑与技术的边界，构建未来的数字空间体验">
    <title>Archyx Blog</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body id="app">
    <welcome-page></welcome-page>
</body>
</html>
