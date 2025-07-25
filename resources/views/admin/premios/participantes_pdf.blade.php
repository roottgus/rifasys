<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Participantes Premio #{{ $premio->id }}</title>
  <style>
    body { font-family: sans-serif; margin: 0; padding: 20px; }
    header { text-align: center; margin-bottom: 20px; }
    h1 { font-size: 24px; margin: 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { padding: 8px; border: 1px solid #333; text-align: left; }
    th { background-color: #f4f4f4; }
    footer { position: fixed; bottom: 20px; width: 100%; text-align: center; font-size: 12px; }
  </style>
</head>
<body>
  <header>
    <h1>Premio: {{ ucfirst($premio->tipo_premio) }}</h1>
    <p>Abono mínimo: ${{ number_format($premio->abono_minimo,2) }}</p>
    <p>Rifa: {{ $premio->rifa->nombre }} — Sorteo: {{ $premio->rifa->fecha_sorteo->format('d M Y') }}</p>
  </header>

  <table>
    <thead>
      <tr>
        <th># Ticket</th>
        <th>Cliente</th>
      </tr>
    </thead>
    <tbody>
      @forelse($data as $item)
        <tr>
          <td>{{ $item['numero'] }}</td>
          <td>{{ $item['cliente'] }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="2" style="text-align:center;">No hay participantes</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <footer>Generado el {{ now()->format('d M Y H:i') }}</footer>
</body>
</html>
