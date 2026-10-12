{{-- Datos del centro que se usan en los textos legales (Panel > Configuración > Datos del centro) --}}
@php
    $centroNombre    = \App\Models\Ajuste::valor('centro_nombre');
    $centroDireccion = \App\Models\Ajuste::valor('centro_direccion');
    $centroTelefono  = \App\Models\Ajuste::valor('centro_telefono');
    $centroEmail     = \App\Models\Ajuste::valor('centro_email');
    $enlaceEmail     = '<a href="mailto:' . e($centroEmail) . '" style="color:var(--primary)">' . e($centroEmail) . '</a>';
    $enlaceTelefono  = '<a href="tel:' . preg_replace('/[^0-9+]/', '', $centroTelefono) . '" style="color:var(--primary)">' . e($centroTelefono) . '</a>';
@endphp
