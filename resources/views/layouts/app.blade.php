<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BOSSTON - KTV POS System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Nav (Optional if needed, otherwise use sidebar) -->
            
            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                @yield('content')
            </main>

            <!-- Mobile Nav -->
            @include('partials.mobile-nav')
        </div>
    </div>

    <script>
      lucide.createIcons();
    </script>
</body>
</html>
