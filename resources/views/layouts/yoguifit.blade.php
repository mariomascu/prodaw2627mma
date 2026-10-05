<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'YoguiFit') — Especialistas en terapia deportiva</title>

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
        .navbar-brand span { color: var(--salmon); }

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

        .hamburger { display: none; background: none; border: none; cursor: pointer; }
        .hamburger span { display: block; width: 24px; height: 2px; background: #fff; margin: 5px 0; transition: .25s; }

        .mobile-menu {
            display: none; flex-direction: column;
            background: var(--primary-dark); padding: .5rem 1.2rem 1.2rem; gap: .1rem;
        }
        .mobile-menu a, .mobile-menu button {
            color: rgba(255,255,255,.82); text-decoration: none;
            padding: .65rem .2rem; font-size: .9rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex; align-items: center; gap: .5rem;
            background: none; border-left: none; border-right: none; border-top: none;
            cursor: pointer; font-family: 'Geologica', sans-serif; width: 100%;
        }
        .mobile-menu.open { display: flex; }

        @media(max-width: 768px) {
            .nav-links, .nav-auth { display: none; }
            .hamburger { display: block; }
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
        .section { padding: 3rem 0; }

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
    </style>
    @stack('styles')
</head>
<body>

<nav class="navbar">
    <a href="{{ route('home') }}" class="navbar-brand">Yogui<span>Fit</span></a>

    <div class="nav-links">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="bi bi-house-door"></i> Inicio
        </a>
        <a href="{{ route('servicios') }}" class="{{ request()->routeIs('servicios') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap"></i> Servicios
        </a>
        <a href="{{ route('tarifas') }}" class="{{ request()->routeIs('tarifas') ? 'active' : '' }}">
            <i class="bi bi-tag"></i> Tarifas
        </a>
        <a href="{{ route('cuestionario') }}" class="{{ request()->routeIs('cuestionario*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> Reservar
        </a>
        @auth
        <a href="{{ route('citas') }}" class="{{ request()->routeIs('citas') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i> Mis citas
        </a>
        @endauth
    </div>

    <div class="nav-auth">
        @guest
            <a href="{{ route('login') }}" class="btn-outline">Iniciar sesión</a>
            <a href="{{ route('register') }}" class="btn-filled">Registrarse</a>
        @else
            <a href="{{ route('profile.edit') }}" class="btn-user">
                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </button>
            </form>
        @endguest
    </div>

    <button class="hamburger" id="hamburger" aria-label="Menú">
        <span></span><span></span><span></span>
    </button>
</nav>

<div class="mobile-menu" id="mobileMenu">
    <a href="{{ route('home') }}"><i class="bi bi-house-door"></i> Inicio</a>
    <a href="{{ route('servicios') }}"><i class="bi bi-grid-3x3-gap"></i> Servicios</a>
    <a href="{{ route('tarifas') }}"><i class="bi bi-tag"></i> Tarifas</a>
    <a href="{{ route('cuestionario') }}"><i class="bi bi-calendar-check"></i> Reservar sesión</a>
    @auth
        <a href="{{ route('citas') }}"><i class="bi bi-clock-history"></i> Mis citas</a>
        <a href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> Mi perfil</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</button>
        </form>
    @else
        <a href="{{ route('login') }}"><i class="bi bi-door-open"></i> Iniciar sesión</a>
        <a href="{{ route('register') }}"><i class="bi bi-person-plus"></i> Registrarse</a>
    @endauth
    <hr style="border-color:rgba(255,255,255,.1);margin:.5rem 0">
    <a href="{{ route('contacto') }}"><i class="bi bi-telephone"></i> Contacto</a>
    <a href="{{ route('donde-estamos') }}"><i class="bi bi-geo-alt"></i> Dónde estamos</a>
    <a href="{{ route('acerca-de') }}"><i class="bi bi-info-circle"></i> Acerca de</a>
    <a href="{{ route('aviso-legal') }}"><i class="bi bi-file-text"></i> Aviso legal</a>
</div>

<div class="divider-teal"></div>

@if(session('success'))
    <div class="container" style="padding-top:1rem">
        <div class="alert alert-success"><i class="bi bi-check-circle"></i> {{ session('success') }}</div>
    </div>
@endif

<main>
    @yield('content')
</main>

<div class="divider-teal"></div>

<footer>
    <div class="footer-links">
        <a href="{{ route('contacto') }}">Contacto</a>
        <a href="{{ route('donde-estamos') }}">Dónde estamos</a>
        <a href="{{ route('acerca-de') }}">Acerca de</a>
        <a href="{{ route('aviso-legal') }}">Aviso legal</a>
        <a href="{{ route('politica-privacidad') }}">Política de privacidad</a>
        <a href="{{ route('terminos-condiciones') }}">Términos y condiciones</a>
    </div>
    <p>© {{ date('Y') }} YoguiFit · Especialistas en terapia deportiva y masaje Thai Fusión</p>
</footer>

<script>
    document.getElementById('hamburger').addEventListener('click', () => {
        document.getElementById('mobileMenu').classList.toggle('open');
    });
</script>
@stack('scripts')
</body>
</html>
