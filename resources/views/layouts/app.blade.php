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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @yield('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark shadow-sm fixed-top" style="background-color: #5a5c69;">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('home') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                   @auth
                    <ul class="navbar-nav me-auto">
                        @hasanyrole('Lider ToolRoom|Administrador ToolRoom|Administrador IT')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Catálogos
                            </a>
                            <ul class="dropdown-menu border-secondary">
                                <li><h2 class="dropdown-header fs-6">Manufactura »</h2></li>
                                <li><a class="dropdown-item" href="{{ route('catalogos.numeroparte-modelo.index') }}">Modelos</a></li>
                                <li><a class="dropdown-item" href="{{ route('catalogos.numeroparte.index') }}">Números de Parte</a></li>
                                <li><a class="dropdown-item" href="{{ route('catalogos.estacion.index') }}">Prensas</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><h2 class="dropdown-header fs-6">MTTO »</h2></li>
                                <li><a class="dropdown-item" href="{{ route('catalogos.clasificacion-falla.index') }}">Clasificación de Fallas</a></li>
                                <li><a class="dropdown-item" href="{{ route('catalogos.falla.index') }}">Fallas</a></li>
                                <li><a class="dropdown-item" href="{{ route('catalogos.causa-falla.index') }}">Causas Fallas</a></li>
                                <li><a class="dropdown-item" href="{{ route('catalogos.actividad-reparacion.index') }}">Actividades de Reparación</a></li>
                            </ul>
                        </li>
                        @endhasanyrole
                        @hasanyrole('Lider Prensas|Lider ToolRoom|Administrador Prensas|Administrador ToolRoom|Administrador IT|Tecnico Reparador')
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Mantenimiento
                            </a>
                            <ul class="dropdown-menu border-secondary">
                                <li><h2 class="dropdown-header fs-6">Procesos »</h2></li>
                                @hasrole('Lider Prensas')
                                <li><a class="dropdown-item" href="{{ route('mantenimiento.ordenesreparacion.create') }}">Generar OT</a></li>
                                @endhasrole
                                @hasanyrole('Lider Prensas|Lider ToolRoom')
                                <li><a class="dropdown-item" href="{{ route('mantenimiento.ordenesreparacion.index') }}">Seguimiento OT</a></li>
                                <li><a class="dropdown-item" href="{{ route('mantenimiento.ordenesreparacion-falla.index') }}">Seguimiento OT (Fallas)</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @endhasanyrole
                                @hasrole('Tecnico Reparador')
                                <li><a class="dropdown-item" href="{{ route('mantenimiento.kiosco-reparaciones.index') }}">Kiosco de Reparaciones</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @endhasrole
                                <li><a class="dropdown-item" href="{{ route('mantenimiento.tablero.control-correctivos.index') }}" target="_blank">Tablero: Control de Correctivos</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><h2 class="dropdown-header fs-6">Reportes »</h2></li>
                                @hasanyrole('Lider Prensas|Lider ToolRoom|Administrador Prensas|Administrador ToolRoom|Administrador IT')
                                <li><a class="dropdown-item" href="{{ route('mantenimiento.reportes.ordenesreparacion.index') }}">Ordenes de reparación</a></li>
                                <li><a class="dropdown-item" href="{{ route('mantenimiento.reportes.ordenesreparacion-fallas.index') }}">Fallas</a></li>
                                @endhasanyrole
                            </ul>
                        </li>
                        @endhasanyrole
                        @hasanyrole('Lider ToolRoom|Administrador ToolRoom|Administrador IT')
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Seguridad
                            </a>
                            <ul class="dropdown-menu border-secondary">
                                <li><a class="dropdown-item" href="{{ route('seguridad.usuarios.index') }}">Usuarios</a></li>
                            </ul>
                        </li>
                        @endhasanyrole
                    </ul>
                    @endauth

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item me-1">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Inicio de sesión') }}</a>
                                </li>
                                <button id="theme-toggle" class="btn btn-light">
                                    <i id="theme-icon" class="bi bi-sun-fill"></i>
                                </button>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <span class="text-light me-1">{{ Auth::user()->nombre }}</span>
                                <a role="button" class="btn btn-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Salir">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"></path>
                                        <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"></path>
                                    </svg>
                                </a>

                                <button id="theme-toggle" class="btn btn-light">
                                    <i id="theme-icon" class="bi bi-sun-fill"></i>
                                </button>

                                <button type="button" class="btn btn-outline-light" onclick="window.dispatchEvent(new CustomEvent('information-system'))">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                        <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286m1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94"/>
                                    </svg>
                                </button>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            <div class="{{ Route::is('login') ? '' : 'row-main' }}">
                @yield('content')
            </div>

            @include('shared.formulario-informacion')
        </main>
    </div>
    
    <script type="text/javascript">
         document.addEventListener("DOMContentLoaded", function () {
            const themeToggle = document.getElementById("theme-toggle");
            const themeIcon = document.getElementById("theme-icon");
            const savedTheme = localStorage.getItem("theme") || "light";
            
            document.documentElement.setAttribute("data-bs-theme", savedTheme);
            themeIcon.className = savedTheme === "dark" ? "bi bi-moon-stars-fill" : "bi bi-sun-fill";

            themeToggle.addEventListener("click", function () {
                let currentTheme = document.documentElement.getAttribute("data-bs-theme");
                let newTheme = currentTheme === "light" ? "dark" : "light";
                document.documentElement.setAttribute("data-bs-theme", newTheme);
                localStorage.setItem("theme", newTheme);
                themeIcon.className = newTheme === "dark" ? "bi bi-moon-stars-fill" : "bi bi-sun-fill";
            });
        });
    </script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
