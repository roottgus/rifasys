<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Rifa;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketService
{
    public static function vistaIndex(Request $request)
    {
        $rifas = Rifa::orderBy('nombre')->get();
        return view('admin.tickets.index', compact('rifas'));
    }

    public static function listadoAjax(Request $request)
    {
        $query = Ticket::with(['cliente', 'rifa'])->whereHas('rifa');

        $rifa_id = $request->query('rifa_id');
        if ($rifa_id) {
            $query->where('rifa_id', $rifa_id);
        }

        $estado = $request->query('estado');
        if ($estado && in_array($estado, ['vendido', 'abonado', 'apartado', 'reservado'])) {
            $query->where('estado', $estado);
        } else {
            $query->whereIn('estado', ['vendido', 'abonado', 'apartado', 'reservado']);
        }

        $busqueda = $request->query('q');
        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->whereHas('cliente', function ($sub) use ($busqueda) {
                    $sub->where('nombre', 'like', "%$busqueda%")
                        ->orWhere('cedula', 'like', "%$busqueda%");
                })
                ->orWhere('numero', 'like', "%$busqueda%");
            });
        }

        $tickets = $query->orderBy('numero', 'asc')->get();

        return view('admin.tickets._tickets_list', compact('tickets'))->render();
    }

    public static function ticketsJson(Rifa $rifa)
    {
        try {
            $count = $rifa->cantidad_numeros;

            // (.. lógica de creación de tickets ..)

            $tickets = $rifa->tickets()
                ->leftJoin('clientes', 'tickets.cliente_id', '=', 'clientes.id')
                ->select(
                    'tickets.id',
                    'tickets.numero',
                    'tickets.estado',
                    'tickets.precio_ticket',
                    'tickets.uuid',
                    'tickets.rifa_id',
                    'clientes.nombre as cliente_nombre',
                    'clientes.direccion as cliente_direccion',
                    'clientes.telefono as cliente_telefono',
                    'clientes.cedula as cliente_cedula'
                )
                ->orderBy('tickets.numero')
                ->get();

            $ticketIds = $tickets->pluck('id')->all();
            $abonosPorTicket = DB::table('abonos')
                ->select('ticket_id', DB::raw('SUM(monto) as total_abonado'))
                ->whereIn('ticket_id', $ticketIds)
                ->groupBy('ticket_id')
                ->pluck('total_abonado', 'ticket_id')
                ->toArray();

            $jsonTickets = $tickets->map(function ($t) use ($abonosPorTicket) {
                return [
                    'id'                => $t->id,
                    'numero'            => $t->numero,
                    'estado'            => $t->estado,
                    'precio_ticket'     => $t->precio_ticket,
                    'uuid'              => $t->uuid,
                    'rifa_id'           => $t->rifa_id,
                    'cliente_nombre'    => $t->cliente_nombre,
                    'cliente_direccion' => $t->cliente_direccion,
                    'cliente_telefono'  => $t->cliente_telefono,
                    'cliente_cedula'    => $t->cliente_cedula,
                    'total_abonado'     => (float)($abonosPorTicket[$t->id] ?? 0),
                    'qr_url'            => route('admin.tickets.qr', $t->id),
                ];
            })->values();

            return response()->json($jsonTickets);
        } catch (\Throwable $e) {
            \Log::error('Error en ticketsJson: ' . $e->getMessage(), [
                'rifa_id' => $rifa->id,
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar tickets: ' . $e->getMessage(),
            ], 500);
        }
    }

    public static function venderTicket(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'cliente' => ['required', 'string', 'max:150'],
        ]);
        $cliente = Cliente::firstOrCreate(
            ['nombre' => $data['cliente']],
            []
        );
        $ticket->cliente()->associate($cliente);
        $ticket->estado = 'vendido';
        $ticket->save();

        \App\Models\Abono::create([
            'ticket_id'   => $ticket->id,
            'monto'       => $ticket->precio_ticket,
            'metodo_pago' => 'efectivo',
        ]);

        return response()->json([
            'message'    => "Ticket #{$ticket->numero} vendido a {$cliente->nombre}.",
            'ticket_id'  => $ticket->id,
            'cliente_id' => $cliente->id,
            'estado'     => $ticket->estado,
        ]);
    }

    public static function reservarTicket(Ticket $ticket)
    {
        if ($ticket->estado === 'vendido') {
            return back()->with('error', 'Este ticket ya está vendido y no puede reservarse.');
        }
        $ticket->update(['estado' => 'reservado']);

        \App\Models\Abono::create([
            'ticket_id'   => $ticket->id,
            'monto'       => 0,
            'metodo_pago' => 'reserva',
        ]);
        return back()->with('success', "Ticket #{$ticket->numero} reservado correctamente.");
    }

    public static function generarPdf(Ticket $ticket)
    {
        $ticket->load('rifa', 'cliente', 'abonos');
        $abono = $ticket->abonos()->latest()->first();
        $esAbonado = $abono && $abono->monto > 0 && $abono->monto < $ticket->precio_ticket;
        $esVendido = $ticket->estado === 'vendido';
        $esReservado = $ticket->estado === 'reservado';

        $estadoLabel = match (true) {
            $esVendido     => 'TICKET VENDIDO',
            $esAbonado     => 'TICKET ABONADO',
            $esReservado   => 'TICKET RESERVADO',
            default        => strtoupper($ticket->estado),
        };

        $cliente = $ticket->cliente;
        $clienteNombre = $cliente->nombre ?? '—';
        $clienteTelefono = $cliente->telefono ?? '—';
        $clienteDireccion = $cliente->direccion ?? '—';

        $abonoInfo = $esAbonado ? [
            'fecha'  => $abono->created_at->format('d/m/Y H:i'),
            'monto'  => $abono->monto,
            'metodo' => $abono->metodo_pago,
        ] : null;

        $qr_svg = $ticket->qr_code;

        $pdf = Pdf::loadView('admin.tickets.ticket_pdf', [
            'ticket'          => $ticket,
            'estadoLabel'     => $estadoLabel,
            'clienteNombre'   => $clienteNombre,
            'clienteTelefono' => $clienteTelefono,
            'clienteDireccion'=> $clienteDireccion,
            'abonoInfo'       => $abonoInfo,
            'qr_svg'          => $qr_svg,
        ])
        ->setPaper('a5', 'landscape');

        return $pdf->download("ticket-{$ticket->numero}-{$ticket->estado}.pdf");
    }

    public static function detalleTicket(Ticket $ticket)
    {
        // 1) Carga relaciones necesarias, incluyendo lotería y tipo de lotería
        $ticket->load([
            'cliente',
            'abonos',
            'rifa.premiosEspeciales',
            'rifa.loteria',
            'rifa.tipoLoteria',
        ]);

        // 2) Formateo de los abonos
        $abonos = $ticket->abonos->sortByDesc('created_at')->map(fn($a) => [
            'id'         => $a->id,
            'metodo_pago'=> $a->metodo_pago,
            'banco'      => $a->banco ?? '—',
            'monto'      => (float) $a->monto,
            'referencia' => $a->referencia ?? '—',
            'fecha'      => $a->created_at->format('Y-m-d H:i:s'),
        ])->values();

        // 3) Sumas y cálculos
        $totalAbonado   = $ticket->abonos->sum('monto');
        $precioTicket   = $ticket->precio_ticket;
        $saldoPendiente = max(0, $precioTicket - $totalAbonado);

        // 4) Longitud de padding para el número
        $padLength = $ticket->rifa && $ticket->rifa->cantidad_numeros
            ? strlen((string) ($ticket->rifa->cantidad_numeros - 1))
            : 3;

        // 5) Premios especiales formateados
        $premios = $ticket->rifa->premiosEspeciales->map(fn($p) => [
            'descripcion' => $p->descripcion ?: "Premio de \${$p->monto}",
            'fecha'       => \Carbon\Carbon::parse($p->fecha_premio)->format('d/m/Y'),
            'hora'        => substr($p->hora_premio, 0, 5),
        ])->all();

        // 6) Datos de la rifa, incluyendo lotería y tipo
        $rifaData = [
            'nombre'       => $ticket->rifa->nombre,
            'loteria'      => $ticket->rifa->loteria->nombre ?? '—',
            'tipo'         => $ticket->rifa->tipoLoteria->nombre ?? '—',
            'fecha_sorteo' => $ticket->rifa->fecha_sorteo,
            'hora_sorteo'  => $ticket->rifa->hora_sorteo,
        ];

        // 7) QR / código
        $codigoQR = $ticket->uuid ?? $ticket->id;
        $urlQR    = route('tickets.verificar', ['uuid' => $codigoQR]);

        // 8) Respuesta JSON
        return response()->json([
            'id'                => $ticket->id,
            'numero'            => $ticket->numero,
            'numero_formateado' => str_pad($ticket->numero, $padLength, '0', STR_PAD_LEFT),
            'pad_length'        => $padLength,
            'estado'            => $ticket->estado,
            'updated_at'        => $ticket->updated_at?->format('Y-m-d H:i:s'),
            'precio_ticket'     => $precioTicket,
            'total_abonado'     => $totalAbonado,
            'saldo_pendiente'   => $saldoPendiente,
            'cliente'           => $ticket->cliente?->only(['nombre','cedula','telefono','direccion']),
            'rifa'              => $rifaData,
            'premios'           => $premios,
            'abonos'            => $abonos,
            'codigo_qr'         => $codigoQR,
            'url_qr'            => $urlQR,
        ]);
    }

    public static function showTicket(Ticket $ticket)
    {
        $ticket->load(['cliente', 'abonos', 'rifa.premiosEspeciales', 'rifa.loteria', 'rifa.tipoLoteria']);

        $abonos = $ticket->abonos->sortByDesc('created_at');
        $totalAbonado   = $ticket->abonos->sum('monto');
        $precioTicket   = $ticket->precio_ticket;
        $saldoPendiente = max(0, $precioTicket - $totalAbonado);

        $padLength = $ticket->rifa && $ticket->rifa->cantidad_numeros
            ? strlen((string) ($ticket->rifa->cantidad_numeros - 1))
            : 3;

        $premios = method_exists($ticket, 'evaluacionPremiosEspeciales')
            ? $ticket->evaluacionPremiosEspeciales()
            : [];

        $codigoQR = $ticket->uuid ?? $ticket->id;
        $urlQR    = route('tickets.verificar', ['uuid' => $codigoQR]);
        $qr_svg   = \QrCode::format('svg')->size(180)->generate($urlQR);

        return view('admin.tickets.show', compact(
            'ticket', 'abonos', 'totalAbonado', 'precioTicket',
            'saldoPendiente', 'padLength', 'premios', 'codigoQR', 'urlQR', 'qr_svg'
        ));
    }
}
