@extends('layouts.admin')
@section('title', 'Detalle de Ticket')

@section('content')
<div class="w-full min-h-screen bg-gray-100 py-10 px-2">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-8">
        <!-- CARD PRINCIPAL -->
        <div class="flex-1">
            <div class="bg-white rounded-3xl shadow-xl p-8">
                <!-- Botón Volver -->
                <a href="{{ url('/admin/tickets') }}" 
   class="inline-flex items-center gap-2 px-4 py-2 mb-4 rounded-xl bg-gray-100 hover:bg-indigo-100 text-indigo-700 font-semibold text-sm transition-all shadow-sm border border-gray-200">
    <i class="fas fa-arrow-left"></i>
    Volver
</a>

                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-6 gap-2">
                    <div>
                        
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-xl font-bold text-primary tracking-wide">
                                TICKET N° <span class="font-mono text-2xl text-indigo-700">{{ str_pad($ticket->numero, $padLength, '0', STR_PAD_LEFT) }}</span>
                            </span>
                            <span class="ml-2 text-xs px-3 py-1 rounded-full font-bold
                                @if($ticket->estado == 'abonado') bg-orange-100 text-orange-700
                                @elseif($ticket->estado == 'vendido') bg-green-100 text-green-700
                                @else bg-red-100 text-red-700 @endif uppercase">
                                {{ strtoupper($ticket->estado) }}
                            </span>
                        </div>
                        <!-- NUEVO BLOQUE DE DETALLES EN DOS COLUMNAS -->
                        <div class="bg-white/80 border border-gray-100 rounded-lg shadow-sm px-5 py-3 mb-3 max-w-2xl">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 gap-x-8 text-sm text-gray-700">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-ticket-alt text-indigo-500"></i>
                                    <span>
                                        <strong>Rifa:</strong>
                                        <span class="font-medium">{{ $ticket->rifa->nombre }}</span>
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-orange-500"></i>
                                    <span>
                                        <strong>Fecha sorteo:</strong>
                                        {{ \Carbon\Carbon::parse($ticket->rifa->fecha_sorteo)->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-layer-group text-blue-500"></i>
                                    <span>
                                        <strong>Lotería:</strong>
                                        <span class="font-medium">
                                            {{ $ticket->rifa->loteria?->nombre ?? 'Sin Lotería' }}
                                        </span>
                                    </span>
                                </div>
                                @php
    $hora = \Carbon\Carbon::parse($ticket->rifa->fecha_sorteo)->format('H:i');
@endphp
<div class="flex items-center gap-1">
    <span class="inline-flex items-center bg-blue-100 text-blue-800 rounded px-2 py-0.5 text-xs font-semibold">
        <i class="fas fa-clock mr-1"></i> Hora: {{ $hora != '00:00' ? $hora : 'Por definir' }}
    </span>
</div>

                                <div class="flex items-center gap-2">
                                    <i class="fas fa-tag text-green-500"></i>
                                    <span>
                                        <strong>Tipo de Lotería:</strong>
                                        <span class="font-medium">
                                            {{ $ticket->rifa->tipoLoteria?->nombre ?? 'Sin Tipo' }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- /FIN NUEVO BLOQUE DE DETALLES -->
                    </div>
                    <!-- QR MÁS PEQUEÑO -->
                    <div class="flex flex-col items-center">
                        <div class="rounded-lg border border-gray-200 bg-white p-1 shadow flex justify-center items-center">
                            <div class="w-[90px] h-[90px] flex items-center justify-center">
                                {!! $qr_svg ?? '' !!}
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">Escanéame</span>
                    </div>
                </div>

                <!-- Tarjetas resumen -->
                <div class="flex flex-col sm:flex-row gap-4 mb-6">
                    <div class="flex-1 bg-white border rounded-xl flex flex-col items-center justify-center py-5 shadow-sm">
                        <div class="text-xs text-gray-500">VALOR TICKET</div>
                        <div class="text-2xl font-bold text-gray-800">${{ number_format($precioTicket, 2) }}</div>
                    </div>
                    <div class="flex-1 bg-white border border-green-300 rounded-xl flex flex-col items-center justify-center py-5 shadow-sm">
                        <div class="text-xs text-green-600">TOTAL ABONADO</div>
                        <div class="text-2xl font-bold text-green-700">${{ number_format($totalAbonado, 2) }}</div>
                    </div>
                    <div class="flex-1 bg-white border border-red-300 rounded-xl flex flex-col items-center justify-center py-5 shadow-sm">
                        <div class="text-xs text-red-600">SALDO PENDIENTE</div>
                        <div class="text-2xl font-bold text-red-700">${{ number_format($saldoPendiente, 2) }}</div>
                    </div>
                </div>

                <!-- Cliente y Premios especiales -->
                <div class="flex flex-col gap-3">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-2">
                        <div class="flex items-center gap-2 text-blue-700 font-bold mb-2">
                            <i class="fa fa-user"></i> Cliente
                        </div>
                        <div class="text-sm"><b>Nombre:</b> {{ $ticket->cliente->nombre ?? '—' }}</div>
                        <div class="text-sm"><b>Cédula:</b> {{ $ticket->cliente->cedula ?? '—' }}</div>
                        <div class="text-sm"><b>Teléfono:</b> {{ $ticket->cliente->telefono ?? '—' }}</div>
                        <div class="text-sm"><b>Dirección:</b> {{ $ticket->cliente->direccion ?? '—' }}</div>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center gap-2">
                        <i class="fa fa-gift text-yellow-600 text-xl"></i>
                        <div>
                            <div class="font-bold text-yellow-700">Premios especiales:</div>
                            @if(!empty($premios))
                                <ul class="text-xs mt-1">
                                    @foreach($premios as $premio)
                                        <li class="{{ $premio['participa'] ? 'text-green-700' : 'text-red-600' }}">
                                            {!! $premio['mensaje'] !!}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-400 text-xs">No aplica premios especiales.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD DERECHA: PAGOS Y ABONOS -->
        <div class="w-full md:w-[450px] shrink-0">
            <div class="bg-white border rounded-3xl shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="font-bold text-green-900 text-lg flex items-center gap-2">
                        <i class="fa-solid fa-money-bill-wave"></i> Pagos y abonos
                    </div>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-1 rounded-lg shadow transition-all flex items-center gap-1">
                        <i class="fa fa-print"></i> Imprimir recibo
                    </button>
                </div>

                <!-- Historial abonos (Alpine.js envuelve todo el bloque) -->
<div x-data="{ abonoSeleccionado: null }"
     x-init="$watch('abonoSeleccionado', v => { if(v){ document.body.style.overflow = 'hidden'; } else { document.body.style.overflow = ''; } })">
    @if($abonos->count())
    <div x-data="{ abonoSeleccionado: null }">
        @foreach($abonos as $abono)
            <div class="rounded-xl border border-green-300 bg-green-50 px-4 py-2 mb-3 flex flex-col gap-1 relative">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="bg-green-600 text-white text-xs px-2 py-1 rounded-md font-semibold">
                        {{ strtoupper($abono->metodo_pago) }}
                    </span>
                    <span class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($abono->fecha_pago)->format('d/m/Y H:i') }}</span>
                    <span class="text-xs text-green-800 ml-auto font-bold">${{ number_format($abono->monto,2) }}</span>
                    <!-- Botón ver detalles -->
                    <button @click="abonoSeleccionado = {{ $abono->id }}" type="button"
                            class="ml-2 text-green-700 hover:text-green-900 p-1 transition rounded-full"
                            title="Ver detalles del abono">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
                <div class="text-xs text-gray-500">
                    Banco: <b>{{ $abono->banco ?? '-' }}</b>
                    | Ref.: <b>{{ $abono->referencia ?? '-' }}</b>
                </div>

                <!-- Modal para este abono -->
                <div x-show="abonoSeleccionado === {{ $abono->id }}" style="display: none"
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
                    <div class="bg-white rounded-2xl shadow-2xl p-7 w-full max-w-md relative"
                         @keydown.escape.window="abonoSeleccionado = null" tabindex="0">
                        <button @click="abonoSeleccionado = null"
                                class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-lg">
                            <i class="fa fa-times"></i>
                        </button>
                        <h2 class="font-bold text-xl text-green-700 mb-4 flex items-center gap-2">
                            <i class="fa fa-file-invoice-dollar"></i>
                            Detalle del abono
                        </h2>
                        <div class="space-y-3 text-base">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-gray-700">Monto:</span>
                                <span class="text-2xl font-bold text-green-600">${{ number_format($abono->monto, 2) }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Método de pago:</span>
                                <span class="inline-block px-2 py-0.5 rounded bg-green-100 text-green-800 text-xs font-semibold uppercase">
                                    {{ $abono->metodo_pago }}
                                </span>
                            </div>
                            @if($abono->referencia)
                                <div>
                                    <span class="font-semibold text-gray-700">Referencia:</span>
                                    <span class="text-gray-900">{{ $abono->referencia }}</span>
                                </div>
                            @endif
                            @if($abono->banco)
                                <div>
                                    <span class="font-semibold text-gray-700">Banco:</span>
                                    <span class="text-gray-900">{{ $abono->banco }}</span>
                                </div>
                            @endif
                            @if($abono->telefono)
                                <div>
                                    <span class="font-semibold text-gray-700">Teléfono:</span>
                                    <span class="text-gray-900">{{ $abono->telefono }}</span>
                                </div>
                            @endif
                            @if($abono->cedula)
                                <div>
                                    <span class="font-semibold text-gray-700">Cédula:</span>
                                    <span class="text-gray-900">{{ $abono->cedula }}</span>
                                </div>
                            @endif
                            @if($abono->correo)
                                <div>
                                    <span class="font-semibold text-gray-700">Correo:</span>
                                    <span class="text-gray-900">{{ $abono->correo }}</span>
                                </div>
                            @endif
                            <div>
                                <span class="font-semibold text-gray-700">Fecha:</span>
                                <span class="text-gray-900">{{ \Carbon\Carbon::parse($abono->fecha_pago)->format('d/m/Y H:i') }}</span>
                            </div>
                            @if($abono->lugar_pago)
                                <div>
                                    <span class="font-semibold text-gray-700">Lugar de pago:</span>
                                    <span class="text-gray-900">{{ $abono->lugar_pago }}</span>
                                </div>
                            @endif
                            @if($abono->nota)
                                <div>
                                    <span class="font-semibold text-gray-700">Nota:</span>
                                    <span class="text-gray-900">{{ $abono->nota }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- /modal -->
            </div>
        @endforeach
    </div>
@else
    <div class="text-gray-400 italic mb-4">No hay abonos registrados.</div>
@endif


    <!-- Formulario de abono -->
    @include('admin.tickets._form_abono')
</div>

            </div>
        </div>
    </div>
</div>
@endsection
