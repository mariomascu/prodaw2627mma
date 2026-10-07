@extends('layouts.yoguifit')
@section('title', 'Dónde estamos — YoguiFit')
@section('content')
<div class="page-banner">
    <h1><i class="bi bi-geo-alt"></i> Dónde estamos</h1>
    <p>Masajes en la Axarquía</p>
</div>
<div class="container section" style="max-width:800px">
    <div class="card-yf" style="overflow:hidden">
        <iframe
            src="https://www.google.com/maps?q={{ urlencode('YoguiFit, C. Farmacéutico Moreno Chica, 6, 29749 Almayate, Málaga') }}&z=16&output=embed"
            title="Mapa de YoguiFit en Almayate"
            width="100%" height="380" style="border:0;display:block" allowfullscreen loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
        <div class="card-pad" style="padding-top:1.75rem;padding-bottom:1.75rem">
            <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.5rem">
                <i class="bi bi-pin-map-fill" style="color:var(--salmon);font-size:1.3rem"></i>
                <strong style="color:var(--primary-dark)">Dirección</strong>
            </div>
            <p>
                C. Farmacéutico Moreno Chica, 6<br>
                29749 Almayate, Vélez-Málaga (Málaga)<br>
                <a href="https://share.google/adrIHwqdI0FiHBOba" target="_blank" rel="noopener" style="color:var(--primary)">
                    <i class="bi bi-box-arrow-up-right"></i> Abrir en Google Maps
                </a>
            </p>
            <p style="font-size:.9rem;color:#777">
                <i class="bi bi-info-circle"></i> Aconsejamos hacer la reserva con varios días de antelación para poder ofrecer una disponibilidad horaria mayor.
            </p>
        </div>
    </div>
</div>
@endsection
