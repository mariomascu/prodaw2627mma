@extends('layouts.yoguifit')
@section('title', 'YoguiFit — ' . __('Inicio'))

@push('styles')
<style>
    .hero {
        /* Fondo de mandala en mosaico (public/img/fondo_mandala_blanco_contraste.webp) */
        background: #e6f1ef url('{{ asset('img/fondo_mandala_blanco_contraste.webp') }}') repeat center top;
        min-height: 80vh;
        display: flex; align-items: center; justify-content: center;
        text-align: center; color: var(--primary-dark); padding: 3rem 1.5rem;
        position: relative; overflow: hidden;
    }
    .hero-content { position: relative; max-width: 820px; width: 100%; }
    .hero-logo {
        font-family: 'Montez', cursive;
        font-size: 5rem;
        line-height: 1;
        margin-bottom: .5rem;
        color: var(--primary);
        display: inline-flex; align-items: center; gap: .25em;
    }
    .hero-logo .logo-icono { font-size: .95em; }
    .hero-slogan {
        font-size: 1.15rem;
        font-weight: 400;
        color: var(--primary-dark);
        margin-bottom: 2.5rem;
        letter-spacing: .02em;
    }
    .hero-divider {
        width: 80px; height: 3px;
        background: var(--primary);
        margin: 1rem auto 2rem;
        border-radius: 2px;
    }
    .hero-actions {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        max-width: 640px;
        margin: 0 auto;
    }
    /* Botones del hero: verde turquesa */
    .action-card {
        background: var(--primary);
        border: 1px solid var(--primary);
        border-radius: 14px;
        padding: 1.5rem 1rem;
        text-decoration: none;
        color: #fff;
        box-shadow: 0 6px 18px rgba(52,130,137,.28);
        transition: background .2s, transform .2s, box-shadow .2s;
        display: flex; flex-direction: column; align-items: center; gap: .6rem;
    }
    .action-card:hover {
        background: var(--primary2);
        transform: translateY(-4px);
        box-shadow: 0 10px 24px rgba(52,130,137,.35);
        color: #fff;
    }
    .action-card i { font-size: 2.2rem; color: #fff; }
    .action-card span { font-size: .95rem; font-weight: 500; text-transform: uppercase; letter-spacing: .05em; text-align: center; }
    .hero-reservar { margin-top: 1.75rem; }
    .btn-reservar-hero {
        display: inline-flex; align-items: center; gap: .5rem;
        background: var(--primary-dark); color: #fff; text-decoration: none;
        font-size: 1rem; padding: .85rem 2.2rem; border-radius: 25px;
        box-shadow: 0 6px 20px rgba(18,52,55,.3);
        text-transform: uppercase; letter-spacing: .05em; font-weight: 500;
        transition: background .2s, transform .2s;
    }
    .btn-reservar-hero:hover { background: var(--primary); color: #fff; transform: translateY(-2px); }

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
    .cta-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }

    @media (max-width: 768px) {
        .hero { min-height: auto; padding: 3rem 1rem; }
        .hero-logo { font-size: 3.4rem; }
        .hero-slogan { font-size: 1rem; margin-bottom: 1.75rem; }
        /* Las 3 tarjetas siguen en una fila, más compactas */
        .hero-actions { gap: .6rem; }
        .action-card { padding: 1rem .35rem; gap: .45rem; border-radius: 12px; }
        .action-card i { font-size: 1.7rem; }
        .action-card span { font-size: .72rem; letter-spacing: .03em; }
        .features { padding: 2.5rem 0; }
        .features-grid { gap: 1rem; }
        .feature-item { padding: 1rem .5rem; }
        .cta-section { padding: 3rem 1rem; }
        .cta-section h2 { font-size: 2.1rem; }
    }
</style>
@endpush

@section('content')

<section class="hero">
    <div class="hero-content">
        <div class="hero-logo"><span class="logo-icono" aria-hidden="true"></span>YoguiFit</div>
        <div class="hero-divider"></div>
        <p class="hero-slogan">{{ __('Especialistas en terapia deportiva y masaje Thai Fusión') }}</p>
        <div class="hero-actions">
            <a href="{{ route('servicios') }}" class="action-card">
                <i class="bi bi-grid-3x3-gap-fill"></i>
                <span>{{ __('Servicios') }}</span>
            </a>
            <a href="{{ route('tarifas') }}" class="action-card">
                <i class="bi bi-tag-fill"></i>
                <span>{{ __('Tarifas') }}</span>
            </a>
            @auth
            <a href="{{ route('citas') }}" class="action-card">
                <i class="bi bi-clock-history"></i>
                <span>{{ __('Mis citas') }}</span>
            </a>
            @else
            <a href="{{ route('login') }}" class="action-card">
                <i class="bi bi-person-circle"></i>
                <span>{{ __('Ver mis citas') }}</span>
            </a>
            @endauth
        </div>
        <div class="hero-reservar">
            <a href="{{ route('servicios') }}" class="btn-reservar-hero">
                <i class="bi bi-calendar-check"></i> {{ __('Reservar servicio') }}
            </a>
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <div class="features-grid">
            <div class="feature-item">
                <i class="bi bi-award"></i>
                <h3>{{ __('Terapeutas certificados') }}</h3>
                <p>{{ __('Profesionales con formación especializada en masaje Thai y terapia deportiva.') }}</p>
            </div>
            <div class="feature-item">
                <i class="bi bi-heart-pulse"></i>
                <h3>{{ __('Bienestar integral') }}</h3>
                <p>{{ __('Combinamos técnicas orientales y occidentales para un tratamiento completo.') }}</p>
            </div>
            <div class="feature-item">
                <i class="bi bi-stars"></i>
                <h3>{{ __('Yoga terapéutico') }}</h3>
                <p>{{ __('Clases adaptadas a todos los niveles para mejorar tu salud y equilibrio.') }}</p>
            </div>
            <div class="feature-item">
                <i class="bi bi-mortarboard"></i>
                <h3>{{ __('Formación profesional') }}</h3>
                <p>{{ __('Cursos y talleres para quienes desean aprender y crecer en el sector.') }}</p>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>{{ __('¿Listo para empezar?') }}</h2>
        <p>{{ __('Reserva tu primera sesión y descubre cómo podemos ayudarte.') }}</p>
        <div class="cta-actions">
            <a href="{{ route('servicios') }}" class="btn-salmon">
                <i class="bi bi-calendar-plus"></i> {{ __('Reservar ahora') }}
            </a>
            <a href="{{ route('servicios') }}" class="btn-outline-yf" style="border-color:#fff;color:#fff">
                <i class="bi bi-eye"></i> {{ __('Ver servicios') }}
            </a>
        </div>
    </div>
</section>

@endsection
