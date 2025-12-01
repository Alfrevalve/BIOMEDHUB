<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Digest Operativo</title>
</head>
<body style="font-family: Arial, sans-serif; color:#111;">
  <h2>🧠 BIOMED HUB 2.0 – Digest Diario</h2>
  <p><strong>Fecha:</strong> {{ $fecha }}</p>

  <h3>🏥 Cirugías de mañana ({{ $totalCir }})</h3>
  @if($totalCir === 0)
    <p>Sin cirugías programadas para mañana.</p>
  @else
    <ul>
      @foreach($cirugias as $c)
        <li>
          <strong>{{ $c->nombre }}</strong><br>
          📍 {{ $c->institucion->nombre ?? 'Sin institución' }}
          | ⏰ {{ \Carbon\Carbon::parse($c->fecha_programada)->timezone('America/Lima')->format('d/m/Y H:i') }}
          | 👤 {{ $c->instrumentista_asignado ?? 'Sin asignar' }}
        </li>
      @endforeach
    </ul>
  @endif

  <h3>📦 Pedidos para entregar mañana ({{ $totalPed }})</h3>
  @if($totalPed === 0)
    <p>No hay pedidos pendientes para mañana.</p>
  @else
    <ul>
      @foreach($pedidos as $p)
        <li>
          <strong>{{ $p->codigo_pedido }}</strong> — {{ $p->estado }} →
          Entrega: {{ optional($p->fecha_entrega)->timezone('America/Lima')->format('d/m/Y H:i') ?? '—' }}
          @if(!empty($p->entrega_a)) | 📍 {{ $p->entrega_a }} @endif
        </li>
      @endforeach
    </ul>
  @endif

  <h3>⚠️ Pedidos atrasados ({{ $totalAtr }})</h3>
  @if($totalAtr === 0)
    <p>Sin atrasos (¡bien!).</p>
  @else
    <ul>
      @foreach($atrasados as $a)
        <li>
          <strong>{{ $a->codigo_pedido }}</strong> — {{ $a->estado }} →
          Debió entregarse: {{ optional($a->fecha_entrega)->timezone('America/Lima')->format('d/m/Y H:i') ?? '—' }}
        </li>
      @endforeach
    </ul>
  @endif

  <hr>
  <p>
    <a href="http://biomedhub.test/admin" style="color:#0b5ed7; text-decoration:none;">Abrir Panel Admin</a>
  </p>
</body>
</html>
