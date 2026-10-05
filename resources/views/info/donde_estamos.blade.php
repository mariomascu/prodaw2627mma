@extends('layouts.yoguifit')
@section('title', 'Dónde estamos — YoguiFit')
@section('content')
<div class="page-banner">
    <h1><i class="bi bi-geo-alt"></i> Dónde estamos</h1>
    <p>Encuéntranos fácilmente</p>
</div>
<div class="container section" style="max-width:800px">
    <div class="card-yf" style="overflow:hidden">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3037.7680316024!2d-3.7037902!3d40.4167754!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd4228ee68c1328f%3A0x2e0f97e89a69c9e!2sMadrid%2C%20Spain!5e0!3m2!1sen!2ses!4v1680000000000!5m2!1sen!2ses"
            width="100%" height="380" style="border:0;display:block" allowfullscreen loading="lazy">
        </iframe>
        <div style="padding:1.75rem">
            <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.5rem">
                <i class="bi bi-pin-map-fill" style="color:var(--salmon);font-size:1.3rem"></i>
                <strong style="color:var(--primary-dark)">Dirección</strong>
            </div>
            <p style="color:#666;line-height:1.6">
                Calle Ejemplo 123, 28001 Madrid<br>
                <a href="https://maps.app.goo.gl/eVhwLpu6r669m7rv6" target="_blank" style="color:var(--primary)">
                    <i class="bi bi-box-arrow-up-right"></i> Abrir en Google Maps
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
