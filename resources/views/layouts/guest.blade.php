<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'YoguiFit') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montez&family=Geologica:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #348289; --primary2: #58999D; --primary-dark: #123437;
            --terciario: #92CBC5; --secondary: #8ED4CC; --salmon: #F9A392; --selected-bg: #D7E5E3;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Geologica', sans-serif;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 55%, var(--secondary) 100%);
            min-height: 100vh;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 2rem 1rem;
        }
        .auth-logo {
            font-family: 'Montez', cursive; font-size: 3rem; color: #fff;
            margin-bottom: 1.5rem; text-align: center;
        }
        .auth-logo span { color: var(--salmon); }
        .auth-card {
            background: #fff; border-radius: 18px;
            padding: 2.5rem 2rem; width: 100%; max-width: 420px;
            box-shadow: 0 8px 40px rgba(0,0,0,.25);
        }
        .auth-card h2 {
            font-size: 1.1rem; font-weight: 600;
            color: var(--primary-dark); margin-bottom: 1.5rem; text-align: center;
        }
        .form-group { margin-bottom: 1.1rem; }
        .form-group label { display: block; font-size: .83rem; font-weight: 500; color: var(--primary-dark); margin-bottom: .35rem; }
        .form-group input[type=text],
        .form-group input[type=email],
        .form-group input[type=password] {
            width: 100%; padding: .7rem 1rem; border: 2px solid var(--selected-bg);
            border-radius: 10px; font-family: 'Geologica', sans-serif; font-size: .9rem; outline: none;
            transition: border-color .2s; color: #333;
        }
        .form-group input:focus { border-color: var(--primary); }
        .form-errors { font-size: .78rem; color: #d44; margin-top: .25rem; }
        .btn-auth {
            width: 100%; padding: .75rem; border-radius: 25px;
            background: var(--primary); color: #fff; border: none;
            font-family: 'Geologica', sans-serif; font-size: .95rem; font-weight: 600;
            cursor: pointer; transition: background .18s; margin-top: .5rem;
        }
        .btn-auth:hover { background: var(--primary2); }
        .auth-links { text-align: center; margin-top: 1.25rem; font-size: .85rem; }
        .auth-links a { color: var(--primary); text-decoration: none; }
        .auth-links a:hover { color: var(--primary2); }
        .auth-divider { border: none; border-top: 1px solid var(--selected-bg); margin: 1.2rem 0; }
        .session-status { background: var(--selected-bg); border-radius: 8px; padding: .65rem 1rem; font-size: .85rem; color: var(--primary-dark); margin-bottom: 1rem; }
        .error-msg { background: #fde8e8; border-radius: 8px; padding: .65rem 1rem; font-size: .82rem; color: #700; margin-bottom: 1rem; }
        .checkbox-group { display: flex; align-items: center; gap: .5rem; margin: .5rem 0; }
        .checkbox-group input { width: auto; accent-color: var(--primary); }
        .checkbox-group label { font-size: .85rem; color: #666; cursor: pointer; }
    </style>
</head>
<body>
    <a href="/" class="auth-logo">Yogui<span>Fit</span></a>
    <div class="auth-card">
        {{ $slot }}
    </div>
    <div style="color:rgba(255,255,255,.6);font-size:.78rem;margin-top:1.5rem">
        © {{ date('Y') }} YoguiFit · Especialistas en terapia deportiva
    </div>
</body>
</html>
