<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rifa;
use App\Models\PaymentMethod;
use App\Models\Ticket;
use App\Models\Cliente;
use App\Models\Abono;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketVentaController extends Controller
{
    public function __construct()
    {
        // Sin inyección de QrCodeService: dejamos que el front convierta el SVG
    }

    /**
     * Pantalla de venta de tickets (con métodos de pago activos y rifas).
     */
    public function sale()
    {
        $rifas = Rifa::with([
            'loteria',
            'tipoLoteria',
            'premiosEspeciales.loteria',
            'premiosEspeciales.tipoLoteria',
        ])->orderBy('nombre')->get();

        $rifas = $rifas->map(function($rifa) {
            return [
                'id'               => $rifa->id,
                'nombre'           => $rifa->nombre,
                'precio'           => $rifa->precio ?? 0,
                'fecha_sorteo'     => $rifa->fecha_sorteo
                    ? $rifa->fecha_sorteo->format('Y-m-d')
                    : null,
                'hora_sorteo'      => $rifa->hora_sorteo ?? '',
                'cantidad_numeros' => $rifa->cantidad_numeros ?? 0,
                'loteria'          => $rifa->loteria ? [
                    'id'     => $rifa->loteria->id,
                    'nombre' => $rifa->loteria->nombre,
                ] : null,
                'tipo_loteria'     => $rifa->tipoLoteria ? [
                    'id'     => $rifa->tipoLoteria->id,
                    'nombre' => $rifa->tipoLoteria->nombre,
                ] : null,
                'premios_especiales' => $rifa->premiosEspeciales->map(function($p) {
                    return [
                        'id'               => $p->id,
                        'tipo_premio'      => $p->tipo_premio,
                        'monto'            => $p->monto,
                        'detalle_articulo' => $p->detalle_articulo,
                        'fecha_premio'     => $p->fecha_premio,
                        'hora_premio'      => $p->hora_premio,
                        'loteria'          => $p->loteria ? [
                            'id'     => $p->loteria->id,
                            'nombre' => $p->loteria->nombre,
                        ] : null,
                        'tipo_loteria'     => $p->tipoLoteria ? [
                            'id'     => $p->tipoLoteria->id,
                            'nombre' => $p->tipoLoteria->nombre,
                        ] : null,
                    ];
                })->toArray(),
            ];
        })->values();

        // Configuración de métodos de pago
        $configs = [
    'tran_bancaria_nacional' => [
        'icon'        => 'fas fa-university text-primary',
        'descripcion' => 'Transferencia a cuenta bancaria nacional.',
        'info'        => '',
        'fields'      => [
            [ 'key' => 'banco',   'label' => 'Banco' ],
            [ 'key' => 'cuenta',  'label' => 'Cuenta' ],
            [ 'key' => 'titular', 'label' => 'Titular' ],
            [ 'key' => 'ci_rif',  'label' => 'CI/RIF' ],
        ],
    ],
    'pago_efectivo' => [
        'icon'        => 'fas fa-money-bill-wave text-green-600',
        'descripcion' => 'Pago en efectivo.',
        'info'        => '',
        'fields'      => [
            [ 'key' => 'detalle', 'label' => 'Detalle' ],
        ],
    ],
    'pago_movil' => [
        'icon'        => 'fas fa-mobile-alt text-indigo-500',
        'descripcion' => 'Pago móvil (Venezuela).',
        'info'        => '',
        'fields'      => [
            [ 'key' => 'banco',    'label' => 'Banco' ],
            [ 'key' => 'telefono', 'label' => 'Teléfono' ],
            [ 'key' => 'ci_rif',   'label' => 'CI/RIF' ],
        ],
    ],
    'tran_bancaria_internacional' => [
        'icon'        => 'fas fa-globe text-cyan-500',
        'descripcion' => 'Transferencia bancaria internacional.',
        'info'        => '',
        'fields'      => [
            [ 'key' => 'banco',   'label' => 'Banco' ],
            [ 'key' => 'cuenta',  'label' => 'Cuenta' ],
            [ 'key' => 'titular', 'label' => 'Titular' ],
            [ 'key' => 'ci_rif',  'label' => 'ID/Documento' ],
        ],
    ],
    'zelle' => [
        'icon'        => 'fab fa-cc-visa text-blue-500',
        'descripcion' => 'Transferencia Zelle.',
        'info'        => '',
        'fields'      => [
            [ 'key' => 'correo',  'label' => 'Correo Zelle' ],
            [ 'key' => 'titular', 'label' => 'Titular' ],
        ],
    ],
];
        $metodosPagoActivos = PaymentMethod::where('enabled', 1)
            ->orderBy('id')
            ->get()
            ->map(function($m) use ($configs) {
                $config = $configs[$m->key] ?? [
                    'icon'        => 'fas fa-credit-card text-gray-400',
                    'descripcion' => '',
                    'info'        => '',
                    'fields'      => [],
                ];
                $details = is_array($m->details)
                    ? $m->details
                    : (json_decode($m->details, true) ?: []);
                return [
                    'id'          => $m->id,
                    'key'         => $m->key,
                    'name'        => $m->name,
                    'alias'       => $m->alias ?? null,
                    'icon'        => $config['icon'],
                    'descripcion' => $config['descripcion'],
                    'info'        => $config['info'],
                    'fields'      => collect($config['fields'])->map(function($f) use ($details) {
                        return $f + ['value' => ($details[$f['key']] ?? '')];
                    })->toArray(),
                ];
            })
            ->values();

        return view('admin.tickets.sale', [
            'rifas'              => $rifas,
            'metodosPagoActivos' => $metodosPagoActivos,
        ]);
    }

    /**
     * Procesar la venta (AJAX)
     */
   public function procesarVenta(Request $request)
    {
        $data = $request->validate([
            'ticket_ids'       => 'required|array|min:1',
            'ticket_ids.*'     => 'integer|exists:tickets,id',
            'accion'           => 'required|in:vender,apartado,abono',
            'cliente.cedula'   => 'nullable|string|max:20',
            'cliente.nombre'   => 'required|string|max:150',
            'cliente.email'    => 'nullable|email|max:150',
            'cliente.telefono' => 'nullable|string|max:50',
            'cliente.direccion'=> 'nullable|string|max:150',
            'monto_abono'      => 'nullable|numeric|min:0.01',
            'abono_global'     => 'nullable|boolean',
            'ticket_id'        => 'nullable|integer|exists:tickets,id',
            'metodo_pago'      => 'nullable|string|max:100',
            'pago_datos'       => 'nullable|array',
        ]);

        try {
            $result = null;

            DB::transaction(function() use ($data, &$result) {
                // 1) Crear o actualizar cliente
                $cliente = Cliente::updateOrCreate(
                    ['cedula' => $data['cliente']['cedula'] ?? null],
                    [
                        'nombre'    => $data['cliente']['nombre'],
                        'email'     => $data['cliente']['email']    ?? null,
                        'telefono'  => $data['cliente']['telefono'] ?? null,
                        'direccion' => $data['cliente']['direccion']?? null,
                    ]
                );

                // 2) Cargar tickets
                $tickets = Ticket::whereIn('id', $data['ticket_ids'])->get();

                // 3) (opcional) cargar método de pago
                $paymentMethodId = null;
                if (!empty($data['metodo_pago'])) {
                    $pm = PaymentMethod::where('key', $data['metodo_pago'])->first();
                    $paymentMethodId = $pm->id ?? null;
                }

                // 4) Procesar según acción
                switch ($data['accion']) {
                    case 'apartado':
                        foreach ($tickets as $ticket) {
                            $ticket->cliente()->associate($cliente);
                            $ticket->estado = 'reservado';
                            $ticket->save();
                        }

                        $payload = [
                            'success' => true,
                            'mensaje' => $tickets->count() === 1
                                ? '¡Apartado exitoso!'
                                : '¡Apartados exitosos!',
                        ];

                        if ($tickets->count() === 1) {
                            $t = $tickets->first();
                            $payload['ticket'] = [
                                'id'                  => $t->id,
                                'numero'              => $t->numero,
                                'uuid'                => $t->uuid,
                                'qr_code'             => $t->qr_code,
                                'codigo_verificacion' => $t->codigo_verificacion ?? '',
                                'cliente'             => ['nombre' => $t->cliente?->nombre],
                            ];
                        } else {
                            $payload['tickets'] = $tickets->map(fn($t) => [
                                'id'                  => $t->id,
                                'numero'              => $t->numero,
                                'uuid'                => $t->uuid,
                                'qr_code'             => $t->qr_code,
                                'codigo_verificacion' => $t->codigo_verificacion ?? '',
                                'estado'              => $t->estado,
                                'precio_ticket'       => $t->precio_ticket,
                                'nombre_rifa'         => $t->rifa?->nombre,
                                'monto_rifa'          => $t->rifa?->monto,
                                'cliente'             => ['nombre' => $t->cliente?->nombre],
                            ])->all();
                        }

                        $result = $payload;
                        break;

                    case 'vender':
                        // Validar pago
                        $request->validate([
                            'metodo_pago' => 'required|string',
                            'pago_datos'  => 'required|array',
                        ]);

                        $vendidos = [];
                        foreach ($tickets as $ticket) {
                            $ticket->cliente()->associate($cliente);
                            $ticket->estado = 'vendido';
                            $ticket->save();

                            Abono::create([
                                'ticket_id'           => $ticket->id,
                                'tipo'                => 'vender',
                                'monto'               => $ticket->precio_ticket,
                                'metodo_pago'         => $data['metodo_pago'],
                                'telefono'            => $data['pago_datos']['telefono']   ?? null,
                                'cedula'              => $data['pago_datos']['ci_rif']     ?? null,
                                'titular'             => $data['pago_datos']['titular']    ?? null,
                                'payment_method_id'   => $paymentMethodId,
                                'cuenta_admin_destino'=> $this->formatearCuentaDestino(
                                    PaymentMethod::find($paymentMethodId),
                                    (array) $data['pago_datos'],
                                    $data['metodo_pago']
                                ),
                            ]);

                            $vendidos[] = [
                                'id'      => $ticket->id,
                                'numero'  => $ticket->numero,
                                'uuid'    => $ticket->uuid,
                                'qr_code' => $ticket->qr_code,
                                'cliente' => ['nombre' => $ticket->cliente?->nombre],
                            ];
                        }

                        $result = [
                            'success' => true,
                            'mensaje' => 'Venta registrada correctamente.',
                            'tickets' => $vendidos,
                        ];
                        break;

                    case 'abono':
                        // Validación específica de abono
                        $request->validate([
                            'monto_abono'  => 'required|numeric|min:0.01',
                            'abono_global' => 'required|boolean',
                            'ticket_id'    => 'nullable|integer|exists:tickets,id',
                        ]);

                        if ($data['abono_global']) {
                            $count     = $tickets->count();
                            $perTicket = round($data['monto_abono'] / $count, 2);

                            foreach ($tickets as $ticket) {
                                $ticket->cliente()->associate($cliente);
                                $ticket->estado = 'abonado';
                                $ticket->save();

                                Abono::create([
                                    'ticket_id' => $ticket->id,
                                    'tipo'      => 'abono',
                                    'monto'     => $perTicket,
                                ]);
                            }

                            $result = [
                                'success' => true,
                                'mensaje' => 'Abono global aplicado correctamente.',
                                'tickets' => $tickets->map(fn($t) => [
                                    'id'      => $t->id,
                                    'numero'  => $t->numero,
                                    'uuid'    => $t->uuid,
                                    'qr_code' => $t->qr_code,
                                    'cliente' => ['nombre' => $t->cliente?->nombre],
                                ])->all(),
                            ];
                        } else {
                            // Abono específico
                            $t = Ticket::findOrFail($data['ticket_id']);
                            $t->cliente()->associate($cliente);
                            $t->estado = 'abonado';
                            $t->save();
                            Abono::create([
                                'ticket_id' => $t->id,
                                'tipo'      => 'abono',
                                'monto'     => $data['monto_abono'],
                            ]);

                            foreach ($tickets->where('id', '!=', $data['ticket_id']) as $other) {
                                $other->cliente()->associate($cliente);
                                $other->estado = 'reservado';
                                $other->save();
                            }

                            $result = [
                                'success' => true,
                                'mensaje' => 'Abono aplicado correctamente.',
                                'ticket'  => [
                                    'id'      => $t->id,
                                    'numero'  => $t->numero,
                                    'uuid'    => $t->uuid,
                                    'qr_code' => $t->qr_code,
                                    'cliente' => ['nombre' => $t->cliente?->nombre],
                                ],
                            ];
                        }
                        break;
                }
            });

            return response()->json($result);
        } catch (\Throwable $e) {
            Log::error('Error en procesarVenta: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'mensaje' => config('app.debug')
                    ? 'Error interno: ' . $e->getMessage()
                    : 'Ha ocurrido un error en el servidor.'
            ], 500);
        }
    }

        /**
     * Formatea la cadena para cuenta de destino según método.
     */
    private function formatearCuentaDestino($metodo, array $details, string $key): string
    {
        if (!$metodo) {
            return '';
        }
        if ($key === 'zelle') {
            return sprintf(
                'Correo: %s | Titular: %s',
                $details['correo'] ?? '',
                $details['titular'] ?? ''
            );
        }
        return sprintf(
            'Banco: %s | Titular: %s | Cuenta: %s | CI/RIF: %s',
            $details['banco']   ?? '',
            $details['titular'] ?? '',
            $details['cuenta']  ?? '',
            $details['ci_rif']  ?? ''
        );
    }

    /**
     * AJAX: Valida si la referencia de pago ya existe (para evitar duplicados)
     */
    public function validarReferencia(Request $request)
    {
        $referencia = $request->input('referencia');
        // Ajusta el campo si en tu tabla se llama diferente
        $exists = \App\Models\Abono::where('referencia', $referencia)->exists();
        return response()->json(['unique' => !$exists]);
    }

}
