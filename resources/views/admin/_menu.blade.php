{{-- Cabecera, submenú y estilos comunes del panel de administración --}}
@push('styles')
<style>
    .admin-wrap { padding-bottom: 3rem; }
    .admin-menu { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 1.75rem; }
    .admin-menu a {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .55rem 1.2rem; border-radius: 25px; text-decoration: none; font-size: .9rem;
        border: 2px solid var(--primary); color: var(--primary); transition: all .18s;
    }
    .admin-menu a:hover, .admin-menu a.active { background: var(--primary); color: #fff; }
    .admin-titulo { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .admin-titulo h2 { color: var(--primary-dark); font-size: 1.4rem; }

    .caja { background: #fff; border-radius: 14px; padding: 1.5rem; box-shadow: 0 2px 14px rgba(52,130,137,.1); margin-bottom: 1.5rem; }
    .caja h3 { color: var(--primary-dark); font-size: 1.05rem; margin-bottom: 1rem; display: flex; align-items: center; gap: .4rem; }
    .caja h3 i { color: var(--primary); }

    /* Tablas */
    .tabla-scroll { overflow-x: auto; }
    table.tabla { width: 100%; border-collapse: collapse; font-size: .88rem; }
    .tabla th { text-align: left; color: var(--primary-dark); font-weight: 600; padding: .7rem .6rem; border-bottom: 2px solid var(--selected-bg); white-space: nowrap; }
    .tabla td { padding: .7rem .6rem; border-bottom: 1px solid var(--selected-bg); color: #444; vertical-align: middle; }
    .tabla tr:hover td { background: #f7fbfb; }
    .tabla a { color: var(--primary); }
    .vacio { text-align: center; color: #999; padding: 2rem 1rem; }

    .badge { padding: .25rem .7rem; border-radius: 20px; font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap; }
    .badge-pendiente { background: #fff3cd; color: #856404; }
    .badge-confirmada { background: var(--selected-bg); color: var(--primary-dark); }
    .badge-cancelada { background: #fde8e8; color: #700; }

    .btn-peq { padding: .4rem .9rem; font-size: .82rem; }
    .btn-peligro { background: #fff; color: #c33; border: 2px solid #f3c4c4; border-radius: 25px; padding: .4rem .9rem; font-size: .82rem; cursor: pointer; font-family: 'Geologica', sans-serif; display: inline-flex; align-items: center; gap: .3rem; }
    .btn-peligro:hover { background: #fde8e8; }

    /* Formularios */
    .campo { margin-bottom: 1.1rem; }
    .campo label { display: block; font-size: .88rem; font-weight: 500; color: var(--primary-dark); margin-bottom: .35rem; }
    .campo input[type=text], .campo input[type=number], .campo input[type=search], .campo input[type=date], .campo select, .campo textarea {
        width: 100%; padding: .65rem .9rem; border: 2px solid var(--selected-bg); border-radius: 10px;
        font-family: 'Geologica', sans-serif; font-size: .92rem; color: #333; background: #fff; outline: none;
    }
    .campo input:focus, .campo select:focus, .campo textarea:focus { border-color: var(--primary); }
    .campo .ayuda { font-size: .78rem; color: #888; margin-top: .25rem; }
    .campo .error { font-size: .8rem; color: #d44; margin-top: .25rem; }
    select.estado { padding: .35rem .6rem; border: 2px solid var(--selected-bg); border-radius: 8px; font-family: 'Geologica', sans-serif; font-size: .82rem; }

    @media (max-width: 768px) {
        .admin-wrap { padding-bottom: 2rem; }
        .admin-menu a { padding: .45rem .9rem; font-size: .82rem; }
        .caja { padding: 1.1rem; }
    }
</style>
@endpush

<div class="page-banner" style="padding-top:2rem;padding-bottom:1.75rem">
    <h1><i class="bi bi-speedometer2"></i> {{ __('Panel de administración') }}</h1>
</div>

<div class="container" style="padding-top:1.75rem">
    <nav class="admin-menu">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-bar-chart"></i> {{ __('Estadísticas') }}</a>
        <a href="{{ route('admin.agenda') }}" class="{{ request()->routeIs('admin.agenda') ? 'active' : '' }}"><i class="bi bi-calendar-week"></i> {{ __('Agenda') }}</a>
        <a href="{{ route('admin.clientes.index') }}" class="{{ request()->routeIs('admin.clientes.*') ? 'active' : '' }}"><i class="bi bi-people"></i> {{ __('Clientes') }}</a>
        <a href="{{ route('admin.servicios.index') }}" class="{{ request()->routeIs('admin.servicios.*') ? 'active' : '' }}"><i class="bi bi-grid-3x3-gap"></i> {{ __('Servicios') }}</a>
        <a href="{{ route('admin.configuracion') }}" class="{{ request()->routeIs('admin.configuracion') ? 'active' : '' }}"><i class="bi bi-gear"></i> {{ __('Configuración') }}</a>
    </nav>
</div>
