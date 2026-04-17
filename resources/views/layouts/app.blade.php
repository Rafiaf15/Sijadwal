<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistem Penjadwalan')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/app-layout.css') }}">
    @stack('head')
</head>
<body class="min-h-screen page-shell text-zinc-900" x-data>
    <div class="flex min-h-screen">
        @include('partials.sidebar')

        <main class="flex-1 overflow-auto">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
