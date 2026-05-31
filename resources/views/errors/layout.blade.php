<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '403') - {{ config('app.name', 'ARCHYX') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
            background: #f9fafb;
        }
        .error-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            color: #e5e7eb;
            line-height: 1;
            user-select: none;
            margin-bottom: 0.5rem;
        }
        .error-icon {
            width: 5rem;
            height: 5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        .error-icon-403 {
            background: #fef3c7;
        }
        .error-icon-403 svg { color: #d97706; width: 2.5rem; height: 2.5rem; }
        .error-icon-404 {
            background: #dbeafe;
        }
        .error-icon-404 svg { color: #2563eb; width: 2.5rem; height: 2.5rem; }
        .error-icon-500 {
            background: #fee2e2;
        }
        .error-icon-500 svg { color: #dc2626; width: 2.5rem; height: 2.5rem; }
        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.75rem;
        }
        p {
            font-size: 0.95rem;
            color: #6b7280;
            text-align: center;
            max-width: 24rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .btn-group {
            display: flex;
            gap: 0.75rem;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background: #1f2937;
            color: white;
        }
        .btn-primary:hover { background: #374151; }
        .btn-secondary {
            background: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        .btn-secondary:hover { background: #f3f4f6; }
        .permission-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            font-size: 0.75rem;
            font-family: monospace;
            color: #6b7280;
            margin-bottom: 1.5rem;
        }
        .permission-dot {
            width: 0.375rem;
            height: 0.375rem;
            border-radius: 50%;
            background: #f59e0b;
        }
    </style>
</head>
<body>
    @php
        $code = (int)($exception->getStatusCode() ?? 403);
    @endphp
    <div class="error-container">
        @if($code === 403)
            <div class="error-icon error-icon-403">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
        @elseif($code === 404)
            <div class="error-icon error-icon-404">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="16" y1="16" x2="15.65" y2="15.65"/><line x1="7" y1="7" x2="11" y2="11"/></svg>
            </div>
        @else
            <div class="error-icon error-icon-500">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            </div>
        @endif

        <div class="error-code">{{ $code }}</div>

        <h1>@yield('title')</h1>

        @hasSection('message')
            <p>@yield('message')</p>
        @else
            <p>
                @if($code === 403)
                    抱歉，您没有权限访问此页面。
                @elseif($code === 404)
                    抱歉，您访问的页面不存在。
                @else
                    服务器遇到错误，请稍后重试。
                @endif
            </p>
        @endif

        <div class="btn-group">
            <a href="/admin" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                回到仪表盘
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                返回上一页
            </a>
        </div>
    </div>
</body>
</html>
