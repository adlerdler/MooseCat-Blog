@extends('errors.layout')

@section('title', '403 - 访问被拒绝')

@section('content')
    <div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f5f0eb;font-family:'Space Grotesk',system-ui,sans-serif;">
        <div style="text-align:center;max-width:28rem;padding:2rem;">
            <div style="font-size:8rem;font-weight:900;color:#e5e7eb;line-height:1;user-select:none;">403</div>
            <h1 style="font-size:1.5rem;font-weight:700;color:#1f2937;margin:0.75rem 0;">访问被拒绝</h1>
            <p style="font-size:0.95rem;color:#6b7280;line-height:1.6;margin-bottom:2rem;">@yield('message', '抱歉，您没有权限访问此页面。')</p>
            <a href="/admin" style="display:inline-block;padding:0.75rem 1.5rem;background:#1f2937;color:white;text-decoration:none;border-radius:0.5rem;font-weight:500;">回到仪表盘</a>
        </div>
    </div>
@endsection
