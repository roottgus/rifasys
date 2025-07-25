<div class="bg-white rounded-2xl shadow-2xl p-6 mt-6 border border-gray-100">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-extrabold tracking-tight flex items-center gap-2" style="color: {{ $primaryColor }}">
            <i class="fa-solid fa-clock-rotate-left mr-2"></i>
            Últimos Tickets
        </h3>
        <span class="text-xs text-gray-400 font-medium uppercase tracking-wider flex items-center gap-1">
            <i class="fa-solid fa-sync-alt animate-spin text-gray-300"></i>
            Actualizado {{ now()->diffForHumans() }}
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full rounded-lg overflow-hidden shadow-sm text-[15px]">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-3 py-2 text-left font-bold text-gray-600 uppercase w-20">#</th>
                    <th class="px-3 py-2 text-left font-bold text-gray-600 uppercase">Cliente</th>
                    <th class="px-3 py-2 text-left font-bold text-gray-600 uppercase">Rifa</th>
                    <th class="px-3 py-2 text-left font-bold text-gray-600 uppercase">Tipo</th>
                    <th class="px-3 py-2 text-left font-bold text-gray-600 uppercase">Monto Pagado</th>
                    <th class="px-3 py-2 text-left font-bold text-gray-600 uppercase">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($ultimosTickets as $ticket)
                    <tr class="hover:bg-indigo-50/70 hover:shadow-lg hover:scale-[1.01] transition-all duration-200 group">
                        <td class="px-3 py-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-indigo-100 text-indigo-700 font-bold text-base shadow-sm min-w-[52px] justify-center group-hover:bg-indigo-200 transition-colors duration-200">
                                <i class="fa-solid fa-ticket-alt mr-1 text-indigo-400 text-sm"></i>
                                {{ str_pad($ticket->numero, 3, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 font-medium text-gray-800 truncate max-w-[160px]">
                            <i class="fa-solid fa-user text-gray-300 mr-1"></i>
                            {{ $ticket->cliente?->nombre ?? '—' }}
                        </td>
                        <td class="px-3 py-2 text-gray-700 truncate max-w-[120px]">
                            <i class="fa-solid fa-gift text-gray-300 mr-1"></i>
                            {{ $ticket->rifa?->nombre ?? '—' }}
                        </td>
                        {{-- TIPO DE VENTA / ESTADO --}}
                        <td class="px-3 py-2">
                            @php
                                $venta = $ticket->tipo_venta ?? $ticket->estado ?? 'venta_total';
                                $tipos = [
                                    'venta_total' => ['label' => 'Venta Total', 'color' => 'bg-gray-500 text-white'],
                                    'vendido'     => ['label' => 'Venta Total', 'color' => 'bg-gray-500 text-white'],
                                    'abono'       => ['label' => 'Abono',      'color' => 'bg-purple-500 text-white'],
                                    'abonado'     => ['label' => 'Abonado',    'color' => 'bg-purple-500 text-white'],
                                    'apartado'    => ['label' => 'Apartado',   'color' => 'bg-orange-500 text-white'],
                                    'reservado'   => ['label' => 'Reservado',  'color' => 'bg-orange-500 text-white'],
                                    'disponible'  => ['label' => 'Disponible', 'color' => 'bg-green-400 text-white'],
                                ];
                                $badge = $tipos[$venta] ?? ['label' => ucfirst($venta), 'color' => 'bg-slate-300 text-gray-700'];
                            @endphp
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold shadow transition-all duration-200 group-hover:scale-105 {{ $badge['color'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </td>
                        {{-- MONTO PAGADO SOLO DEL ÚLTIMO ABONO --}}
                        <td class="px-3 py-2">
                            @php
                                $abono = $ticket->abonos->first();
                                $monto = $abono ? $abono->monto : 0;
                            @endphp
                            <span class="inline-block bg-green-50 text-green-700 font-bold px-3 py-1 rounded-full shadow text-base min-w-[70px] text-center group-hover:bg-green-100 group-hover:scale-110 transition-all duration-200">
                                ${{ number_format($monto, 2) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-gray-500">
                            <i class="fa-solid fa-calendar-day text-gray-300 mr-1"></i>
                            {{ $ticket->updated_at->format('d M Y, H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-5 text-center text-gray-400 font-semibold">
                            <i class="fa-solid fa-ticket-slash text-2xl"></i>
                            No hay tickets recientes.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
