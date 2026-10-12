@push('styles')
<style>
    .reserva-wrap { max-width: 720px; margin: 0 auto; padding: 3rem 1.25rem; }
    .reserva-card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(52,130,137,.12); }
    .resumen-servicio img { width: 100%; height: 220px; object-fit: cover; display: block; }
    .resumen-texto { padding: 1.5rem 1.75rem 0; }
    .resumen-tipo {
        display: inline-block; background: var(--salmon); color: #fff;
        padding: .2rem .7rem; border-radius: 12px; font-size: .75rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: .04em; margin-bottom: .6rem;
    }
    .resumen-texto h2 { color: var(--primary-dark); font-size: 1.35rem; margin-bottom: .5rem; }
    .resumen-texto p { color: #666; line-height: 1.6; font-size: .92rem; }
    .resumen-chips { display: flex; gap: .5rem; flex-wrap: wrap; margin-top: 1rem; }
    .chip-yf {
        display: inline-flex; align-items: center; gap: .3rem;
        background: var(--selected-bg); color: var(--primary-dark);
        padding: .3rem .8rem; border-radius: 20px; font-size: .82rem; font-weight: 500;
    }
    .chip-precio { background: var(--salmon); color: #fff; }
    .reserva-accion { padding: 1.5rem 1.75rem 1.75rem; border-top: 1px solid var(--selected-bg); margin-top: 1.5rem; }
    .reserva-accion h3 { color: var(--primary-dark); font-size: 1.05rem; margin-bottom: .4rem; }
    .reserva-accion > p { color: #666; font-size: .9rem; line-height: 1.6; margin-bottom: 1.25rem; }
    .reserva-botones { display: flex; gap: .75rem; flex-wrap: wrap; }
    .form-group { margin-bottom: 1.1rem; }
    .form-group label { display: block; font-size: .88rem; font-weight: 500; color: var(--primary-dark); margin-bottom: .4rem; }
    .form-group input, .form-group textarea {
        width: 100%; padding: .7rem 1rem; border: 2px solid var(--selected-bg); border-radius: 10px;
        font-family: 'Geologica', sans-serif; font-size: .95rem; color: #333; outline: none;
    }
    .form-group input:focus, .form-group textarea:focus { border-color: var(--primary); }
    .form-group .error { font-size: .8rem; color: #d44; margin-top: .25rem; }
    .volver { display: inline-flex; align-items: center; gap: .3rem; color: var(--primary); text-decoration: none; font-size: .88rem; margin-bottom: 1rem; }

    @media (max-width: 768px) {
        .reserva-wrap { padding: 2rem 1rem; }
        .resumen-servicio img { height: 180px; }
        .resumen-texto { padding: 1.25rem 1.25rem 0; }
        .reserva-accion { padding: 1.25rem; }
        .reserva-botones { flex-direction: column; }
        .reserva-botones > * { justify-content: center; }
    }
</style>
@endpush
