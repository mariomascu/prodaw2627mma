<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'YoguiFit') — {{ __('Especialistas en terapia deportiva') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montez&family=Geologica:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary:         #348289;
            --primary2:        #58999D;
            --primary-dark:    #123437;
            --primary-alt:     #719EA2;
            --terciario:       #92CBC5;
            --secondary:       #8ED4CC;
            --salmon:          #F9A392;
            --selected-bg:     #D7E5E3;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Geologica', sans-serif;
            background: #f5f8f8;
            color: #1a1a1a;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ──────── NAVBAR ──────── */
        .navbar {
            background: var(--primary-dark);
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,.35);
        }
        .navbar-brand {
            font-family: 'Montez', cursive;
            font-size: 2.1rem;
            color: #fff;
            text-decoration: none;
        }
        /* Icono del logo (piedras) en un solo color: toma el color de "background" */
        .logo-icono {
            display: inline-block; flex-shrink: 0;
            width: 1em; height: 1em;
            background: currentColor;
            -webkit-mask: url('{{ asset('img/logo_yoguifit.webp') }}') center / contain no-repeat;
                    mask: url('{{ asset('img/logo_yoguifit.webp') }}') center / contain no-repeat;
        }
        .navbar-brand { display: inline-flex; align-items: center; gap: .45rem; }
        .navbar-brand .logo-icono { font-size: 2.1rem; }

        .nav-links { display: flex; align-items: center; gap: .2rem; }
        .nav-links a {
            color: rgba(255,255,255,.82);
            text-decoration: none;
            padding: .45rem .85rem;
            border-radius: 6px;
            font-size: .88rem;
            transition: background .18s, color .18s;
            display: flex; align-items: center; gap: .3rem;
        }
        .nav-links a:hover, .nav-links a.active {
            background: var(--primary);
            color: #fff;
        }

        .nav-auth { display: flex; align-items: center; gap: .5rem; }
        .nav-auth a, .nav-auth button {
            color: rgba(255,255,255,.82);
            text-decoration: none;
            padding: .4rem .9rem;
            border-radius: 20px;
            font-size: .84rem;
            border: 1px solid transparent;
            cursor: pointer;
            font-family: 'Geologica', sans-serif;
            transition: all .18s;
            background: none;
        }
        .nav-auth .btn-outline { border-color: var(--terciario); color: var(--terciario); }
        .nav-auth .btn-outline:hover { background: var(--terciario); color: var(--primary-dark); }
        .nav-auth .btn-filled { background: var(--salmon); color: #fff; }
        .nav-auth .btn-filled:hover { background: #e8836f; }
        .nav-auth .btn-user { display: flex; align-items: center; gap: .3rem; }

        /* ──────── BANDERAS ──────── */
        .bandera {
            width: 20px; height: 14px; object-fit: cover; border-radius: 2px; flex-shrink: 0;
            box-shadow: 0 0 0 1px rgba(0,0,0,.12); vertical-align: middle;
        }

        /* ──────── SELECTOR DE IDIOMA (header) ──────── */
        .header-derecha { display: flex; align-items: center; gap: .5rem; }
        .idioma-selector { position: relative; }
        .idioma-selector summary {
            list-style: none; cursor: pointer; color: rgba(255,255,255,.85);
            padding: .4rem .7rem; border-radius: 20px; font-size: .82rem;
            display: flex; align-items: center; gap: .3rem; border: 1px solid rgba(255,255,255,.25);
        }
        .idioma-selector summary::-webkit-details-marker { display: none; }
        .idioma-selector summary:hover { background: rgba(255,255,255,.1); }
        .idioma-lista {
            position: absolute; right: 0; top: calc(100% + .4rem); z-index: 1100;
            background: #fff; border-radius: 10px; box-shadow: 0 6px 24px rgba(0,0,0,.2);
            padding: .35rem; min-width: 190px;
        }
        .idioma-lista a {
            display: flex; align-items: center; gap: .55rem;
            padding: .5rem .75rem; border-radius: 7px; color: var(--primary-dark); text-decoration: none; font-size: .88rem;
        }
        .idioma-lista a:hover { background: var(--selected-bg); }
        .idioma-lista a.activo { font-weight: 600; color: var(--primary); }
        .idioma-lista a .bi-check-lg { margin-left: auto; }

        /* ──────── BOTÓN DEL MENÚ LATERAL ──────── */
        .btn-menu {
            display: flex; align-items: center; gap: .45rem; cursor: pointer;
            background: rgba(255,255,255,.1); color: #fff; border: 1px solid rgba(255,255,255,.25);
            padding: .4rem .85rem; border-radius: 20px; font-family: 'Geologica', sans-serif; font-size: .85rem;
        }
        .btn-menu:hover { background: rgba(255,255,255,.2); }
        .btn-menu i { font-size: 1.15rem; }
        .btn-escaner { text-decoration: none; padding: .45rem .6rem; }

        /* ──────── MENÚ LATERAL ──────── */
        .menu-fondo {
            position: fixed; inset: 0; background: rgba(10,30,32,.5); z-index: 1500;
            opacity: 0; visibility: hidden; transition: opacity .25s, visibility .25s;
        }
        .menu-lateral {
            position: fixed; top: 0; right: 0; bottom: 0; z-index: 1600;
            width: 320px; max-width: 88vw; background: #fff;
            display: flex; flex-direction: column;
            transform: translateX(100%); transition: transform .3s ease;
        }
        body.menu-abierto .menu-fondo { opacity: 1; visibility: visible; }
        body.menu-abierto .menu-lateral { transform: none; box-shadow: -6px 0 30px rgba(0,0,0,.25); }
        body.menu-abierto { overflow: hidden; }

        .menu-cabecera {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: #fff; padding: 1.5rem 1.25rem 1.25rem; position: relative;
        }
        .menu-cerrar {
            position: absolute; top: .75rem; right: .75rem; background: none; border: none;
            color: #fff; font-size: 1.4rem; cursor: pointer; opacity: .8;
        }
        .menu-cerrar:hover { opacity: 1; }
        .menu-avatar {
            width: 52px; height: 52px; border-radius: 50%; background: var(--salmon);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; font-weight: 600; margin-bottom: .7rem;
        }
        .menu-nombre { font-weight: 600; font-size: 1.05rem; }
        .menu-email { font-size: .8rem; opacity: .8; word-break: break-all; }
        .menu-rol { display: inline-block; margin-top: .4rem; background: rgba(255,255,255,.2); padding: .15rem .6rem; border-radius: 10px; font-size: .72rem; text-transform: uppercase; letter-spacing: .04em; }
        .menu-acceso { display: flex; gap: .5rem; margin-top: .9rem; flex-wrap: wrap; }
        .menu-acceso a { flex: 1; text-align: center; padding: .5rem .75rem; border-radius: 20px; text-decoration: none; font-size: .85rem; white-space: nowrap; }
        .menu-acceso .entrar { border: 1px solid #fff; color: #fff; }
        .menu-acceso .registrarse { background: var(--salmon); color: #fff; }

        .menu-cuerpo { flex: 1; overflow-y: auto; padding: .75rem 0; }
        .menu-seccion { padding: .75rem 1.25rem .25rem; font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; color: #999; }
        .menu-cuerpo a {
            display: flex; align-items: center; gap: .7rem; padding: .6rem 1.25rem;
            color: var(--primary-dark); text-decoration: none; font-size: .92rem;
        }
        .menu-cuerpo a i { color: var(--primary); font-size: 1.05rem; width: 1.2rem; text-align: center; }
        .menu-cuerpo a:hover { background: var(--selected-bg); }
        .menu-cuerpo a.activo { font-weight: 600; background: #eef5f4; }
        .menu-cuerpo .menu-idiomas { display: flex; flex-wrap: wrap; gap: .4rem; padding: .25rem 1.25rem .5rem; }
        .menu-cuerpo .menu-idiomas a { padding: .45rem .6rem; border: 2px solid transparent; border-radius: 10px; }
        .menu-cuerpo .menu-idiomas a .bandera { width: 30px; height: 21px; }
        .menu-cuerpo .menu-idiomas a.activo { border-color: var(--primary); background: #eef5f4; }

        .menu-pie { border-top: 1px solid var(--selected-bg); padding: 1rem 1.25rem; }
        .menu-version { font-size: .78rem; color: #888; display: flex; align-items: center; gap: .35rem; }
        .menu-salir {
            width: 100%; display: flex; align-items: center; justify-content: center; gap: .4rem;
            background: #fff; color: #c33; border: 2px solid #f3c4c4; border-radius: 25px; padding: .6rem;
            font-family: 'Geologica', sans-serif; font-size: .9rem; cursor: pointer; margin-top: .75rem;
        }
        .menu-salir:hover { background: #fde8e8; }

        @media(max-width: 900px) {
            .nav-links, .nav-auth { display: none; }
        }
        @media(min-width: 769px) {
            .menu-solo-movil { display: none; }
        }
        @media(max-width: 768px) {
            .btn-menu .btn-menu-texto { display: none; }
            .btn-menu { padding: .45rem .6rem; }
        }

        /* ──────── DIVIDER ──────── */
        .divider-teal {
            height: 5px;
            background: linear-gradient(90deg, var(--primary-dark), var(--primary), var(--secondary), var(--primary), var(--primary-dark));
        }

        /* ──────── MAIN ──────── */
        main { flex: 1; }

        /* ──────── PAGE BANNER ──────── */
        .page-banner {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 55%, var(--secondary) 100%);
            color: #fff;
            padding: 3.5rem 1.5rem 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .page-banner::before {
            content: '';
            position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .page-banner h1 {
            font-family: 'Montez', cursive;
            font-size: 3rem;
            margin-bottom: .5rem;
            letter-spacing: .04em;
            position: relative;
        }
        .page-banner p { font-size: 1.05rem; opacity: .88; position: relative; }

        /* ──────── CONTENEDOR ──────── */
        .container { max-width: 1100px; margin: 0 auto; padding: 0 1.25rem; }
        .section { padding-top: 3rem; padding-bottom: 3rem; }

        /* ──────── BOTONES ──────── */
        .btn-primary-yf {
            display: inline-flex; align-items: center; gap: .4rem;
            background: var(--primary); color: #fff;
            padding: .65rem 1.6rem; border-radius: 25px;
            text-decoration: none; font-size: .9rem;
            font-family: 'Geologica', sans-serif; border: none; cursor: pointer;
            transition: background .18s, transform .1s;
        }
        .btn-primary-yf:hover { background: var(--primary2); color: #fff; transform: translateY(-1px); }

        .btn-outline-yf {
            display: inline-flex; align-items: center; gap: .4rem;
            background: transparent; color: var(--primary);
            padding: .65rem 1.6rem; border-radius: 25px;
            text-decoration: none; font-size: .9rem;
            font-family: 'Geologica', sans-serif; border: 2px solid var(--primary); cursor: pointer;
            transition: all .18s;
        }
        .btn-outline-yf:hover { background: var(--primary); color: #fff; }

        .btn-salmon {
            display: inline-flex; align-items: center; gap: .4rem;
            background: var(--salmon); color: #fff;
            padding: .65rem 1.6rem; border-radius: 25px;
            text-decoration: none; font-size: .9rem;
            font-family: 'Geologica', sans-serif; border: none; cursor: pointer;
            transition: background .18s;
        }
        .btn-salmon:hover { background: #e8836f; color: #fff; }

        /* ──────── CARDS ──────── */
        .card-yf {
            background: #fff; border-radius: 14px; overflow: hidden;
            box-shadow: 0 2px 14px rgba(52,130,137,.11);
            transition: transform .2s, box-shadow .2s;
        }
        .card-yf:hover { transform: translateY(-3px); box-shadow: 0 6px 26px rgba(52,130,137,.2); }

        /* ──────── ALERTS ──────── */
        .alert { padding: .85rem 1.2rem; border-radius: 8px; margin-bottom: 1rem; }
        .alert-success { background: var(--selected-bg); border-left: 4px solid var(--primary); color: var(--primary-dark); }
        .alert-error { background: #fde8e8; border-left: 4px solid #d44; color: #700; }

        /* ──────── FOOTER ──────── */
        footer {
            background: var(--primary-dark); color: rgba(255,255,255,.65);
            text-align: center; padding: 1.75rem 1.5rem; font-size: .8rem;
        }
        footer a { color: var(--terciario); text-decoration: none; }
        footer a:hover { color: var(--secondary); }
        .footer-links { display: flex; justify-content: center; gap: 1.2rem; flex-wrap: wrap; margin-bottom: .75rem; }
        .footer-contacto { display: flex; justify-content: center; gap: .5rem 1.2rem; flex-wrap: wrap; margin-bottom: .9rem; }
        .footer-contacto span, .footer-contacto a { display: inline-flex; align-items: center; gap: .35rem; }
        .footer-idiomas { display: flex; justify-content: center; align-items: center; gap: .3rem .9rem; flex-wrap: wrap; margin-bottom: .9rem; }
        .footer-idiomas a { display: inline-flex; align-items: center; gap: .35rem; }
        .footer-idiomas a { padding: .2rem; border-radius: 5px; border: 2px solid transparent; opacity: .75; }
        .footer-idiomas a:hover { opacity: 1; }
        .footer-idiomas a.activo { border-color: var(--terciario); opacity: 1; }
        .footer-idiomas .bandera { width: 26px; height: 18px; }

        /* ──────── TARJETAS DE TEXTO (páginas informativas) ──────── */
        .card-pad { padding: 2.5rem; }
        .card-pad h2 { color: var(--primary-dark); margin-bottom: 1rem; }
        .card-pad h3 { color: var(--primary); margin: 1.5rem 0 .5rem; }
        .card-pad p, .card-pad li { color: #555; line-height: 1.8; }
        .card-pad p + p { margin-top: 1rem; }
        .card-pad ul { margin: .5rem 0 0 1.25rem; }

        /* ──────── RESPONSIVE ──────── */
        @media (max-width: 768px) {
            .navbar { padding: .6rem 1rem; }
            .navbar-brand { font-size: 1.8rem; }
            .page-banner { padding: 2.25rem 1rem 2rem; }
            .page-banner h1 { font-size: 2.2rem; }
            .page-banner p { font-size: .95rem; }
            .container { padding: 0 1rem; }
            .section { padding-top: 2rem; padding-bottom: 2rem; }
            .card-pad { padding: 1.5rem 1.25rem; }
            footer { padding: 1.5rem 1rem; }
            .footer-links { gap: .5rem 1rem; }
        }
    </style>
    @stack('styles')
</head>
<body>

<nav class="navbar">
    <a href="{{ route('home') }}" class="navbar-brand"><span class="logo-icono" aria-hidden="true"></span>YoguiFit</a>

    <div class="nav-links">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i> {{ __('Inicio') }}
        </a>
        <a href="{{ route('servicios') }}" class="{{ request()->routeIs('servicios') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap"></i> {{ __('Servicios') }}
        </a>
        <a href="{{ route('tarifas') }}" class="{{ request()->routeIs('tarifas') ? 'active' : '' }}">
            <i class="bi bi-tag"></i> {{ __('Tarifas') }}
        </a>
        @auth
        <a href="{{ route('citas') }}" class="{{ request()->routeIs('citas') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i> {{ __('Mis citas') }}
        </a>
        @if(Auth::user()->is_admin)
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> {{ __('Panel') }}
        </a>
        @endif
        @endauth
    </div>

    <div class="header-derecha">
        @guest
        <div class="nav-auth">
            <a href="{{ route('login') }}" class="btn-outline">{{ __('Iniciar sesión') }}</a>
            <a href="{{ route('register') }}" class="btn-filled">{{ __('Registrarse') }}</a>
        </div>
        @endguest

        {{-- Selector de idioma --}}
        @include('layouts._selector-idioma')

        {{-- Lector de QR para validar citas (solo administrador) --}}
        @auth
            @if(Auth::user()->is_admin)
                <a href="{{ route('admin.escaner') }}" class="btn-menu btn-escaner" title="{{ __('Lector de QR') }}" aria-label="{{ __('Lector de QR') }}">
                    <i class="bi bi-qr-code-scan"></i>
                </a>
            @endif
        @endauth

        {{-- Botón que abre el menú lateral --}}
        <button type="button" class="btn-menu" id="abrirMenu" aria-label="{{ __('Abrir menú') }}" aria-controls="menuLateral" aria-expanded="false">
            @auth
                <i class="bi bi-person-circle"></i>
                <span class="btn-menu-texto">{{ Str::limit(Auth::user()->name, 18) }}</span>
            @else
                <i class="bi bi-list"></i>
                <span class="btn-menu-texto">{{ __('Menú') }}</span>
            @endauth
        </button>
    </div>
</nav>

{{-- ──────── MENÚ LATERAL ──────── --}}
<div class="menu-fondo" id="menuFondo"></div>
<aside class="menu-lateral" id="menuLateral" aria-label="{{ __('Menú') }}">
    <div class="menu-cabecera">
        <button type="button" class="menu-cerrar" id="cerrarMenu" aria-label="{{ __('Cerrar menú') }}"><i class="bi bi-x-lg"></i></button>
        @auth
            <div class="menu-avatar">{{ Str::upper(Str::substr(Auth::user()->name, 0, 1)) }}</div>
            <div class="menu-nombre">{{ Auth::user()->name }}</div>
            <div class="menu-email">{{ Auth::user()->email }}</div>
            <span class="menu-rol">{{ Auth::user()->is_admin ? __('Administrador') : __('Cliente') }}</span>
        @else
            <div class="menu-avatar"><i class="bi bi-person"></i></div>
            <div class="menu-nombre">{{ __('¡Hola!') }}</div>
            <div class="menu-email">{{ __('Inicia sesión para reservar y ver tus citas.') }}</div>
            <div class="menu-acceso">
                <a href="{{ route('login') }}" class="entrar">{{ __('Iniciar sesión') }}</a>
                <a href="{{ route('register') }}" class="registrarse">{{ __('Registrarse') }}</a>
            </div>
        @endauth
    </div>

    <nav class="menu-cuerpo">
        <div class="menu-seccion">{{ __('Navegación') }}</div>
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'activo' : '' }}"><i class="bi bi-house-door"></i> {{ __('Inicio') }}</a>
        <a href="{{ route('servicios') }}" class="{{ request()->routeIs('servicios') ? 'activo' : '' }}"><i class="bi bi-grid-3x3-gap"></i> {{ __('Servicios') }}</a>
        <a href="{{ route('tarifas') }}" class="{{ request()->routeIs('tarifas') ? 'activo' : '' }}"><i class="bi bi-tag"></i> {{ __('Tarifas') }}</a>

        @auth
            <div class="menu-seccion">{{ __('Mi cuenta') }}</div>
            <a href="{{ route('citas') }}" class="{{ request()->routeIs('citas') ? 'activo' : '' }}"><i class="bi bi-clock-history"></i> {{ __('Mis citas') }}</a>
            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'activo' : '' }}"><i class="bi bi-person-gear"></i> {{ __('Mi perfil') }}</a>
            @if(Auth::user()->is_admin)
                <div class="menu-seccion">{{ __('Administración') }}</div>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard', 'admin.clientes.*', 'admin.servicios.*') ? 'activo' : '' }}"><i class="bi bi-speedometer2"></i> {{ __('Panel de administración') }}</a>
                <a href="{{ route('admin.configuracion') }}" class="{{ request()->routeIs('admin.configuracion') ? 'activo' : '' }}"><i class="bi bi-gear"></i> {{ __('Configuración') }}</a>
            @endif
        @endauth

        @if(count(\App\Models\Ajuste::idiomasActivos()) > 1)
            {{-- En escritorio el idioma se elige en el header; en el menú lateral solo aparece en móvil --}}
            <div class="menu-solo-movil">
            <div class="menu-seccion">{{ __('Idioma') }}</div>
            <div class="menu-idiomas">
                @foreach(\App\Models\Ajuste::idiomasActivos() as $codigo => $nombre)
                    <a href="{{ route('idioma', $codigo) }}" class="{{ app()->getLocale() === $codigo ? 'activo' : '' }}" title="{{ $nombre }}" aria-label="{{ $nombre }}"><img src="{{ \App\Models\Ajuste::bandera($codigo) }}" alt="" class="bandera"></a>
                @endforeach
            </div>
            </div>
        @endif

        <div class="menu-seccion">{{ __('Información') }}</div>
        <a href="{{ route('contacto') }}"><i class="bi bi-telephone"></i> {{ __('Contacto') }}</a>
        <a href="{{ route('donde-estamos') }}"><i class="bi bi-geo-alt"></i> {{ __('Dónde estamos') }}</a>
        <a href="{{ route('acerca-de') }}"><i class="bi bi-info-circle"></i> {{ __('Acerca de') }}</a>
    </nav>

    <div class="menu-pie">
        <div class="menu-version"><i class="bi bi-git"></i> {{ __('Versión') }} {{ $versionApp ?? '—' }}</div>
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="menu-salir"><i class="bi bi-box-arrow-right"></i> {{ __('Cerrar sesión') }}</button>
            </form>
        @endauth
    </div>
</aside>

<div class="divider-teal"></div>

@if(session('success'))
    <div class="container" style="padding-top:1rem">
        <div class="alert alert-success"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
    </div>
@endif
@error('inicio')
    <div class="container" style="padding-top:1rem">
        <div class="alert alert-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
    </div>
@enderror
@if(session('error'))
    <div class="container" style="padding-top:1rem">
        <div class="alert alert-error"><i class="bi bi-exclamation-circle"></i> {{ session('error') }}</div>
    </div>
@endif

<main>
    @yield('content')
</main>

<div class="divider-teal"></div>

<footer>
    @php $telefono = \App\Models\Ajuste::valor('centro_telefono'); $email = \App\Models\Ajuste::valor('centro_email'); @endphp
    <div class="footer-contacto">
        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $telefono) }}"><i class="bi bi-telephone"></i> {{ $telefono }}</a>
        <a href="mailto:{{ $email }}"><i class="bi bi-envelope"></i> {{ $email }}</a>
        <span><i class="bi bi-geo-alt"></i> {{ \App\Models\Ajuste::valor('centro_direccion') }}</span>
    </div>
    <div class="footer-links">
        <a href="{{ route('contacto') }}">{{ __('Contacto') }}</a>
        <a href="{{ route('donde-estamos') }}">{{ __('Dónde estamos') }}</a>
        <a href="{{ route('acerca-de') }}">{{ __('Acerca de') }}</a>
        <a href="{{ route('aviso-legal') }}">{{ __('Aviso legal') }}</a>
        <a href="{{ route('politica-privacidad') }}">{{ __('Política de privacidad') }}</a>
        <a href="{{ route('terminos-condiciones') }}">{{ __('Términos y condiciones') }}</a>
    </div>
    @if(count(\App\Models\Ajuste::idiomasActivos()) > 1)
    <div class="footer-idiomas">
        <i class="bi bi-translate"></i>
        @foreach(\App\Models\Ajuste::idiomasActivos() as $codigo => $nombre)
            <a href="{{ route('idioma', $codigo) }}" class="{{ app()->getLocale() === $codigo ? 'activo' : '' }}" title="{{ $nombre }}" aria-label="{{ $nombre }}"><img src="{{ \App\Models\Ajuste::bandera($codigo) }}" alt="" class="bandera"></a>
        @endforeach
    </div>
    @endif
    <p>© {{ date('Y') }} YoguiFit · {{ __('Especialistas en terapia deportiva y masaje Thai Fusión') }} · {{ __('Versión') }} {{ $versionApp ?? '—' }}</p>
</footer>

<script>
    // Abrir y cerrar el menú lateral
    const botonMenu = document.getElementById('abrirMenu');

    function abrirMenu() {
        document.body.classList.add('menu-abierto');
        botonMenu.setAttribute('aria-expanded', 'true');
    }
    function cerrarMenu() {
        document.body.classList.remove('menu-abierto');
        botonMenu.setAttribute('aria-expanded', 'false');
    }

    botonMenu.addEventListener('click', abrirMenu);
    document.getElementById('cerrarMenu').addEventListener('click', cerrarMenu);
    document.getElementById('menuFondo').addEventListener('click', cerrarMenu);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarMenu(); });

    // El desplegable de idioma del header se cierra al pulsar fuera de él (o con Escape)
    document.addEventListener('click', e => {
        document.querySelectorAll('details.idioma-selector[open]').forEach(d => {
            if (!d.contains(e.target)) d.removeAttribute('open');
        });
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') document.querySelectorAll('details.idioma-selector[open]').forEach(d => d.removeAttribute('open'));
    });
</script>
@include('partials.mostrar-contrasena')
@stack('scripts')
</body>
</html>
