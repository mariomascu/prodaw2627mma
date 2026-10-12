{{--
    Botón con un ojo para mostrar u ocultar la contraseña.
    Se añade solo a todos los campos type="password" de la página.
    Se incluye al final de los layouts: yoguifit, guest y app.
--}}
<style>
    .campo-contrasena { position: relative; display: block; }
    .campo-contrasena input { padding-right: 2.75rem !important; }
    .btn-ojo {
        position: absolute; top: 50%; right: .5rem; transform: translateY(-50%);
        width: 2rem; height: 2rem; padding: 0; border: none; border-radius: 6px;
        background: transparent; color: #6b8f92; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
    }
    .btn-ojo:hover, .btn-ojo:focus-visible { color: #348289; background: rgba(52,130,137,.08); outline: none; }
    .btn-ojo svg { width: 1.2rem; height: 1.2rem; }
</style>

<script>
(function () {
    const textoMostrar = @js(__('Mostrar contraseña'));
    const textoOcultar = @js(__('Ocultar contraseña'));

    // Iconos: ojo abierto (mostrar) y ojo tachado (ocultar)
    const ojo = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>';
    const ojoTachado = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 19c-6.5 0-10-7-10-7a18.5 18.5 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c6.5 0 10 7 10 7a18.5 18.5 0 0 1-2.16 3.19"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/></svg>';

    document.querySelectorAll('input[type="password"]').forEach(function (campo) {
        // Se envuelve el campo para colocar el botón dentro, a la derecha
        const envoltorio = document.createElement('span');
        envoltorio.className = 'campo-contrasena';
        campo.parentNode.insertBefore(envoltorio, campo);
        envoltorio.appendChild(campo);

        const boton = document.createElement('button');
        boton.type = 'button';
        boton.className = 'btn-ojo';
        boton.innerHTML = ojo;
        boton.setAttribute('aria-label', textoMostrar);
        boton.setAttribute('title', textoMostrar);
        boton.setAttribute('aria-pressed', 'false');
        envoltorio.appendChild(boton);

        boton.addEventListener('click', function () {
            const visible = campo.type === 'text';
            campo.type = visible ? 'password' : 'text';
            boton.innerHTML = visible ? ojo : ojoTachado;
            boton.setAttribute('aria-label', visible ? textoMostrar : textoOcultar);
            boton.setAttribute('title', visible ? textoMostrar : textoOcultar);
            boton.setAttribute('aria-pressed', visible ? 'false' : 'true');
            campo.focus();
        });
    });
})();
</script>
