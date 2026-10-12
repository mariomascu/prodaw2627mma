{{--
    Letrero salmón que invita a subir de nivel: «Consigue un 10 % de descuento con tu nivel Bronze».
    Recibe $siguiente = ['nombre' => 'Bronze', 'descuento' => 10, …]. Si $enlazar es true lleva a la página de niveles.
--}}
@php $texto = __('Consigue un :descuento % de descuento con tu nivel :nivel', ['descuento' => $siguiente['descuento'], 'nivel' => $siguiente['nombre']]); @endphp
@if($enlazar ?? true)
    <a href="{{ route('niveles') }}" class="letrero-nivel"><i class="bi bi-award-fill"></i> {{ $texto }} <i class="bi bi-chevron-right"></i></a>
@else
    <span class="letrero-nivel"><i class="bi bi-award-fill"></i> {{ $texto }}</span>
@endif

@once
@push('styles')
<style>
    .letrero-nivel {
        display: inline-flex; align-items: center; gap: .45rem;
        background: var(--salmon); color: #fff; text-decoration: none;
        padding: .55rem 1.1rem; border-radius: 25px; font-size: .9rem; font-weight: 500;
        box-shadow: 0 4px 14px rgba(249,163,146,.35); transition: background .18s, transform .18s;
    }
    a.letrero-nivel:hover { background: #e8836f; color: #fff; transform: translateY(-1px); }
</style>
@endpush
@endonce
