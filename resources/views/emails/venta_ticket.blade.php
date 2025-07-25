{{-- resources/views/emails/venta_ticket.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Tu Ticket de Rifa – {{ config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f6f8fb;font-family:Arial,sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f8fb;padding:40px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="430" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.05);">
          {{-- Header --}}
          <tr>
            <td style="background:#2563eb;padding:20px;text-align:center;">
              <img src="{{ $logoCid }}" alt="Logo" style="max-width:200px;">


              <h1 style="margin:0;color:#ffffff;font-size:24px;">¡Tu Ticket Está Listo!</h1>
            </td>
          </tr>

          {{-- Body --}}
          <tr>
            <td style="padding:30px;">
              <p style="margin:0 0 16px;font-size:16px;color:#374151;">
                Hola <strong>{{ $cliente->nombre }}</strong>, gracias por tu compra. Aquí tienes los detalles de tu ticket:
              </p>

              <h2 style="font-size:20px;margin:0 0 16px;color:#1e3a8a;text-align:center;">
                Ticket #{{ str_pad($ticket->numero, 3, '0', STR_PAD_LEFT) }}
              </h2>

              <div style="text-align:center;margin-bottom:24px;">
                <img src="{{ $ticket->qr_code }}" alt="Código QR" width="180" height="180" style="display:block;margin:0 auto;border-radius:8px;">
                <p style="margin:12px 0 0 0;font-size:14px;color:#6b7280;">Escanea para verificar tu ticket</p>
              </div>

              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:15px;color:#374151;">
                <tr>
                  <td width="30%" style="padding:4px 0;"><strong>Rifa:</strong></td>
                  <td style="padding:4px 0;">{{ $ticket->rifa->nombre }}</td>
                </tr>
                <tr>
                  <td style="padding:4px 0;"><strong>Estado:</strong></td>
                  <td style="padding:4px 0;color:{{ $ticket->estado==='vendido'?'#059669':($ticket->estado==='abonado'?'#eab308':'#6366f1') }};">
                    {{ strtoupper($ticket->estado) }}
                  </td>
                </tr>
              </table>

              <div style="text-align:center;margin:28px 0;">
                <a href="{{ url('tickets/verificar/'.$ticket->uuid) }}"
                   style="background:#2563eb;color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:8px;display:inline-block;font-weight:600;font-size:15px;">
                  Verificar mi ticket
                </a>
              </div>
            </td>
          </tr>

          {{-- Footer --}}
          <tr>
            <td style="background:#f3f4f6;padding:16px;text-align:center;font-size:12px;color:#9ca3af;">
              {{ config('app.name') }} – Sistemas de Rifas<br>
              ¿Necesitas ayuda? Contáctanos en soporte@{{ parse_url(config('app.url'), PHP_URL_HOST) }}
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
