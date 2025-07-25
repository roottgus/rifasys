<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Muestra la pantalla de verificación de un ticket por su UUID.
     */
    public function verificar(string $uuid)
{
    // Trae ticket con info de rifa, cliente y abonos
    $ticket = \App\Models\Ticket::with(['rifa.premiosEspeciales', 'cliente', 'abonos'])->where('uuid', $uuid)->first();

    // --- Traer settings corporativos ---
    $settings = [
        'empresa_nombre' => \App\Models\Setting::get('empresa_nombre', config('app.name')),
        'empresa_logo'   => \App\Models\Setting::get('empresa_logo'),
        'empresa_color'  => \App\Models\Setting::get('empresa_color', '#0d47a1'),
    ];

    if (!$ticket) {
        return view('tickets.verificar', [
            'ticket'  => null,
            'error'   => "Ticket no encontrado.",
            'success' => null,
            'settings'=> $settings,
        ]);
    }

    // Calcular total abonado
    $totalAbonado = $ticket->abonos->sum('monto');
    $premiosEspeciales = $ticket->rifa->premiosEspeciales->map(function($premio) use ($totalAbonado) {
        $participa = $totalAbonado >= $premio->abono_minimo;
        return [
            'nombre'        => $premio->tipo_premio,
            'detalle'       => $premio->detalle_articulo,
            'monto'         => $premio->monto,
            'fecha'         => $premio->fecha_premio?->format('d/m/Y'),
            'hora'          => $premio->hora_premio,
            'abono_minimo'  => $premio->abono_minimo,
            'participa'     => $participa,
            'total_abonado' => $totalAbonado,
        ];
    });

    $yaVerificado = $ticket->estado === 'verificado';

    return view('tickets.verificar', [
        'ticket'            => $ticket,
        'yaVerificado'      => $yaVerificado,
        'error'             => session('error'),
        'success'           => session('success'),
        'settings'          => $settings,
        'premiosEspeciales' => $premiosEspeciales,
        'totalAbonado'      => $totalAbonado,
    ]);
}

    /**
     * Procesa la solicitud para marcar el ticket como verificado.
     */
    public function marcarVerificado(Request $request, string $uuid)
    {
        $ticket = Ticket::where('uuid', $uuid)->first();

        if (!$ticket) {
            return redirect()->route('tickets.verificar', $uuid)
                ->with('error', 'Ticket no encontrado.');
        }

        if ($ticket->estado === 'verificado') {
            return redirect()->route('tickets.verificar', $uuid)
                ->with('error', 'Este ticket ya fue verificado previamente.');
        }

        if ($ticket->estado !== 'vendido') {
            return redirect()->route('tickets.verificar', $uuid)
                ->with('error', 'Solo los tickets vendidos pueden ser verificados.');
        }

        $ticket->estado = 'verificado';
        $ticket->save();

        return redirect()->route('tickets.verificar', $uuid)
            ->with('success', '¡Ticket verificado correctamente!');
    }
}
