@extends('layouts.yoguifit')
@section('title', 'Contacto — YoguiFit')
@section('content')
<div class="page-banner">
    <h1><i class="bi bi-telephone"></i> Contacto</h1>
    <p>Estamos aquí para atenderte</p>
</div>
<div class="container section" style="max-width:700px">
    <div class="card-yf" style="padding:2.5rem">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:2rem">
            <div style="text-align:center">
                <i class="bi bi-telephone-fill" style="font-size:2.5rem;color:var(--primary);display:block;margin-bottom:.75rem"></i>
                <h3 style="color:var(--primary-dark);margin-bottom:.4rem">Teléfono</h3>
                <a href="tel:+34600000000" style="color:var(--primary);font-size:1.1rem;text-decoration:none">+34 600 000 000</a>
            </div>
            <div style="text-align:center">
                <i class="bi bi-envelope-fill" style="font-size:2.5rem;color:var(--primary);display:block;margin-bottom:.75rem"></i>
                <h3 style="color:var(--primary-dark);margin-bottom:.4rem">Email</h3>
                <a href="mailto:info@yoguifit.com" style="color:var(--primary);font-size:1rem;text-decoration:none">info@yoguifit.com</a>
            </div>
            <div style="text-align:center">
                <i class="bi bi-instagram" style="font-size:2.5rem;color:var(--primary);display:block;margin-bottom:.75rem"></i>
                <h3 style="color:var(--primary-dark);margin-bottom:.4rem">Instagram</h3>
                <span style="color:#666">@yoguifit</span>
            </div>
        </div>
        <hr style="margin:2rem 0;border-color:var(--selected-bg)">
        <p style="color:#666;text-align:center;line-height:1.7">
            Puedes contactarnos por cualquiera de estos medios o directamente <a href="{{ route('cuestionario') }}" style="color:var(--primary)">reservando tu sesión online</a>.
            Respondemos en menos de 24 horas.
        </p>
    </div>
</div>
@endsection
