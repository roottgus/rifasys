<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PremioEspecial;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;

class PremioEspecialController extends Controller
{
    /**
     * Devuelve los participantes de un premio, en JSON.
     */
    public function participantes(PremioEspecial $premio)
    {
        // Traemos todos los tickets de esta rifa con su relación de abonos y cliente
        $tickets = Ticket::where('rifa_id', $premio->rifa_id)
            ->with(['cliente', 'abonos'])
            ->orderBy('numero')
            ->get();

        // Filtramos solo los que tienen suma de abonos >= abono_minimo
        $filtrados = $tickets->filter(function($ticket) use ($premio) {
            return $ticket->abonos->sum('monto') >= $premio->abono_minimo;
        });

        // Mapeamos al formato que espera el frontend
        $data = $filtrados->map(function($t) {
            return [
                'numero'  => $t->numero,
                'cliente' => $t->cliente->nombre,
            ];
        })->values();

        return response()->json($data);
    }

    /**
     * Exporta en PDF los participantes de un premio.
     */
    public function exportPdf(PremioEspecial $premio)
    {
        // Recuperar participantes filtrados
        $tickets = Ticket::where('rifa_id', $premio->rifa_id)
            ->with(['cliente', 'abonos'])
            ->orderBy('numero')
            ->get()
            ->filter(fn($ticket) => $ticket->abonos->sum('monto') >= $premio->abono_minimo);

        $data = $tickets->map(fn($t) => [
            'numero'  => $t->numero,
            'cliente' => $t->cliente->nombre,
        ])->values();

        // Generar PDF con vista dedicada
        $pdf = Pdf::loadView('admin.premios.participantes_pdf', [
            'premio' => $premio,
            'data'   => $data,
        ])
        ->setPaper('a4', 'landscape');

        // Forzar descarga
        return $pdf->download("participantes_premio_{$premio->id}.pdf");
    }
}
