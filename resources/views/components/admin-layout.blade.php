@props(['title' => 'Admin'])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    @vite(['resources/css/admin.css'])
</head>
<body>
    <div class="admin-wrap">
        @if (session('admin_2fa_verified'))
            <div class="admin-topbar">
                <span class="admin-brand">~/admin <span class="text-comment">#</span></span>
                <nav class="admin-nav">
                    <a href="{{ route('admin.projects.index') }}" class="{{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">projects</a>
                    <a href="{{ route('admin.posts.index') }}" class="{{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">blog</a>
                    <a href="{{ route('admin.audit') }}" class="{{ request()->routeIs('admin.audit') ? 'active' : '' }}">audit</a>
                    <a href="{{ route('projects') }}" target="_blank">view site</a>
                    <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="admin-link-btn">logout</button>
                    </form>
                </nav>
            </div>
        @else
            <div class="admin-topbar">
                <span class="admin-brand">~/admin <span class="text-comment">#</span></span>
            </div>
        @endif

        @if (session('status'))
            <div class="admin-alert admin-alert-ok">
                <span class="text-keyword">&gt;</span> {{ session('status') }}
            </div>
        @endif

        {{ $slot }}
    </div>
</body>
</html>
