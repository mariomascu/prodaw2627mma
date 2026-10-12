{{--
    Modal con el calendario de horas libres. Se abre con cualquier botón que tenga data-reservar:
      data-titulo         → nombre del servicio
      data-precio         → duración y precio que se ven en la cabecera
      data-solo-precio    → precio (ya con el descuento del cliente) para el resumen de confirmación
      data-disponibilidad → URL que devuelve las horas libres en JSON
      data-accion         → URL a la que se envía el formulario
      data-modo           → "reservar" (pide teléfono y comentarios) o "cambiar" (solo la hora nueva)
--}}
@php $telefonoUsuario = auth()->user()?->telefono; @endphp

@push('styles')
<style>
    dialog.modal-reserva {
        margin: auto; /* el reset "* { margin: 0 }" quitaría el centrado del <dialog> */
        border: none; border-radius: 16px; padding: 0; width: min(560px, calc(100% - 2rem));
        max-height: calc(100vh - 2rem); box-shadow: 0 20px 60px rgba(0,0,0,.3); color: var(--primary-dark);
    }
    dialog.modal-reserva::backdrop { background: rgba(10,30,32,.55); }
    .modal-cabecera {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary)); color: #fff;
        padding: 1.1rem 1.25rem; display: flex; align-items: flex-start; gap: .75rem;
    }
    .modal-cabecera h2 { font-size: 1.1rem; margin-bottom: .2rem; }
    .modal-cabecera p { font-size: .85rem; opacity: .9; }
    .modal-cerrar { margin-left: auto; background: none; border: none; color: #fff; font-size: 1.3rem; cursor: pointer; }
    .modal-cuerpo { padding: 1.25rem; overflow-y: auto; }

    .cal-cabecera { display: flex; align-items: center; justify-content: space-between; margin-bottom: .75rem; }
    .cal-cabecera strong { font-size: 1rem; }
    .cal-cabecera strong::first-letter { text-transform: uppercase; }
    .cal-flecha { background: var(--selected-bg); border: none; border-radius: 50%; width: 2rem; height: 2rem; cursor: pointer; color: var(--primary-dark); }
    .cal-flecha:disabled { opacity: .35; cursor: default; }
    .cal-rejilla { display: grid; grid-template-columns: repeat(7, 1fr); gap: .3rem; }
    .cal-dia-semana { text-align: center; font-size: .72rem; color: #999; text-transform: uppercase; padding-bottom: .2rem; }
    .cal-dia {
        height: 2.6rem; border: none; border-radius: 8px; font-family: 'Geologica', sans-serif; font-size: .9rem;
        background: transparent; color: #c5cfcf; cursor: default;
    }
    .cal-dia.disponible { background: var(--selected-bg); color: var(--primary-dark); cursor: pointer; font-weight: 500; }
    .cal-dia.disponible:hover { background: var(--terciario); }
    .cal-dia.elegido { background: var(--primary); color: #fff; }

    .cal-horas-titulo { margin: 1.1rem 0 .5rem; font-size: .88rem; font-weight: 500; }
    .cal-horas { display: grid; grid-template-columns: repeat(auto-fill, minmax(72px, 1fr)); gap: .4rem; }
    .cal-hora {
        padding: .5rem 0; border: 2px solid var(--selected-bg); border-radius: 8px; background: #fff;
        font-family: 'Geologica', sans-serif; font-size: .9rem; color: var(--primary-dark); cursor: pointer;
    }
    .cal-hora:hover { border-color: var(--primary); }
    .cal-hora.elegida { background: var(--primary); border-color: var(--primary); color: #fff; }
    .cal-aviso { color: #888; font-size: .88rem; text-align: center; padding: 1rem 0; }
    .cal-resumen { margin-top: 1rem; background: var(--selected-bg); border-radius: 10px; padding: .75rem 1rem; font-size: .9rem; }
    .cal-resumen::first-letter { text-transform: uppercase; }

    .modal-reserva .form-group { margin-top: 1rem; }
    .modal-reserva .form-group label { display: block; font-size: .85rem; font-weight: 500; margin-bottom: .3rem; }
    .modal-reserva .form-group input, .modal-reserva .form-group textarea {
        width: 100%; padding: .6rem .9rem; border: 2px solid var(--selected-bg); border-radius: 10px;
        font-family: 'Geologica', sans-serif; font-size: .92rem; outline: none;
    }
    .modal-reserva .form-group input:focus, .modal-reserva .form-group textarea:focus { border-color: var(--primary); }
    .modal-pie { display: flex; gap: .75rem; justify-content: flex-end; margin-top: 1.25rem; flex-wrap: wrap; }
    .modal-pie button:disabled { opacity: .45; cursor: not-allowed; }

    /* Paso de confirmación */
    .confirmar-icono { text-align: center; font-size: 2.6rem; color: var(--primary); }
    .confirmar-titulo { text-align: center; font-size: 1.15rem; margin: .25rem 0 1rem; }
    .confirmar-datos { display: grid; grid-template-columns: auto 1fr; gap: .5rem 1rem; background: var(--selected-bg); border-radius: 12px; padding: 1rem 1.25rem; font-size: .92rem; }
    .confirmar-datos dt { color: #6b8f92; }
    .confirmar-datos dd { font-weight: 600; }
    .confirmar-datos dd::first-letter { text-transform: uppercase; }
    .confirmar-nota { font-size: .82rem; color: #888; text-align: center; margin-top: .9rem; }
</style>
@endpush

<dialog class="modal-reserva" id="modalReserva">
    <form method="POST" action="" id="formReserva">
        @csrf
        <input type="hidden" name="inicio" id="campoInicio">

        <div class="modal-cabecera">
            <div>
                <h2 id="modalTitulo"></h2>
                <p id="modalSubtitulo"></p>
            </div>
            <button type="button" class="modal-cerrar" id="modalCerrar" aria-label="{{ __('Cerrar') }}"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="modal-cuerpo">
          {{-- ── Paso 1: elegir día y hora ── --}}
          <div id="pasoElegir">
            <div class="cal-cabecera">
                <button type="button" class="cal-flecha" id="calAnterior" aria-label="{{ __('Mes anterior') }}"><i class="bi bi-chevron-left"></i></button>
                <strong id="calMes"></strong>
                <button type="button" class="cal-flecha" id="calSiguiente" aria-label="{{ __('Mes siguiente') }}"><i class="bi bi-chevron-right"></i></button>
            </div>
            <div class="cal-rejilla" id="calRejilla"></div>

            <div id="calHorasBloque" hidden>
                <div class="cal-horas-titulo">{{ __('Horas disponibles') }}</div>
                <div class="cal-horas" id="calHoras"></div>
            </div>
            <p class="cal-aviso" id="calAviso"></p>
            <div class="cal-resumen" id="calResumen" hidden></div>

            {{-- Solo al reservar (al pedir un cambio no hacen falta) --}}
            <div id="camposReserva">
                <div class="form-group">
                    <label for="modalTelefono"><i class="bi bi-telephone"></i> {{ __('Teléfono de contacto') }}</label>
                    <input type="tel" id="modalTelefono" name="telefono" value="{{ old('telefono', $telefonoUsuario) }}" maxlength="20">
                </div>
                <div class="form-group">
                    <label for="modalNotas"><i class="bi bi-chat-left-text"></i> {{ __('Comentarios (opcional)') }}</label>
                    <textarea id="modalNotas" name="notas" rows="2" maxlength="500" placeholder="{{ __('Lesiones o molestias que debamos tener en cuenta…') }}">{{ old('notas') }}</textarea>
                </div>
            </div>

            <div class="modal-pie">
                <button type="button" class="btn-outline-yf" id="modalCancelar">{{ __('Cancelar') }}</button>
                <button type="button" class="btn-salmon" id="modalContinuar" disabled>{{ __('Continuar') }} <i class="bi bi-arrow-right"></i></button>
            </div>
          </div>

          {{-- ── Paso 2: confirmar la cita ── --}}
          <div id="pasoConfirmar" hidden>
            <div class="confirmar-icono"><i class="bi bi-calendar2-check"></i></div>
            <h3 class="confirmar-titulo" id="confirmarPregunta"></h3>
            <dl class="confirmar-datos">
                <dt>{{ __('Servicio') }}</dt><dd id="confirmarServicio"></dd>
                <dt>{{ __('Día') }}</dt><dd id="confirmarDia"></dd>
                <dt>{{ __('Hora') }}</dt><dd id="confirmarHora"></dd>
                <dt class="solo-reserva">{{ __('Precio') }}</dt><dd class="solo-reserva" id="confirmarPrecio"></dd>
                <dt class="solo-reserva">{{ __('Teléfono') }}</dt><dd class="solo-reserva" id="confirmarTelefono"></dd>
            </dl>
            <p class="confirmar-nota" id="confirmarNota"></p>
            <div class="modal-pie">
                <button type="button" class="btn-outline-yf" id="modalVolver"><i class="bi bi-arrow-left"></i> {{ __('Volver') }}</button>
                <button type="submit" class="btn-salmon" id="modalEnviar"><i class="bi bi-check-lg"></i> <span id="modalEnviarTexto"></span></button>
            </div>
          </div>
        </div>
    </form>
</dialog>

@push('scripts')
<script>
(function () {
    const modal = document.getElementById('modalReserva');
    const idioma = document.documentElement.lang || 'es';
    const textos = {
        reservar: @js(__('Confirmar cita')),
        cambiar: @js(__('Solicitar el cambio')),
        preguntaReservar: @js(__('¿Confirmas tu cita?')),
        preguntaCambiar: @js(__('¿Confirmas el cambio de cita?')),
        notaReservar: @js(__('Al confirmar, la cita queda reservada y aparecerá en «Mis citas».')),
        notaCambiar: @js(__('El cambio quedará pendiente hasta que lo apruebe el centro.')),
        faltaTelefono: @js(__('Indica un teléfono de contacto.')),
        cargando: @js(__('Cargando horas disponibles…')),
        sinHoras: @js(__('Ahora mismo no hay horas disponibles. Vuelve a mirar pronto.')),
        eligeDia: @js(__('Elige un día marcado en el calendario.')),
        error: @js(__('No se han podido cargar las horas. Inténtalo de nuevo.')),
    };

    let horasLibres = {};   // { '2026-11-03': ['10:00', '16:00'], ... }
    let mesVisible = null;  // primer día del mes que se está viendo
    let diaElegido = null;
    let modo = 'reservar';
    let datosServicio = {};

    // ── Abrir el modal desde cualquier botón con data-reservar ──
    document.querySelectorAll('[data-reservar]').forEach(boton => {
        boton.addEventListener('click', () => abrir(boton.dataset));
    });

    function abrir(datos) {
        modo = datos.modo === 'cambiar' ? 'cambiar' : 'reservar';
        datosServicio = datos;
        mostrarPaso('elegir');
        document.getElementById('formReserva').action = datos.accion;
        document.getElementById('modalTitulo').textContent = datos.titulo;
        document.getElementById('modalSubtitulo').textContent = datos.precio || '';
        document.getElementById('camposReserva').hidden = datos.modo === 'cambiar';
        document.getElementById('modalTelefono').required = datos.modo !== 'cambiar';
        document.getElementById('modalEnviarTexto').textContent = datos.modo === 'cambiar' ? textos.cambiar : textos.reservar;

        reiniciar();
        aviso(textos.cargando);
        modal.showModal();

        fetch(datos.disponibilidad, { headers: { 'Accept': 'application/json' } })
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(json => {
                horasLibres = Array.isArray(json) ? {} : json;
                const fechas = Object.keys(horasLibres).sort();
                if (fechas.length === 0) {
                    aviso(textos.sinHoras);
                    mesVisible = primerDiaDelMes(new Date());
                } else {
                    aviso(textos.eligeDia);
                    mesVisible = primerDiaDelMes(new Date(fechas[0] + 'T00:00'));
                }
                pintarMes();
            })
            .catch(() => aviso(textos.error));
    }

    function reiniciar() {
        horasLibres = {};
        diaElegido = null;
        document.getElementById('campoInicio').value = '';
        document.getElementById('modalContinuar').disabled = true;
        document.getElementById('calHorasBloque').hidden = true;
        document.getElementById('calResumen').hidden = true;
        document.getElementById('calRejilla').innerHTML = '';
        document.getElementById('calMes').textContent = '';
    }

    // ── Calendario del mes ──
    function pintarMes() {
        const rejilla = document.getElementById('calRejilla');
        rejilla.innerHTML = '';

        document.getElementById('calMes').textContent =
            mesVisible.toLocaleDateString(idioma, { month: 'long', year: 'numeric' });

        // Cabecera con los días de la semana (empezando en lunes)
        for (let i = 0; i < 7; i++) {
            const dia = new Date(2024, 0, 1 + i); // el 1 de enero de 2024 fue lunes
            const celda = document.createElement('div');
            celda.className = 'cal-dia-semana';
            celda.textContent = dia.toLocaleDateString(idioma, { weekday: 'short' });
            rejilla.appendChild(celda);
        }

        // Huecos antes del día 1
        const huecos = (mesVisible.getDay() + 6) % 7;
        for (let i = 0; i < huecos; i++) rejilla.appendChild(document.createElement('div'));

        const diasDelMes = new Date(mesVisible.getFullYear(), mesVisible.getMonth() + 1, 0).getDate();
        for (let d = 1; d <= diasDelMes; d++) {
            const fecha = claveFecha(new Date(mesVisible.getFullYear(), mesVisible.getMonth(), d));
            const boton = document.createElement('button');
            boton.type = 'button';
            boton.className = 'cal-dia';
            boton.textContent = d;

            if (horasLibres[fecha]) {
                boton.classList.add('disponible');
                if (fecha === diaElegido) boton.classList.add('elegido');
                boton.addEventListener('click', () => elegirDia(fecha));
            } else {
                boton.disabled = true;
            }
            rejilla.appendChild(boton);
        }

        // Solo se puede navegar entre meses que tengan horas libres
        const fechas = Object.keys(horasLibres).sort();
        const mesClave = claveFecha(mesVisible).slice(0, 7);
        document.getElementById('calAnterior').disabled = !fechas.some(f => f.slice(0, 7) < mesClave);
        document.getElementById('calSiguiente').disabled = !fechas.some(f => f.slice(0, 7) > mesClave);
    }

    function elegirDia(fecha) {
        diaElegido = fecha;
        document.getElementById('campoInicio').value = '';
        document.getElementById('modalContinuar').disabled = true;
        document.getElementById('calResumen').hidden = true;
        aviso('');
        pintarMes();

        const contenedor = document.getElementById('calHoras');
        contenedor.innerHTML = '';
        horasLibres[fecha].forEach(hora => {
            const boton = document.createElement('button');
            boton.type = 'button';
            boton.className = 'cal-hora';
            boton.textContent = hora;
            boton.addEventListener('click', () => elegirHora(boton, fecha, hora));
            contenedor.appendChild(boton);
        });
        document.getElementById('calHorasBloque').hidden = false;
    }

    function elegirHora(boton, fecha, hora) {
        document.querySelectorAll('.cal-hora').forEach(b => b.classList.remove('elegida'));
        boton.classList.add('elegida');

        document.getElementById('campoInicio').value = fecha + ' ' + hora;
        document.getElementById('modalContinuar').disabled = false;

        const resumen = document.getElementById('calResumen');
        const dia = new Date(fecha + 'T' + hora).toLocaleDateString(idioma, { weekday: 'long', day: 'numeric', month: 'long' });
        resumen.innerHTML = '<i class="bi bi-calendar-check"></i> ' + dia + ' · <strong>' + hora + '</strong>';
        resumen.hidden = false;
    }

    // ── Paso de confirmación ──
    function mostrarPaso(paso) {
        document.getElementById('pasoElegir').hidden = paso !== 'elegir';
        document.getElementById('pasoConfirmar').hidden = paso !== 'confirmar';
    }

    document.getElementById('modalContinuar').addEventListener('click', () => {
        const valor = document.getElementById('campoInicio').value;          // "2026-11-03 10:00"
        if (!valor) return;

        const telefono = document.getElementById('modalTelefono');
        if (modo === 'reservar' && telefono.value.trim() === '') {
            aviso(textos.faltaTelefono);
            telefono.focus();
            return;
        }

        const [fecha, hora] = valor.split(' ');
        document.getElementById('confirmarPregunta').textContent = modo === 'cambiar' ? textos.preguntaCambiar : textos.preguntaReservar;
        document.getElementById('confirmarServicio').textContent = datosServicio.titulo;
        document.getElementById('confirmarDia').textContent =
            new Date(fecha + 'T' + hora).toLocaleDateString(idioma, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('confirmarHora').textContent = hora;
        document.getElementById('confirmarPrecio').textContent = datosServicio.soloPrecio || datosServicio.precio || '';
        document.getElementById('confirmarTelefono').textContent = telefono.value;
        document.querySelectorAll('#pasoConfirmar .solo-reserva').forEach(el => el.hidden = modo === 'cambiar');
        document.getElementById('confirmarNota').textContent = modo === 'cambiar' ? textos.notaCambiar : textos.notaReservar;

        mostrarPaso('confirmar');
    });

    document.getElementById('modalVolver').addEventListener('click', () => mostrarPaso('elegir'));

    // ── Navegación y cierre ──
    document.getElementById('calAnterior').addEventListener('click', () => { mesVisible.setMonth(mesVisible.getMonth() - 1); pintarMes(); });
    document.getElementById('calSiguiente').addEventListener('click', () => { mesVisible.setMonth(mesVisible.getMonth() + 1); pintarMes(); });
    document.getElementById('modalCerrar').addEventListener('click', () => modal.close());
    document.getElementById('modalCancelar').addEventListener('click', () => modal.close());

    // ── Utilidades ──
    function aviso(texto) {
        const p = document.getElementById('calAviso');
        p.textContent = texto;
        p.hidden = texto === '';
    }
    function primerDiaDelMes(fecha) { return new Date(fecha.getFullYear(), fecha.getMonth(), 1); }
    function claveFecha(fecha) {
        return fecha.getFullYear() + '-' + String(fecha.getMonth() + 1).padStart(2, '0') + '-' + String(fecha.getDate()).padStart(2, '0');
    }

    // Si la página pide abrirlo al cargar (p. ej. al volver del registro)
    const autoabrir = document.querySelector('[data-reservar][data-autoabrir]');
    if (autoabrir) abrir(autoabrir.dataset);
})();
</script>
@endpush
