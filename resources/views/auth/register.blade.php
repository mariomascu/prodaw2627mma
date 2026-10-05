<x-guest-layout>
    <h2><i class="bi bi-person-plus" style="color:var(--primary)"></i> Crear cuenta</h2>

    @if($errors->any())
        <div class="error-msg">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-group">
            <label for="name"><i class="bi bi-person"></i> Nombre</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
                   placeholder="Tu nombre completo" required autofocus>
        </div>
        <div class="form-group">
            <label for="email"><i class="bi bi-envelope"></i> Correo electrónico</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   placeholder="Introduce tu dirección de correo" required>
        </div>
        <div class="form-group">
            <label for="password"><i class="bi bi-lock"></i> Contraseña</label>
            <input type="password" id="password" name="password"
                   placeholder="Mínimo 8 caracteres" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation"><i class="bi bi-lock-fill"></i> Confirmar contraseña</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   placeholder="Repite la contraseña" required>
        </div>
        <button type="submit" class="btn-auth">
            <i class="bi bi-person-check"></i> Registrarse
        </button>
    </form>

    <hr class="auth-divider">
    <div class="auth-links">
        ¿Ya tienes cuenta? <a href="{{ route('login') }}">Iniciar sesión</a>
    </div>
</x-guest-layout>
