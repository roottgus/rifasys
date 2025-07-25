<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Descuento;
use App\Models\Rifa;
use Illuminate\Http\Request;
use App\Services\DescuentoService;

class DescuentoController extends Controller
{
    /**
     * Muestra listado de reglas de descuento.
     */
    public function index()
    {
        $descuentos = Descuento::with('rifa')
            ->orderBy('rifa_id')
            ->orderBy('cantidad_minima')
            ->get();
        return view('admin.descuentos.index', compact('descuentos'));
    }

    /**
     * Formulario de nueva regla.
     */
    public function create()
    {
        $rifas = Rifa::orderBy('nombre')->get();
        return view('admin.descuentos.create', compact('rifas'));
    }

    /**
     * Guarda nueva regla.
     */
    public function store(Request $request)
    {
        $request->validate([
            'rifa_id'         => 'required|exists:rifas,id',
            'cantidad_minima' => 'required|integer|min:1',
            'porcentaje'      => 'required|numeric|min:1|max:100',
        ]);

        Descuento::create($request->only(['rifa_id', 'cantidad_minima', 'porcentaje']));

        return redirect()->route('admin.descuentos.index')
            ->with('success', 'Regla de descuento creada correctamente.');
    }

    /**
     * Formulario para editar.
     */
    public function edit(Descuento $descuento)
    {
        $rifas = Rifa::orderBy('nombre')->get();
        return view('admin.descuentos.edit', compact('descuento', 'rifas'));
    }

    /**
     * Actualiza una regla.
     */
    public function update(Request $request, Descuento $descuento)
    {
        $request->validate([
            'rifa_id'         => 'required|exists:rifas,id',
            'cantidad_minima' => 'required|integer|min:1',
            'porcentaje'      => 'required|numeric|min:1|max:100',
        ]);

        $descuento->update($request->only(['rifa_id', 'cantidad_minima', 'porcentaje']));

        return redirect()->route('admin.descuentos.index')
            ->with('success', 'Regla de descuento actualizada correctamente.');
    }

    /**
     * Elimina una regla.
     */
    public function destroy(Descuento $descuento)
    {
        $descuento->delete();
        return redirect()->route('admin.descuentos.index')
            ->with('success', 'Regla eliminada.');
    }

   /**
 * AJAX: Devuelve el mayor descuento aplicable para un cliente en una rifa,
 * considerando la cantidad actual y los tickets seleccionados.
 *
 * Request esperado:
 * - rifa_id
 * - cliente_id
 * - cantidad
 */
public function obtenerDescuentoAjax(Request $request)
{
    $data = $request->validate([
        'rifa_id'    => 'required|exists:rifas,id',
        'cantidad'   => 'required|integer|min:1',
        'cliente_id' => 'nullable|exists:clientes,id',
    ]);

    $rifaId    = (int) $data['rifa_id'];
    $clienteId = isset($data['cliente_id']) ? (int) $data['cliente_id'] : null;
    $cantidad  = (int) $data['cantidad'];    // ← Leemos 'cantidad', no 'nuevos_tickets'

    // Calcula porcentaje con tu Service (incluye histórico)
    $descuentoPct = DescuentoService::obtenerDescuento($rifaId, $clienteId, $cantidad);

    // Obtén precio unitario
    $rifa      = Rifa::findOrFail($rifaId);
    $unitPrice = (float) $rifa->precio;

    // Calcula valores
    $subtotal       = $unitPrice * $cantidad;
    $montoDescuento = $subtotal * ($descuentoPct / 100);
    $totalFinal     = $subtotal - $montoDescuento;

    // Armar motivo (opcional)
    $umbral = Descuento::where('rifa_id', $rifaId)
        ->where('porcentaje', $descuentoPct)
        ->orderByDesc('cantidad_minima')
        ->first();
    $motivo = ($descuentoPct > 0 && $umbral)
        ? "Promoción por acumular {$umbral->cantidad_minima} o más tickets"
        : '';

    return response()->json([
        'descuento'       => $descuentoPct,
        'subtotal'        => round($subtotal, 2),
        'monto_descuento' => round($montoDescuento, 2),
        'total_final'     => round($totalFinal, 2),
        'motivo'          => $motivo,
    ]);
}

}
