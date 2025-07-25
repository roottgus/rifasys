<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;

class ClienteController extends Controller
{
    /**
     * Listado de clientes con búsqueda y conteo de tickets.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $clientes = Cliente::withCount('tickets')
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                      ->orWhere('cedula', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('telefono', 'like', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.clientes.index', compact('clientes', 'search'));
    }

    /**
 * AJAX: Tickets del cliente para modal rápido.
 */
public function ticketsAjax($cliente)
{
    $cliente = Cliente::with(['tickets.rifa'])->findOrFail($cliente);

    $tickets = $cliente->tickets()
        ->with('rifa')
        ->orderBy('created_at', 'desc')
        ->get();

    // Calcula el pad_length según la rifa con mayor cantidad de boletos entre los tickets de este cliente
    $rifas = $tickets->pluck('rifa')->filter();
    $maxTotal = $rifas->map(function($rifa) {
        // Usa el campo que tengas en la tabla rifas (por ejemplo 'total_boletos' o 'cantidad_tickets')
        return $rifa->total_boletos ?? $rifa->cantidad_tickets ?? null;
    })->filter()->max();

    // Si no se encuentra, por defecto 3 (000-999)
    $padLength = $maxTotal ? strlen((string)($maxTotal - 1)) : 3;

    return response()->json([
        'cliente' => $cliente->only(['id', 'nombre', 'cedula', 'email', 'telefono']),
        'tickets' => $tickets->map(function ($t) {
            return [
                'id'          => $t->id,
                'numero'      => $t->numero,
                'estado'      => $t->estado,
                'monto'       => $t->monto,
                'created_at'  => $t->created_at ? $t->created_at->format('d/m/Y H:i') : '',
                'rifa_nombre' => $t->rifa->nombre ?? '-',
            ];
        }),
        'pad_length' => $padLength,
    ]);
}


    /**
     * Crear nuevo cliente.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:255',
            'cedula'    => 'required|string|max:30|unique:clientes,cedula',
            'email'     => 'nullable|email|max:255|unique:clientes,email',
            'telefono'  => 'nullable|string|max:30|unique:clientes,telefono',
            'direccion' => 'nullable|string|max:255',
        ]);

        $cliente = Cliente::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'cliente' => $cliente]);
        }

        return redirect()->route('admin.clientes.index')->with('success', 'Cliente registrado.');
    }

    /**
     * Editar cliente.
     */
    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $validated = $request->validate([
            'nombre'    => 'required|string|max:255',
            'cedula'    => "required|string|max:30|unique:clientes,cedula,{$cliente->id}",
            'email'     => "nullable|email|max:255|unique:clientes,email,{$cliente->id}",
            'telefono'  => "nullable|string|max:30|unique:clientes,telefono,{$cliente->id}",
            'direccion' => 'nullable|string|max:255',
        ]);

        $cliente->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'cliente' => $cliente]);
        }

        return redirect()->route('admin.clientes.index')->with('success', 'Cliente actualizado.');
    }

    /**
     * Eliminar cliente.
     */
    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Cliente eliminado.');
    }

    /**
     * Mostrar detalle completo de cliente.
     */
    public function show($id)
    {
        $cliente = Cliente::with('tickets.rifa')->findOrFail($id);
        return view('admin.clientes.show', compact('cliente'));
    }

    /**
     * Retornar vista de edición (solo si lo necesitas).
     */
    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('admin.clientes.edit', compact('cliente'));
    }

    /**
     * AJAX: Validación de unicidad/autocompletado para campos clave.
     */
    public function validarCampo(Request $request)
    {
        $campo = $request->input('campo');
        $valor = $request->input('valor');
        $cedulaActual = $request->input('cedula'); // Para saber si es el mismo cliente

        if (!in_array($campo, ['cedula', 'email', 'telefono'])) {
            return response()->json([
                'success' => false,
                'message' => 'Campo inválido.',
            ]);
        }

        // Si no hay valor, es libre para usar
        if (!$valor) {
            return response()->json([
                'success' => true,
                'exists' => false,
                'conflicto' => false,
                'message' => 'Libre para usar.',
            ]);
        }

        // Lógica por campo
       if ($campo === 'cedula') {
        $cliente = Cliente::where('cedula', $valor)->first();
        if ($cliente) {
            // Autocompletar si existe, incluyendo el ID
            return response()->json([
                'success'   => true,
                'exists'    => true,
                'conflicto' => false,
                'cliente'   => [
                    'id'        => $cliente->id,       // ← agregado
                    'cedula'    => $cliente->cedula,
                    'nombre'    => $cliente->nombre,
                    'email'     => $cliente->email,
                    'telefono'  => $cliente->telefono,
                    'direccion' => $cliente->direccion,
                ],
                'message'   => '¡Bienvenido de nuevo! Hemos detectado que ya estás registrado en el sistema y tus datos fueron autocompletados automáticamente.',
            ]);
        }
            // Disponible
            return response()->json([
                'success' => true,
                'exists' => false,
                'conflicto' => false,
                'message' => 'Libre para usar.',
            ]);
        }

        // Email/Teléfono: permitir si es el mismo cliente (edición)
        $query = Cliente::where($campo, $valor);
        if (in_array($campo, ['email', 'telefono']) && $cedulaActual) {
            $query = $query->where(function ($q) use ($cedulaActual) {
                $q->whereNull('cedula')->orWhere('cedula', '!=', $cedulaActual);
            });
        }
        $existe = $query->first();

        if ($existe) {
            return response()->json([
                'success' => true,
                'exists' => true,
                'conflicto' => true,
                'message' => ucfirst($campo) . ' ya está registrado a nombre de: ' . $existe->nombre . '. Por favor ingresa otro distinto.',
            ]);
        }

        return response()->json([
            'success' => true,
            'exists' => false,
            'conflicto' => false,
            'message' => 'Libre para usar.',
        ]);
    }
}
