<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Rifa</title>
    <style>
        @page { margin: 24px; }
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: #f7fafc;
            color: #222;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(44, 62, 80, 0.15);
            padding: 28px 44px 28px 44px;
            background: #fff;
            max-width: 700px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }
        .estado-label {
            display: inline-block;
            padding: 8px 24px;
            border-radius: 999px;
            font-weight: bold;
            color: #fff;
            font-size: 1.15em;
            letter-spacing: 2px;
            margin-bottom: 18px;
        }
        .vendido { background: #4755E9; }
        .abonado { background: #AA39DD; }
        .reservado { background: #F19111; }
        .other    { background: #9CA3AF; }
        .title {
            font-size: 2.3em;
            font-weight: 800;
            margin-bottom: 12px;
            color: #2d2f55;
            letter-spacing: 1.5px;
        }
        .info-list { margin-bottom: 24px; }
        .info-list p {
            font-size: 1.07em;
            margin: 4px 0;
        }
        .qr-box {
            border: 5px solid #9ad6d2;
            border-radius: 16px;
            display: inline-block;
            margin: 14px 0 18px;
            padding: 14px;
        }
        .abono-info {
            background: #F6F8FB;
            border-left: 7px solid #AA39DD;
            padding: 12px 24px;
            margin: 18px 0 16px;
            border-radius: 8px;
            font-size: 1.03em;
        }
        .reservado-info {
            background: #FEF7EA;
            border-left: 7px solid #F19111;
            padding: 12px 24px;
            margin: 18px 0 16px;
            border-radius: 8px;
            font-size: 1.03em;
            color: #955400;
        }
        .foot {
            font-size: 0.97em;
            color: #7b7b7b;
            margin-top: 24px;
            text-align: right;
        }
        .watermark {
            position: absolute;
            top: 52%; left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.09;
            z-index: 0;
            width: 68%;
        }
        /* Mensaje según tipo */
        .msg-title { font-size: 1.16em; font-weight: bold; margin-bottom: 4px; }
        .msg-desc  { font-size: 1.05em; color: #374151; margin-bottom: 10px;}
    </style>
</head>
<body>
    <div class="card">
        {{-- Watermark --}}
        <img src="{{ base_path('public/images/logo.png') }}" class="watermark" alt="logo">

        {{-- Estado distintivo --}}
        @php
            $colorClass = 'other'; $msgTitle = ''; $msgDesc = '';
            if (str_contains($estadoLabel, 'VENDIDO')) {
                $colorClass = 'vendido';
                $msgTitle = 'Ticket Pagado y Confirmado';
                $msgDesc  = 'Presenta este ticket como comprobante oficial. Tu compra ha sido registrada y validada en el sistema.';
            } elseif (str_contains($estadoLabel, 'ABONADO')) {
                $colorClass = 'abonado';
                $msgTitle = 'Ticket con Abono Parcial';
                $msgDesc  = 'Recuerda completar el pago pendiente antes de la fecha del sorteo. Este comprobante es válido solo si terminas el abono total.';
            } elseif (str_contains($estadoLabel, 'RESERVADO')) {
                $colorClass = 'reservado';
                $msgTitle = 'Ticket Apartado (Reservado)';
                $msgDesc  = 'El ticket está apartado a tu nombre, pero no ha sido abonado. Debes realizar el pago para validarlo antes del sorteo.';
            } else {
                $colorClass = 'other';
                $msgTitle = $estadoLabel;
                $msgDesc  = '';
            }
        @endphp

        <div class="estado-label {{ $colorClass }}">{{ $estadoLabel }}</div>

        <div class="msg-title">{{ $msgTitle }}</div>
        <div class="msg-desc">{{ $msgDesc }}</div>

        <div class="title">Ticket #{{ str_pad($ticket->numero, 3, '0', STR_PAD_LEFT) }}</div>
        <div class="info-list">
            <p><strong>Rifa:</strong> {{ $ticket->rifa->nombre }}</p>
            <p><strong>Cliente:</strong> {{ $clienteNombre }}</p>
            <p><strong>Teléfono:</strong> {{ $clienteTelefono }}</p>
            <p><strong>Dirección:</strong> {{ $clienteDireccion }}</p>
            <p><strong>Precio:</strong> Bs. {{ number_format($ticket->precio_ticket, 2) }}</p>
        </div>
        <div class="qr-box">
            {!! $qr_svg !!}
        </div>

        {{-- Info adicional según tipo --}}
        @if($abonoInfo)
            <div class="abono-info">
                <b>ABONO:</b> Bs. {{ number_format($abonoInfo['monto'], 2) }}<br>
                <b>Fecha:</b> {{ $abonoInfo['fecha'] }}<br>
                <b>Método de pago:</b> {{ $abonoInfo['metodo'] }}
            </div>
        @elseif(str_contains($estadoLabel, 'RESERVADO'))
            <div class="reservado-info">
                <b>Este ticket está apartado y no ha sido abonado.<br> Debe pagarlo antes del sorteo para ser válido.</b>
            </div>
        @endif

        <div class="foot">
            Sistema de Rifas — Generado el {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
