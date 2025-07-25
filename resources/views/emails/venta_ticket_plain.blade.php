Ticket de Rifa - {{ config('app.name') }}

Rifa: {{ $ticket->rifa->nombre }}
Ticket: {{ str_pad($ticket->numero, 3, '0', STR_PAD_LEFT) }}
Estado: {{ strtoupper($ticket->estado) }}

Cliente: {{ $cliente->nombre }}
Teléfono: {{ $cliente->telefono ?? '—' }}
Dirección: {{ $cliente->direccion ?? '—' }}

Verifica tu ticket en:
{{ url('tickets/verificar/'.$ticket->uuid) }}

¡Gracias por tu compra!
