<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rifa;
use App\Models\Ticket;
use App\Models\Abono;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Fecha de hoy
        $hoy = Carbon::today();

        // 1. Rifas activas (en curso)
        $rifasActivas = Rifa::whereDate('fecha_sorteo', '>=', $hoy)->count();

        // 2. Tickets vendidos HOY (estado 'vendido' pagados hoy)
        $ticketsVendidosHoy = Ticket::where('estado', 'vendido')
            ->whereDate('created_at', $hoy)
            ->count();

        // 3. Tickets con abonos pendientes (pueden ser estados 'abonado', 'reservado', etc.)
        $ticketsConAbono = Ticket::whereIn('estado', ['abonado', 'reservado'])->count();

        // 4. Ingresos acumulados: SOLO pagos confirmados
        $ingresosTotales = Ticket::where('estado', 'vendido')->sum('precio_ticket');

        // Detalles de ingresos
        $ingresosHoy = Ticket::where('estado', 'vendido')
            ->whereDate('created_at', $hoy)
            ->sum('precio_ticket');

        $ingresosMes = Ticket::where('estado', 'vendido')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('precio_ticket');

        // 5. Próximo sorteo (siguiente rifa con fecha >= hoy)
        $proximoSorteo = Rifa::whereDate('fecha_sorteo', '>=', $hoy)
            ->orderBy('fecha_sorteo', 'asc')
            ->first();

        // 6. Últimos 10 tickets (cualquier estado)
        $ultimosTickets = Ticket::with([
            'cliente',
            'rifa',
            'abonos' => function ($q) {
                $q->orderByDesc('created_at')->limit(1);
            }
        ])
        ->whereIn('estado', ['vendido', 'abonado', 'apartado', 'reservado'])
        ->orderByDesc('updated_at')
        ->limit(10)
        ->get();

        // 7. Ventas diarias últimos 7 días (solo 'vendido')
        $hace6dias = (clone $hoy)->subDays(6);
        $ventas = Ticket::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('estado', 'vendido')
            ->whereBetween('created_at', [$hace6dias->startOfDay(), $hoy->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $ventasFechas = $ventas->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('d M'))
            ->toArray();
        $ventasDatos  = $ventas->pluck('count')->toArray();

        // 8. Abonos por método
        $abonos = Abono::select('tipo', DB::raw('SUM(monto) as total'))
            ->groupBy('tipo')
            ->get();
        $metodosAbono = $abonos->pluck('tipo')->toArray();
        $abonosDatos   = $abonos->pluck('total')->toArray();

        // Mostrar modal solo la primera vez tras login (session flash)
        $showWelcomeModal = session('showWelcomeModal', true);
        session()->forget('showWelcomeModal');

        return view('admin.dashboard', compact(
            'rifasActivas',
            'ticketsVendidosHoy',
            'ticketsConAbono',
            'ingresosTotales',
            'ingresosHoy',
            'ingresosMes',
            'proximoSorteo',
            'ultimosTickets',
            'ventasFechas',
            'ventasDatos',
            'metodosAbono',
            'abonosDatos',
            'showWelcomeModal'
        ));
    }
}
