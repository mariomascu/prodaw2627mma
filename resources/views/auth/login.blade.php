<x-guest-layout>
    <h2><i class="bi bi-door-open" style="color:var(--primary)"></i> Iniciar sesión</h2>

    @if(session('status'))
        <div class="session-status">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="error-msg">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label for="email"><i class="bi bi-envelope"></i> Correo electrónico</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   placeholder="Introduce tu correo electrónico" required autofocus>
        </div>
        <div class="form-group">
            <label for="password"><i class="bi bi-lock"></i> Contraseña</label>
            <input type="password" id="password" name="password"
                   placeholder="Introduce tu contraseña" required>
        </div>
        <div class="checkbox-group">
            <input type="checkbox" id="remember_me" name="remember">
            <label for="remember_me">Recuérdame</label>
        </div>
        <button type="submit" class="btn-auth">
            <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
        </button>
    </form>

    <hr class="auth-divider">
    <div class="auth-links">
        @if(Route::has('password.request'))
            <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
            <span style="color:#ccc"> · </span>
        @endif
        <a href="{{ route('register') }}">
            <i class="bi bi-person-plus"></i> Registrarse con correo electrónico
        </a>
    </div>
</x-guest-layout>
