<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Routes -->
    @routes

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <script type="text/javascript">
         document.addEventListener("DOMContentLoaded", function () {
            const themeToggle = document.getElementById("theme-toggle");
            const themeIcon = document.getElementById("theme-icon");
            const savedTheme = localStorage.getItem("theme") || "light";
            
            document.documentElement.setAttribute("data-bs-theme", savedTheme);
            themeIcon.className = savedTheme === "dark" ? "bi bi-moon-stars-fill" : "bi bi-sun-fill";

                let currentTheme = document.documentElement.getAttribute("data-bs-theme");
                let newTheme = currentTheme === "light" ? "dark" : "light";
                document.documentElement.setAttribute("data-bs-theme", newTheme);
                localStorage.setItem("theme", newTheme);
                themeIcon.className = newTheme === "dark" ? "bi bi-moon-stars-fill" : "bi bi-sun-fill";
        });
    </script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
