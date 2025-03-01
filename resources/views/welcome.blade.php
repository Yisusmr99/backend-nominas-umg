<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistema de Nóminas UMG</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <style>
            body {
                font-family: 'Figtree', sans-serif;
                margin: 0;
                padding: 0;
                background: #f3f4f6;
            }
            .container {
                min-height: 100vh;
                padding: 20px;
            }
            .nav {
                padding: 20px;
                text-align: right;
            }
            .nav a {
                text-decoration: none;
                color: #4b5563;
                margin-left: 15px;
                font-weight: 600;
            }
            .nav a:hover {
                color: #111827;
            }
            .main-content {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
            }
            .logo-container {
                text-align: center;
                margin-bottom: 40px;
            }
            .logo {
                height: 96px;
            }
            .grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 20px;
                margin: 40px 0;
            }
            @media (min-width: 768px) {
                .grid {
                    grid-template-columns: 1fr 1fr;
                }
            }
            .card {
                background: white;
                border-radius: 8px;
                padding: 24px;
                box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
                transition: transform 0.2s;
            }
            .card:hover {
                transform: scale(1.01);
            }
            .card h2 {
                color: #111827;
                font-size: 1.25rem;
                margin-bottom: 1rem;
            }
            .card p {
                color: #6b7280;
                font-size: 0.875rem;
                line-height: 1.5;
            }
            .footer {
                margin-top: 40px;
                padding: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                color: #6b7280;
                font-size: 0.875rem;
            }
            .footer a {
                color: #6b7280;
                text-decoration: none;
            }
            .footer a:hover {
                color: #111827;
            }
        </style>
    </head>
    <body>
        <div class="container">
            @if (Route::has('login'))
                <div class="nav">
                    @auth
                        <a href="{{ url('/dashboard') }}">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="main-content">
                <div class="logo-container">
                    <img src="{{ asset('images/umg-logo.png') }}" alt="UMG Logo" class="logo">
                </div>

                <div class="grid">
                    <div class="card">
                        <h2>Sistema de Nóminas</h2>
                        <p>
                            Bienvenido al sistema de gestión de nóminas de la Universidad Mariano Gálvez. 
                            Esta plataforma permite administrar de manera eficiente los pagos y registros de empleados.
                        </p>
                    </div>

                    <div class="card">
                        <h2>Características</h2>
                        <p>
                            • Gestión de empleados<br>
                            • Cálculo automático de salarios<br>
                            • Reportes detallados<br>
                            • Control de asistencia<br>
                            • Gestión de deducciones y bonificaciones
                        </p>
                    </div>
                </div>

                <div class="footer">
                    <div>
                        <a href="https://www.umg.edu.gt/">Universidad Mariano Gálvez de Guatemala</a>
                    </div>
                    <div>
                        Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
