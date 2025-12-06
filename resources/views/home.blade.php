<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiomedHub</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>
    <div class="page">
        <header class="nav">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="BiomedHub">
                <span>BiomedHub</span>
            </div>
            <div>
                <a class="btn btn-primary" href="{{ url('/admin') }}">Ir al panel</a>
            </div>
        </header>

        <section class="hero">
            <div class="hero-card">
                <div class="pill">Gestión quirúrgica y logística</div>
                <h1>Control de cirugías, consumos y equipos en un solo lugar</h1>
                <p>
                    Seguimiento de cirugías, pedidos y movimientos en tiempo real.
                    Alertas por stock bajo, reportes de instrumentistas y trazabilidad completa.
                </p>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <a class="btn btn-primary" href="{{ url('/admin') }}">Entrar al panel</a>
                    <a class="btn" style="border:1px solid rgba(53,147,96,.4); color:#e6f1f5;" href="#features">Ver características</a>
                </div>
            </div>
            <div class="hero-card">
                <h3>Highlights</h3>
                <ul style="margin:0; padding-left:18px; color:#cde3ec; line-height:1.6;">
                    <li>Reportes de cirugía con evidencia y cierre automático</li>
                    <li>Pedidos y movimientos con seguimiento en Filament</li>
                    <li>Stock crítico y alertas operativas en el dashboard</li>
                    <li>Diseño alineado con la paleta del logo</li>
                </ul>
            </div>
        </section>

        <section id="features" class="features">
            <div class="feature">
                <h3>Cirugías</h3>
                <p>Agenda, estados, instrumentista y reportes finales con evidencia fotográfica.</p>
            </div>
            <div class="feature">
                <h3>Pedidos</h3>
                <p>Prioridades, entregas programadas y estados de logística en un clic.</p>
            </div>
            <div class="feature">
                <h3>Movimientos</h3>
                <p>Traslados de equipos con fechas de salida/retorno y alertas si se exceden.</p>
            </div>
            <div class="feature">
                <h3>Inventario</h3>
                <p>Disponibilidad, reservas y detección automática de stock bajo.</p>
            </div>
        </section>

        <footer class="footer">
            © {{ date('Y') }} BiomedHub — Gestión inteligente para quirófanos y logística.
        </footer>
    </div>
</body>
</html>
