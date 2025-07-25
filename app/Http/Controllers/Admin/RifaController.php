<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRifaRequest;
use App\Http\Requests\UpdateRifaRequest;
use App\Models\Loteria;
use App\Models\Rifa;
use App\Models\TipoLoteria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RifaController extends Controller
{
    public function index()
    {
        $rifas = Rifa::with(['loteria', 'tipoLoteria'])
                     ->latest('fecha_sorteo')
                     ->paginate(15);

        return view('admin.rifas.index', compact('rifas'));
    }

    public function create()
    {
        $loterias = Loteria::pluck('nombre', 'id')->toArray();
        // Por defecto ningún tipo de lotería (hasta que seleccionen una lotería en el frontend)
        $tiposLoteria = [];
        return view('admin.rifas.create', compact('loterias', 'tiposLoteria'));
    }

    public function store(StoreRifaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('rifas', 'public');
        }

        // Premios especiales
        $premiosData = $data['premios'] ?? [];
        unset($data['premios']);

        $rifa = Rifa::create($data);

        // Tickets automáticos
        $count  = $rifa->cantidad_numeros;
        $padLen = strlen((string)($count - 1));
        for ($i = 0; $i < $count; $i++) {
            $rifa->tickets()->create([
                'numero'        => str_pad((string)$i, $padLen, '0', STR_PAD_LEFT),
                'precio_ticket' => $rifa->precio,
                'estado'        => 'disponible',
            ]);
        }

        // Premios especiales
        foreach ($premiosData as $premio) {
            $premio['descripcion'] = $premio['descripcion'] ?? '';
            $rifa->premiosEspeciales()->create($premio);
        }

        return redirect()
            ->route('admin.rifas.index')
            ->with('success', 'Rifa, tickets y premios especiales creados correctamente.');
    }

    public function show(Rifa $rifa)
    {
        $rifa->load(['loteria', 'tipoLoteria', 'premiosEspeciales']);
        return view('admin.rifas.show', compact('rifa'));
    }

    public function edit(Rifa $rifa)
    {
        $loterias = Loteria::pluck('nombre', 'id')->toArray();
        // Cargamos los tipos de lotería solo para la lotería actual de la rifa
        $tiposLoteria = $rifa->loteria_id
            ? TipoLoteria::where('loteria_id', $rifa->loteria_id)->pluck('nombre', 'id')->toArray()
            : [];
        return view('admin.rifas.edit', compact('rifa', 'loterias', 'tiposLoteria'));
    }

    public function update(UpdateRifaRequest $request, Rifa $rifa): RedirectResponse
    {
        $data = $request->validated();

        // Imagen nueva
        if ($request->hasFile('imagen')) {
            if ($rifa->imagen) {
                Storage::disk('public')->delete($rifa->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('rifas', 'public');
        }

        // Premios especiales
        $premiosData = $data['premios'] ?? [];
        unset($data['premios']);

        // Calcular tickets a regenerar
        $newCount     = $data['cantidad_numeros'] ?? $rifa->cantidad_numeros;
        $currentCount = $rifa->tickets()->count();

        $rifa->update($data);

        // Si cambió cantidad, regenerar
        if ($currentCount !== $newCount) {
            $rifa->tickets()->delete();
            $padLen = strlen((string)($newCount - 1));
            for ($i = 0; $i < $newCount; $i++) {
                $rifa->tickets()->create([
                    'numero'        => str_pad((string)$i, $padLen, '0', STR_PAD_LEFT),
                    'precio_ticket' => $rifa->precio,
                    'estado'        => 'disponible',
                ]);
            }
        }

        // Sincronizar premios especiales
        $rifa->premiosEspeciales()->delete();
        foreach ($premiosData as $premio) {
            $premio['descripcion'] = $premio['descripcion'] ?? '';
            $rifa->premiosEspeciales()->create($premio);
        }

        return redirect()
            ->route('admin.rifas.index')
            ->with('success', 'Rifa, tickets y premios especiales actualizados correctamente.');
    }

    public function destroy(Rifa $rifa): RedirectResponse
    {
        if ($rifa->imagen) {
            Storage::disk('public')->delete($rifa->imagen);
        }
        $rifa->premiosEspeciales()->delete();
        $rifa->tickets()->delete();
        $rifa->delete();

        return redirect()
            ->route('admin.rifas.index')
            ->with('success', 'Rifa, tickets y premios especiales eliminados correctamente.');
    }

    public function cloneLast(): RedirectResponse
    {
        $original = Rifa::with(['premiosEspeciales', 'tickets'])
                        ->latest('fecha_sorteo')
                        ->firstOrFail();

        $clone = $original->replicate(['imagen']);
        $clone->nombre       .= ' (Copia)';
        $clone->fecha_sorteo = now()->addWeek()->toDateString();
        $clone->hora_sorteo  = now()->addWeek()->format('H:i');
        $clone->save();

        foreach ($original->premiosEspeciales as $premio) {
            $clone->premiosEspeciales()->create($premio->toArray());
        }

        $count  = $clone->cantidad_numeros;
        $padLen = strlen((string)($count - 1));
        for ($i = 0; $i < $count; $i++) {
            $clone->tickets()->create([
                'numero'        => str_pad((string)$i, $padLen, '0', STR_PAD_LEFT),
                'precio_ticket' => $clone->precio,
                'estado'        => 'disponible',
            ]);
        }

        return redirect()
            ->route('admin.rifas.edit', $clone)
            ->with('success', 'Se ha clonado la última rifa con sus tickets y premios.');
    }

    /**
     * Confirmar ganador principal de la rifa vía AJAX.
     */
    public function winnerPrincipal(Request $request, Rifa $rifa)
    {
        $data = $request->validate([
            'numero' => ['required', 'integer'],
        ]);

        $ticket = $rifa->tickets()
                       ->with(['cliente', 'abonos'])
                       ->where('numero', $data['numero'])
                       ->first();

        if (! $ticket) {
            return response()->json([
                'message' => "El ticket #{$data['numero']} no existe."
            ], 404);
        }

        $total    = $ticket->abonos->sum('monto');
        $solvente = $ticket->estado === 'vendido' || $total >= $ticket->precio_ticket;

        return response()->json([
            'ticket'       => [
                'numero' => $ticket->numero,
                'precio' => $ticket->precio_ticket,
            ],
            'cliente'      => [
                'nombre'    => $ticket->cliente->nombre,
                'telefono'  => $ticket->cliente->telefono  ?? '-',
                'direccion' => $ticket->cliente->direccion ?? '-',
            ],
            'solvente'     => $solvente,
            'total_abonos' => $total,
        ]);
    }

    /**
     * API: Tipos de lotería por lotería (AJAX dependiente).
     */
    public function tiposPorLoteria($loteriaId)
    {
        // Devuelve tipos de lotería para ese padre
        $tipos = TipoLoteria::where('loteria_id', $loteriaId)
                    ->get(['id', 'nombre']);
        return response()->json($tipos);
    }

    // Devuelve los participantes solventes del sorteo principal
public function participantesSolventes(Rifa $rifa)
{
    // Cargar tickets vendidos o solventes (pagados completos)
    $tickets = $rifa->tickets()
        ->with('cliente', 'abonos')
        ->get()
        ->filter(function ($ticket) {
            // Pagado en su totalidad (abonos >= precio_ticket) o vendido
            $total = $ticket->abonos->sum('monto');
            return $ticket->estado === 'vendido' || $total >= $ticket->precio_ticket;
        })
        ->map(function ($ticket) {
            return [
                'numero'  => $ticket->numero,
                'cliente' => $ticket->cliente ? $ticket->cliente->nombre : '(Sin cliente)',
                'monto'   => $ticket->abonos->sum('monto'),
            ];
        })
        ->values();

    return response()->json($tickets);
}

/**
 * Resumen rápido AJAX de la rifa seleccionada.
 * Devuelve cantidad de tickets por estado y progreso.
 */
public function resumenRifa(Rifa $rifa)
{
    // Cuenta tickets por estado
    $totales = $rifa->tickets()
        ->selectRaw("
            estado,
            COUNT(*) as cantidad
        ")
        ->groupBy('estado')
        ->pluck('cantidad', 'estado');

    $vendidos   = (int) ($totales['vendido'] ?? 0);
    $abonados   = (int) ($totales['abonado'] ?? 0);
    $apartados  = (int) ($totales['apartado'] ?? 0);
    $reservados = (int) ($totales['reservado'] ?? 0);
    $disponibles= (int) ($totales['disponible'] ?? 0);

    $total = $rifa->cantidad_numeros;
    $precio = $rifa->precio ?? 0; // Si no existe la columna, pon el valor default

    // Progreso (tickets NO disponibles)
    $progreso = $total > 0
        ? round((($vendidos + $abonados + $apartados + $reservados) / $total) * 100, 1)
        : 0;

    // Formatea la fecha de sorteo
    $fechaFormateada = $rifa->fecha_sorteo
        ? \Carbon\Carbon::parse($rifa->fecha_sorteo)->format('d M Y')
        : '';

    // Total recaudado: vendidos + abonados × precio
    $totalRecaudado = ($vendidos + $abonados) * $precio;

    // Restantes: tickets aún disponibles (puedes cambiar lógica según tus necesidades)
    $restantes = $disponibles;

    return response()->json([
        'total'            => $total,
        'vendidos'         => $vendidos,
        'abonados'         => $abonados,
        'apartados'        => $apartados,
        'reservados'       => $reservados,
        'disponibles'      => $disponibles,
        'restantes'        => $restantes,
        'progreso'         => $progreso,
        'nombre'           => $rifa->nombre,
        'fecha'            => $fechaFormateada,
        'total_recaudado'  => $totalRecaudado,
        'precio_ticket'    => $precio,
    ]);
}



}