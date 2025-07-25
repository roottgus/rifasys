<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Abono;
use App\Services\DescuentoService;

class AbonoService
{
    /**
     * Abonar un ticket de manera unitaria.
     *
     * @param \Illuminate\Http\Request $request
     * @param Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public static function abonarUnitario($request, Ticket $ticket)
    {
        $ticket->load('abonos', 'rifa.premiosEspeciales');
        $totalAbonado   = $ticket->abonos->sum('monto');
        $precioTicket   = $ticket->precio_ticket;
        $saldoPendiente = max(0, $precioTicket - $totalAbonado);

        if ($saldoPendiente <= 0) {
            return back()->with('error', 'El ticket ya está pagado completamente. No puede registrar más abonos.');
        }

        // Validación
        $rules = [
            'monto'         => ['required', 'numeric', 'min:0.01', 'max:' . $saldoPendiente],
            'metodo_pago'   => ['required', 'string', 'max:30'],
            'referencia'    => ['nullable', 'string', 'max:100', 'required_unless:metodo_pago,Efectivo'],
            'banco'         => ['nullable', 'string', 'max:100'],
            'telefono'      => ['nullable', 'string', 'max:30'],
            'correo'        => ['nullable', 'string', 'max:100'],
            'cedula'        => ['nullable', 'string', 'max:30'],
            'fecha_pago'    => ['required', 'date'],
            'lugar_pago'    => ['nullable', 'string', 'max:50'],
            'nota'          => ['nullable', 'string', 'max:250'],
        ];

        $validated = $request->validate($rules);

        if (
            $request->metodo_pago !== 'Efectivo' &&
            !empty($request->referencia) &&
            Abono::where('referencia', $request->referencia)->exists()
        ) {
            return back()->withInput()->with('error', 'La referencia ya fue utilizada en otro abono.');
        }

        // Registrar el abono
        $abono = Abono::create([
            'ticket_id'    => $ticket->id,
            'monto'        => $validated['monto'],
            'metodo_pago'  => $validated['metodo_pago'],
            'referencia'   => $validated['referencia'] ?? null,
            'banco'        => $validated['banco'] ?? null,
            'telefono'     => $validated['telefono'] ?? null,
            'correo'       => $validated['correo'] ?? null,
            'cedula'       => $validated['cedula'] ?? null,
            'fecha'        => $validated['fecha_pago'],
            'lugar_pago'   => $validated['lugar_pago'] ?? null,
            'nota'         => $validated['nota'] ?? null,
        ]);

        // Estado del ticket según nuevo abono
        $nuevoTotalAbonado = $ticket->abonos()->sum('monto') + $abono->monto;
        if ($nuevoTotalAbonado >= $ticket->precio_ticket) {
            $ticket->estado = 'vendido';
        } elseif ($nuevoTotalAbonado > 0) {
            $ticket->estado = 'abonado';
        }
        $ticket->save();

        // Calcula info de premios especiales tras el abono
        $estado = self::estadoTicket($ticket->fresh(), $ticket->precio_ticket);

        return back()->with('success', '¡Abono registrado correctamente!')->with('ticket_estado', $estado);
    }

    /**
     * Abono global (varios tickets, con descuento automático).
     *
     * @param array $ticketIds
     * @param float $monto
     * @param array $data (info de pago)
     * @return array
     * @throws \Exception
     */
    public static function abonarGlobal(array $ticketIds, float $monto, array $data)
    {
        $tickets = Ticket::whereIn('id', $ticketIds)->orderBy('id')->get();

        if ($tickets->isEmpty()) {
            throw new \Exception('No hay tickets válidos');
        }

        $rifaId = $tickets->first()->rifa_id;
        $clienteId = $tickets->first()->cliente_id;

        // Verifica todos los tickets pertenezcan a la misma rifa y cliente
        foreach ($tickets as $ticket) {
            if ($ticket->rifa_id != $rifaId || $ticket->cliente_id != $clienteId) {
                throw new \Exception('Todos los tickets deben ser de la misma rifa y cliente');
            }
        }

        // Cantidad total de tickets con el cliente en la rifa (incluye los seleccionados)
        $totalTickets = Ticket::where('rifa_id', $rifaId)
            ->where('cliente_id', $clienteId)
            ->count();

        // Descuento automático
        $descuento = DescuentoService::obtenerDescuento($rifaId, $clienteId, count($ticketIds));
        $precioTicketOriginal = $tickets->first()->precio_ticket;
        $precioConDescuento = $precioTicketOriginal * (1 - $descuento / 100);

        $abonoRestante = $monto;
        $abonosCreados = [];
        $ticketsEstado = [];

        foreach ($tickets as $ticket) {
            $abonadoPrevio = $ticket->abonos()->sum('monto');

            if ($abonadoPrevio >= $precioConDescuento) {
                $ticketsEstado[] = self::estadoTicket($ticket, $precioConDescuento);
                continue;
            }

            $faltaPorAbonar = $precioConDescuento - $abonadoPrevio;
            $aAbonar = min($faltaPorAbonar, $abonoRestante);

            if ($aAbonar <= 0) break;

            $abono = Abono::create([
                'ticket_id'        => $ticket->id,
                'monto'            => $aAbonar,
                'tipo'             => $data['tipo'] ?? 'efectivo',
                'metodo_pago'      => $data['metodo_pago'] ?? 'efectivo',
                'telefono'         => $data['telefono'] ?? null,
                'referencia'       => $data['referencia'] ?? null,
                'payment_method_id'=> $data['payment_method_id'] ?? null,
                'titular'          => $data['titular'] ?? null,
                'banco'            => $data['banco'] ?? null,
                'cedula'           => $data['cedula'] ?? null,
            ]);

            $abonosCreados[] = $abono;

            $nuevoTotalAbonado = $abonadoPrevio + $aAbonar;
            if ($nuevoTotalAbonado >= $precioConDescuento) {
                $ticket->estado = 'vendido';
            } else {
                $ticket->estado = 'abonado';
            }
            $ticket->save();

            $ticketsEstado[] = self::estadoTicket($ticket->fresh(), $precioConDescuento);

            $abonoRestante -= $aAbonar;
            if ($abonoRestante <= 0) break;
        }

        return [
            'success'              => true,
            'abonos'               => $abonosCreados,
            'descuento_aplicado'   => $descuento,
            'precio_ticket_final'  => round($precioConDescuento, 2),
            'tickets_estado'       => $ticketsEstado,
        ];
    }

    /**
     * Devuelve el estado profesional de un ticket (incluye info de premios especiales).
     *
     * @param Ticket $ticket
     * @param float $precioConDescuento
     * @return array
     */
    public static function estadoTicket(Ticket $ticket, $precioConDescuento): array
    {
        $premios = $ticket->rifa->premiosEspeciales ?? [];
        $premiosEstado = [];
        $totalAbonado = $ticket->abonos()->sum('monto');

        foreach ($premios as $premio) {
            $participa = $totalAbonado >= $premio->abono_minimo;
            $premiosEstado[] = [
                'premio_id'      => $premio->id,
                'descripcion'    => $premio->descripcion,
                'abono_minimo'   => $premio->abono_minimo,
                'participa'      => $participa,
                'abono_actual'   => $totalAbonado,
            ];
        }

        return [
            'ticket_id'         => $ticket->id,
            'numero'            => $ticket->numero,
            'estado'            => $ticket->estado,
            'total_abonado'     => $totalAbonado,
            'precio_con_desc'   => round($precioConDescuento, 2),
            'premios'           => $premiosEstado,
        ];
    }
}
