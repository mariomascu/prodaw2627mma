<x-guest-layout>
    <h2><i class="bi bi-key" style="color:var(--primary)"></i> Recuperar contraseña</h2>

    <p style="font-size:.88rem;color:#666;margin-bottom:1.2rem;line-height:1.6">
        Introduce tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
    </p>

    @if(session('status'))
        <div class="session-status">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="error-msg">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group">
            <label for="email"><i class="bi bi-envelope"></i> Correo electrónico</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   placeholder="Tu dirección de correo" required autofocus>
        </div>
        <button type="submit" class="btn-auth">
            <i class="bi bi-send"></i> Enviar enlace de recuperación
        </button>
    </form>

    <hr class="auth-divider">
    <div class="auth-links">
        <a href="{{ route('login') }}"><i class="bi bi-arrow-left"></i> Volver al inicio de sesión</a>
    </div>
</x-guest-layout>
