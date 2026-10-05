@extends('layouts.yoguifit')
@section('title', 'YoguiFit — Inicio')

@push('styles')
<style>
    .hero {
        background: linear-gradient(160deg, var(--primary-dark) 0%, var(--primary) 50%, var(--secondary) 100%);
        min-height: 80vh;
        display: flex; align-items: center; justify-content: center;
        text-align: center; color: #fff; padding: 3rem 1.5rem;
        position: relative; overflow: hidden;
    }
    .hero::before {
        content: '';
        position: absolute; inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M40 40c0-11.046-8.954-20-20-20S0 28.954 0 40s8.954 20 20 20 20-8.954 20-20zm20 0c0 11.046 8.954 20 20 20s20-8.954 20-20-8.954-20-20-20-20 8.954-20 20z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .hero-content { position: relative; max-width: 700px; }
    .hero-logo {
        font-family: 'Montez', cursive;
        font-size: 5rem;
        line-height: 1;
        margin-bottom: .5rem;
        text-shadow: 0 3px 20px rgba(0,0,0,.3);
    }
    .hero-logo span { color: var(--salmon); }
    .hero-slogan {
        font-size: 1.15rem;
        font-weight: 300;
        opacity: .9;
        margin-bottom: 2.5rem;
        letter-spacing: .02em;
    }
    .hero-divider {
        width: 80px; height: 3px;
        background: var(--salmon);
        margin: 1rem auto 2rem;
        border-radius: 2px;
    }
    .hero-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        max-width: 600px;
        margin: 0 auto;
    }
    .action-card {
        background: rgba(255,255,255,.12);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,.25);
        border-radius: 14px;
        padding: 1.5rem 1rem;
        text-decoration: none;
        color: #fff;
        transition: background .2s, transform .2s;
        display: flex; flex-direction: column; align-items: center; gap: .6rem;
    }
    .action-card:hover {
        background: rgba(255,255,255,.22);
        transform: translateY(-4px);
        color: #fff;
    }
    .action-card i { font-size: 2.2rem; color: var(--salmon); }
    .action-card span { font-size: .95rem; font-weight: 500; text-transform: uppercase; letter-spacing: .05em; }

    /* Sección características */
    .features { background: #fff; padding: 4rem 0; }
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 2rem;
    }
    .feature-item { text-align: center; padding: 1.5rem; }
    .feature-item i {
        font-size: 3rem;
        color: var(--primary);
        margin-bottom: 1rem;
    }
    .feature-item h3 {
        font-size: 1.1rem;
        color: var(--primary-dark);
        margin-bottom: .5rem;
        font-weight: 600;
    }
    .feature-item p { font-size: .9rem; color: #666; line-height: 1.6; }

    /* CTA sección */
    .cta-section {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: 4rem 1.5rem;
        text-align: center;
        color: #fff;
    }
    .cta-section h2 {
        font-family: 'Montez', cursive;
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    .cta-section p { font-size: 1rem; opacity: .88; margin-bottom: 2rem; }
</style>
@endpush

@section('content')

<section class="hero">
    <div class="hero-content">
        <div class="hero-logo">Yogui<span>Fit</span></div>
        <div class="hero-divider"></div>
        <p class="hero-slogan">Especialistas en terapia deportiva y masaje Thai Fusión</p>
        <div class="hero-actions">
            <a href="{{ route('servicios') }}" class="action-card">
                <i class="bi bi-grid-3x3-gap-fill"></i>
                <span>Servicios</span>
            </a>
            <a href="{{ route('tarifas') }}" class="action-card">
                <i class="bi bi-tag-fill"></i>
                <span>Tarifas</span>
            </a>
            <a href="{{ route('cuestionario') }}" class="action-card">
                <i class="bi bi-calendar-check-fill"></i>
                <span>Reservar sesión</span>
            </a>
            @auth
            <a href="{{ route('citas') }}" class="action-card">
                <i class="bi bi-clock-history"></i>
                <span>Mis citas</span>
            </a>
            @else
            <a href="{{ route('login') }}" class="action-card">
                <i class="bi bi-person-circle"></i>
                <span>Ver mis citas</span>
            </a>
            @endauth
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <div class="features-grid">
            <div class="feature-item">
                <i class="bi bi-award"></i>
                <h3>Terapeutas certificados</h3>
                <p>Profesionales con formación especializada en masaje Thai y terapia deportiva.</p>
            </div>
            <div class="feature-item">
                <i class="bi bi-heart-pulse"></i>
                <h3>Bienestar integral</h3>
                <p>Combinamos técnicas orientales y occidentales para un tratamiento completo.</p>
            </div>
            <div class="feature-item">
                <i class="bi bi-stars"></i>
                <h3>Yoga terapéutico</h3>
                <p>Clases adaptadas a todos los niveles para mejorar tu salud y equilibrio.</p>
            </div>
            <div class="feature-item">
                <i class="bi bi-mortarboard"></i>
                <h3>Formación profesional</h3>
                <p>Cursos y talleres para quienes desean aprender y crecer en el sector.</p>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>¿Listo para empezar?</h2>
        <p>Reserva tu primera sesión y descubre cómo podemos ayudarte.</p>
        <a href="{{ route('cuestionario') }}" class="btn-salmon">
            <i class="bi bi-calendar-plus"></i> Reservar ahora
        </a>
        &nbsp;
        <a href="{{ route('servicios') }}" class="btn-outline-yf" style="border-color:#fff;color:#fff">
            <i class="bi bi-eye"></i> Ver servicios
        </a>
    </div>
</section>

@endsection
