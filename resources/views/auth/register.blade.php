<x-guest-layout>
    <h2><i class="bi bi-person-plus" style="color:var(--primary)"></i> {{ __('Crear cuenta') }}</h2>

    @if($errors->any())
        <div class="error-msg">{{ $errors->first() }}</div>
    @endif

    @include('auth._boton-google')

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-group">
            <label for="name"><i class="bi bi-person"></i> {{ __('Nombre') }}</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
                   placeholder="{{ __('Tu nombre completo') }}" required autofocus>
        </div>
        <div class="form-group">
            <label for="email"><i class="bi bi-envelope"></i> {{ __('Correo electrónico') }}</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   placeholder="{{ __('Introduce tu dirección de correo') }}" required>
        </div>
        <div class="form-group">
            <label for="password"><i class="bi bi-lock"></i> {{ __('Contraseña') }}</label>
            <input type="password" id="password" name="password"
                   placeholder="{{ __('Mínimo 8 caracteres') }}" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation"><i class="bi bi-lock-fill"></i> {{ __('Confirmar contraseña') }}</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   placeholder="{{ __('Repite la contraseña') }}" required>
        </div>
        <button type="submit" class="btn-auth">
            <i class="bi bi-person-check"></i> {{ __('Registrarse') }}
        </button>
    </form>

    <hr class="auth-divider">
    <div class="auth-links">
        {{ __('¿Ya tienes cuenta?') }} <a href="{{ route('login') }}">{{ __('Iniciar sesión') }}</a>
    </div>
</x-guest-layout>
