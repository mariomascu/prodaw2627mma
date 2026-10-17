<x-guest-layout>
    <h2><i class="bi bi-door-open" style="color:var(--primary)"></i> {{ __('Iniciar sesión') }}</h2>

    @if(session('status'))
        <div class="session-status">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="error-msg">{{ $errors->first() }}</div>
    @endif

    @include('auth._boton-google')

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label for="email"><i class="bi bi-envelope"></i> {{ __('Correo electrónico o usuario') }}</label>
            <input type="text" id="email" name="email" value="{{ old('email') }}"
                   placeholder="{{ __('Introduce tu correo electrónico') }}" required autocomplete="username">
        </div>
        <div class="form-group">
            <label for="password"><i class="bi bi-lock"></i> {{ __('Contraseña') }}</label>
            <input type="password" id="password" name="password"
                   placeholder="{{ __('Introduce tu contraseña') }}" required>
        </div>
        <div class="checkbox-group">
            <input type="checkbox" id="remember_me" name="remember">
            <label for="remember_me">{{ __('Recuérdame') }}</label>
        </div>
        <button type="submit" class="btn-auth">
            <i class="bi bi-box-arrow-in-right"></i> {{ __('Iniciar sesión') }}
        </button>
    </form>

    <hr class="auth-divider">
    <div class="auth-links">
        @if(Route::has('password.request'))
            <a href="{{ route('password.request') }}">{{ __('¿Olvidaste tu contraseña?') }}</a>
            <span style="color:#ccc"> · </span>
        @endif
        <a href="{{ route('register') }}">
            <i class="bi bi-person-plus"></i> {{ __('Registrarse con correo electrónico') }}
        </a>
    </div>
</x-guest-layout>
