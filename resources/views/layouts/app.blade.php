<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OtakAtik')</title>

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-p1CmF0sH3z8s6q+YQ+VZk5Zk+0u2b9Qf6eZ1Vw1Xo6x1qX6eY5q1K1Z1pWvY1Z1Z1Z1Z1Z1Z1Z1Z1Z1Z1Z1Z1Z1Z==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- App CSS (built by Vite or plain asset) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('head')
</head>
<body class="bg-gray-50 font-sans leading-normal tracking-normal">

    @auth
        @include('components.navbar')
        <div class="pt-20"> <!-- offset for fixed navbar -->
            @yield('content')
        </div>
    @else
        @includeWhen(View::exists('components.navbar'), 'components.navbar')
        <div class="pt-20">
            @yield('content')
        </div>
    @endauth

    <!-- App JS -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
