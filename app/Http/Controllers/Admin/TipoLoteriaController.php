<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoLoteria;
use App\Http\Requests\StoreTipoLoteriaRequest;
use App\Http\Requests\UpdateTipoLoteriaRequest;
use Illuminate\Http\Request;

class TipoLoteriaController extends Controller
{
    public function index(Request $request)
    {
        // Si la petición es AJAX, devolvemos TODOS los tipos planos para Alpine
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(
                TipoLoteria::orderBy('nombre')->get(['id', 'nombre', 'loteria_id'])
            );
        }

        // Vista blade tradicional (paginado sólo si es vista, no ajax)
        $tipos = TipoLoteria::with('loteria')
                    ->orderBy('nombre')
                    ->paginate(10);

        return view('admin.tipos-loteria.index', compact('tipos'));
    }

    public function create()
    {
        return view('admin.tipos-loteria.create');
    }

    public function store(StoreTipoLoteriaRequest $request)
    {
        $tipo = TipoLoteria::create($request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            // Devolvemos datos mínimos y relación para front
            return response()->json([
                'success' => true,
                'tipoLoteria' => [
                    'id' => $tipo->id,
                    'nombre' => $tipo->nombre,
                    'loteria_id' => $tipo->loteria_id,
                ]
            ]);
        }

        return redirect()->route('admin.tipos-loteria.index')
                         ->with('success','Tipo de lotería creado.');
    }

    public function edit(TipoLoteria $tiposLoteria)
    {
        return view('admin.tipos-loteria.edit', compact('tiposLoteria'));
    }

    public function update(UpdateTipoLoteriaRequest $request, TipoLoteria $tiposLoteria)
    {
        $tiposLoteria->update($request->validated());
        return back()->with('success','Tipo de lotería actualizado.');
    }

public function destroy(TipoLoteria $tipoLoteria)
{
    $tipoLoteria->delete();

    if (request()->wantsJson() || request()->ajax()) {
        return response()->json(['success' => true]);
    }

    return back()->with('success', 'Tipo de lotería eliminado.');
}


    /**
     * Devuelve los tipos de lotería hijos de una lotería (AJAX).
     */
    public function tiposPorLoteria($loteriaId)
    {
        // Solo retorna los tipos hijos de la lotería seleccionada
        return response()->json(
            TipoLoteria::where('loteria_id', $loteriaId)
                ->orderBy('nombre')
                ->get(['id', 'nombre'])
        );
    }
}
