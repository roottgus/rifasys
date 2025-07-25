<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Rifa;
use App\Services\TicketService;
use App\Services\AbonoService;

class TicketGestionController extends Controller
{
    public function index(Request $request)
    {
        return TicketService::vistaIndex($request);
    }

    public function ajaxIndex(Request $request)
    {
        return TicketService::listadoAjax($request);
    }

    public function ticketsJson(Rifa $rifa)
    {
        return TicketService::ticketsJson($rifa);
    }

    public function sell(Request $request, Ticket $ticket)
    {
        return TicketService::venderTicket($request, $ticket);
    }

    public function reserve(Ticket $ticket)
    {
        return TicketService::reservarTicket($ticket);
    }

    public function pdf(Ticket $ticket)
    {
        return TicketService::generarPdf($ticket);
    }

    public function detalleJson(Ticket $ticket)
    {
        return TicketService::detalleTicket($ticket);
    }

    public function show(Ticket $ticket)
    {
        return TicketService::showTicket($ticket);
    }

    public function abonar(Request $request, Ticket $ticket)
    {
        return AbonoService::abonarUnitario($request, $ticket);
    }

    public function abonarGlobal(Request $request)
    {
        $request->validate([
            'tickets'      => 'required|array|min:1',
            'tickets.*'    => 'required|integer|exists:tickets,id',
            'monto'        => 'required|numeric|min:0.01',
            'tipo'         => 'required|string',
            'metodo_pago'  => 'required|string',
        ]);

        try {
            $resultado = AbonoService::abonarGlobal(
                $request->input('tickets'),
                (float)$request->input('monto'),
                $request->all()
            );

            return response()->json([
                'success'  => true,
                'mensaje'  => 'Abono global repartido correctamente, con descuento aplicado.',
                'abonos'   => $resultado['abonos'],
                'descuento_aplicado' => $resultado['descuento_aplicado'],
                'precio_ticket_final' => $resultado['precio_ticket_final'],
                'tickets_estado'      => $resultado['tickets_estado'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => $e->getMessage(),
            ], 422);
        }
    }
}
