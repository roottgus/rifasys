<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Loteria;
use App\Models\TipoLoteria;
use App\Http\Requests\StoreLoteriaRequest;
use App\Http\Requests\UpdateLoteriaRequest;
use Illuminate\Http\Request;



class LoteriaController extends Controller
{
    /**
     * Landing page with two cards: Agregar Lotería / Agregar Tipo de Lotería
     */
     public function gestion()
{
    // Objeto indexado por id (para Alpine.js)
    $loterias = Loteria::orderBy('nombre')->get()->keyBy('id');

    // Tipos de lotería con nombre de la lotería asociada (si la relación existe)
    $tiposLoteria = TipoLoteria::with('loteria:id,nombre')->orderBy('nombre')->get(['id', 'nombre', 'loteria_id']);

    return view('admin.loterias.gestion', compact('loterias', 'tiposLoteria'));
}



    /**
     * List all loterías.
     */
    public function index()
    {
        $loterias = Loteria::orderBy('nombre')->paginate(10);
        return view('admin.loterias.index', compact('loterias'));
    }

    /**
     * Show form to create a new lotería.
     */
    public function create()
{
    return view('admin.loterias.create');
}


    /**
     * Store a newly created lotería.
     */
   public function store(Request $request)
{
    // Validación
    $validated = $request->validate([
        'nombre' => ['required', 'string', 'max:255', 'unique:loterias,nombre']
    ]);

    // Crear lotería
    $loteria = \App\Models\Loteria::create($validated);

    // Si es AJAX, retornar JSON para que Alpine actualice la lista
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json([
            'success' => true,
            'loteria' => $loteria
        ]);
    }

    // Si es normal (fallback)
    return redirect()->route('admin.loterias.gestion')
        ->with('success', 'Lotería creada correctamente.');
}



    /**
     * Show form to edit an existing lotería.
     */
    public function edit(Loteria $loteria)
{
    $tiposLoteria = TipoLoteria::pluck('nombre', 'nombre')->toArray();
    return view('admin.loterias.edit', compact('loteria', 'tiposLoteria'));
}


    /**
     * Update the specified lotería.
     */
    public function update(UpdateLoteriaRequest $request, Loteria $loteria)
    {
        $loteria->update($request->validated());
        return back()->with('success', 'Lotería actualizada correctamente.');
    }

    /**
     * Remove the specified lotería.
     */
    // ... resto del código
public function destroy(Loteria $loteria)
{
    $loteria->delete();

    if (request()->wantsJson() || request()->ajax()) {
        return response()->json(['success' => true]);
    }

    return back()->with('success', 'Lotería eliminada.');
}

}
