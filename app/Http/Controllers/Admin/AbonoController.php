<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abono;
use App\Models\Ticket;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AbonoController extends Controller
{
    /**
     * Display a listing of abonos.
     */
    public function index(Request $request)
    {
        $query = Abono::with(['ticket', 'paymentMethod']);

        if ($request->query('filter') === 'pendientes') {
            // Tickets reservados sin pago completo
            $query->whereColumn('monto', '<', 'ticket.precio_ticket')
                  ->orWhereNull('payment_method_id');
        }

        $abonos = $query->orderByDesc('created_at')->paginate(20);

        return view('admin.abonos.index', compact('abonos'));
    }

    /**
     * Show the form for creating a new abono.
     */
    public function create()
    {
        $tickets = Ticket::all();
        $methods = PaymentMethod::where('enabled', true)->get();

        return view('admin.abonos.create', compact('tickets', 'methods'));
    }

    /**
     * Store a newly created abono in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'ticket_id'        => ['required', 'exists:tickets,id'],
            'monto'            => ['required', 'numeric', 'min:0'],
            'payment_method_id'=> ['required', 'exists:payment_methods,id'],
            'referencia'       => [
                'required',
                'string',
                'max:255',
                function($attribute, $value, $fail) {
                    if (\App\Models\Abono::where('referencia', $value)->exists()) {
                        $fail('Esta referencia ya está registrada en el sistema.');
                    }
                }
            ],
            'banco'            => ['nullable', 'string', 'max:100'],
            'telefono'         => ['nullable', 'string', 'max:20'],
            'cedula'           => ['nullable', 'string', 'max:20'],
            'titular'          => ['nullable', 'string', 'max:100'],
        ]);

        Abono::create($data);

        return redirect()->route('admin.abonos.index')
                         ->with('success', 'Abono registrado correctamente.');
    }

    /**
     * Display the specified abono.
     */
    public function show(Abono $abono)
    {
        $abono->load(['ticket', 'paymentMethod']);
        return view('admin.abonos.show', compact('abono'));
    }

    /**
     * Show the form for editing the specified abono.
     */
    public function edit(Abono $abono)
    {
        $tickets = Ticket::all();
        $methods = PaymentMethod::where('enabled', true)->get();

        return view('admin.abonos.edit', compact('abono', 'tickets', 'methods'));
    }

    /**
     * Update the specified abono in storage.
     */
    public function update(Request $request, Abono $abono)
    {
        $data = $request->validate([
            'ticket_id'        => ['required', 'exists:tickets,id'],
            'monto'            => ['required', 'numeric', 'min:0'],
            'payment_method_id'=> ['required', 'exists:payment_methods,id'],
            'referencia'       => [
                'required',
                'string',
                'max:255',
                function($attribute, $value, $fail) use ($abono) {
                    // Permite el propio registro (edición), pero busca duplicados en abonos
                    $abonoExiste = \App\Models\Abono::where('referencia', $value)
                        ->where('id', '!=', $abono->id)
                        ->exists();
                    if ($abonoExiste) {
                        $fail('Esta referencia ya está registrada en el sistema.');
                    }
                }
            ],
            'banco'            => ['nullable', 'string', 'max:100'],
            'telefono'         => ['nullable', 'string', 'max:20'],
            'cedula'           => ['nullable', 'string', 'max:20'],
            'titular'          => ['nullable', 'string', 'max:100'],
        ]);

        $abono->update($data);

        return redirect()->route('admin.abonos.index')
                         ->with('success', 'Abono actualizado correctamente.');
    }

    /**
     * Remove the specified abono from storage.
     */
    public function destroy(Abono $abono)
    {
        $abono->delete();

        return redirect()->route('admin.abonos.index')
                         ->with('success', 'Abono eliminado correctamente.');
    }

    /**
     * AJAX: Validar si una referencia ya existe (para AlpineJS y formularios).
     */
    public function validarReferencia(Request $request)
    {
        $referencia = $request->query('referencia');
        $exceptId   = $request->query('except_id');

        // Buscar solo en abonos (NO en tickets)
        $query = Abono::where('referencia', $referencia);
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        $existe = $query->exists();

        return response()->json(['existe' => $existe]);
    }
}
