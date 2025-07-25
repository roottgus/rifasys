@php
    $ticketsPorRifa = $tickets->groupBy(fn($ticket) => optional($ticket->rifa)->nombre ?? '—');
    $badgeColors = [
        'vendido'    => 'bg-gray-100 text-gray-800 border-gray-300',
        'abonado'    => 'bg-purple-100 text-purple-800 border-purple-300',
        'apartado'   => 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'reservado'  => 'bg-red-100 text-red-800 border-red-300',
        'disponible' => 'bg-green-100 text-green-800 border-green-300',
        'default'    => 'bg-gray-200 text-gray-600 border-gray-400',
    ];
@endphp

@if($ticketsPorRifa->count())
    <div class="flex flex-col gap-7">
        @foreach($ticketsPorRifa as $nombreRifa => $ticketsDeEstaRifa)
            @php
                $numeros = $ticketsDeEstaRifa->pluck('numero')->sort()->values();
                $minNum = $numeros->first();
                $maxNum = $numeros->last();
                $rangeSize = 100;
                $ranges = [];
                for ($start = $minNum; $start <= $maxNum; $start += $rangeSize) {
                    $end = min($start + $rangeSize - 1, $maxNum);
                    $ranges[] = [
                        'start' => str_pad($start, 3, '0', STR_PAD_LEFT),
                        'end'   => str_pad($end, 3, '0', STR_PAD_LEFT),
                        'int_start' => $start,
                        'int_end'   => $end,
                    ];
                }
                $ticketsArray = $ticketsDeEstaRifa->values()->map(function($t) {
                    return [
                        'id' => $t->id,
                        'numero' => $t->numero,
                        'numero_formateado' => $t->numero_formateado ?? str_pad($t->numero, 3, '0', STR_PAD_LEFT),
                        'cliente_nombre' => optional($t->cliente)->nombre,
                        'cliente_id' => optional($t->cliente)->id,
                        'precio_ticket' => $t->precio_ticket,
                        'total_abonado' => $t->total_abonado,
                        'estado' => $t->estado,
                        'fecha' => $t->created_at->format('d M Y'),
                        'premios' => $t->rifa && $t->rifa->premiosEspeciales->count() ? $t->evaluacionPremiosEspeciales() : [],
                        'rifa' => $t->rifa,
                    ];
                })->toArray();
            @endphp

            <div x-data="{
                    open: true,
                    rangoSeleccionado: null,
                    tickets: @js($ticketsArray),
                    ranges: @js($ranges),
                    get ticketsFiltrados() {
                        if (this.rangoSeleccionado === null) return this.tickets;
                        const r = this.ranges[this.rangoSeleccionado];
                        return this.tickets.filter(t =>
                            parseInt(t.numero) >= r.int_start && parseInt(t.numero) <= r.int_end
                        );
                    },
                    verDetalleTicket(ticketId) {
                        window.location.href = '/admin/tickets/' + ticketId;
                    }
                }"
                class="bg-white border rounded-2xl shadow-md px-7 py-6 relative transition hover:shadow-xl"
            >
                <div class="flex flex-row items-center justify-between">
                    <div>
                        <div class="text-lg font-extrabold text-indigo-700 uppercase tracking-wide">
                            {{ $nombreRifa }}
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ $ticketsDeEstaRifa->count() }} ticket{{ $ticketsDeEstaRifa->count() > 1 ? 's' : '' }}
                        </div>
                    </div>
                </div>
                {{-- Filtro de rangos --}}
                <div class="flex flex-wrap gap-2 my-4">
                    <button
                        class="px-3 py-1 rounded-lg font-mono shadow transition border border-primary/30"
                        :class="rangoSeleccionado === null ? 'bg-primary text-white' : 'bg-gray-200 text-primary hover:bg-primary/10'"
                        @click="rangoSeleccionado = null"
                        type="button"
                    >
                        Todos
                    </button>
                    <template x-for="(rango, idx) in ranges" :key="idx">
                        <button
                            class="px-3 py-1 rounded-lg font-mono shadow transition border border-primary/30"
                            :class="rangoSeleccionado === idx ? 'bg-primary text-white' : 'bg-gray-200 text-primary hover:bg-primary/10'"
                            @click="rangoSeleccionado = idx"
                            x-text="`${rango.start} - ${rango.end}`"
                            type="button"
                        ></button>
                    </template>
                </div>
                <div x-show="open" x-transition class="mt-5">
                    <div class="overflow-x-auto rounded-lg shadow-inner border bg-gray-50">
                        <div class="max-h-[520px] overflow-y-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-indigo-100 sticky top-0 z-20">
                                    <tr>
                                        <th class="px-3 py-2 text-left">#</th>
                                        <th class="px-3 py-2 text-left">Cliente</th>
                                        <th class="px-3 py-2 text-left">Precio</th>
                                        <th class="px-3 py-2 text-left">Abono</th>
                                        <th class="px-3 py-2 text-left">Premios Especiales</th>
                                        <th class="px-3 py-2 text-left">Estado</th>
                                        <th class="px-3 py-2 text-left">Fecha</th>
                                        <th class="px-3 py-2 text-left">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <template x-for="ticket in ticketsFiltrados" :key="ticket.id">
                                        <tr>
                                            <td class="px-3 py-2 align-top font-mono font-bold text-indigo-700 whitespace-nowrap flex items-center gap-2" style="vertical-align: top;">
                                                <button
                                                    type="button"
                                                    class="flex items-center gap-2 focus:outline-none hover:underline"
                                                    @click="$dispatch('open-ticket-detail', ticket.id)"
                                                    style="background: none;"
                                                    title="Ver detalle del ticket"
                                                >
                                                    <span x-text="ticket.numero_formateado"></span>
                                                    <span class="text-primary/50" style="font-size:1.4em; line-height:1;">
                                                        <i class="fas fa-ticket-alt"></i>
                                                    </span>
                                                </button>
                                            </td>
                                            <td class="px-3 py-2 align-top">
                                                <template x-if="ticket.cliente_id">
                                                    <a :href="`/admin/clientes/${ticket.cliente_id}/edit`"
                                                       class="text-blue-700 underline hover:text-blue-900 font-semibold"
                                                       title="Ver/editar cliente"
                                                       target="_blank"
                                                       x-text="ticket.cliente_nombre"></a>
                                                </template>
                                                <template x-if="!ticket.cliente_id">
                                                    <span class="text-gray-400">—</span>
                                                </template>
                                            </td>
                                            <td class="px-3 py-2 align-top" x-text="`$${parseFloat(ticket.precio_ticket).toFixed(2)}`"></td>
                                            <td class="px-3 py-2 align-top" x-text="`$${parseFloat(ticket.total_abonado).toFixed(2)}`"></td>
                                            <td class="px-3 py-2 align-top w-64">
                                                <template x-if="ticket.premios && Object.keys(ticket.premios).length">
                                                    <ul class="text-xs space-y-1">
                                                        <template x-for="(resultado, idx) in ticket.premios" :key="idx">
                                                            <li :class="resultado.participa ? 'text-green-700' : 'bg-yellow-50 text-red-600 font-semibold px-2 py-1 rounded'">
                                                                <span x-text="resultado.mensaje"></span>
                                                            </li>
                                                        </template>
                                                    </ul>
                                                </template>
                                                <template x-if="!ticket.premios || !Object.keys(ticket.premios).length">
                                                    <span class="text-gray-400">—</span>
                                                </template>
                                            </td>
                                            <td class="px-3 py-2 align-top">
                                                <template x-if="ticket.estado">
                                                    <span class="inline-block px-3 py-1 text-xs rounded-full border font-bold"
                                                        :class="{
                                                            'bg-gray-100 text-gray-800 border-gray-300': ticket.estado === 'vendido',
                                                            'bg-purple-100 text-purple-800 border-purple-300': ticket.estado === 'abonado',
                                                            'bg-yellow-100 text-yellow-800 border-yellow-300': ticket.estado === 'apartado',
                                                            'bg-red-100 text-red-800 border-red-300': ticket.estado === 'reservado',
                                                            'bg-green-100 text-green-800 border-green-300': ticket.estado === 'disponible',
                                                            'bg-gray-200 text-gray-600 border-gray-400': !['vendido','abonado','apartado','reservado','disponible'].includes(ticket.estado)
                                                        }"
                                                        x-text="ticket.estado.charAt(0).toUpperCase() + ticket.estado.slice(1)"
                                                    ></span>
                                                </template>
                                            </td>
                                            <td class="px-3 py-2 align-top" x-text="ticket.fecha"></td>
                                            <td class="px-3 py-2 whitespace-nowrap align-top">
                                                <div class="flex flex-col sm:flex-row gap-1 sm:gap-2">
                                                    <a :href="`/admin/tickets/${ticket.id}`"
                                                        class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold rounded bg-blue-500 hover:bg-blue-600 text-white shadow focus:outline-none transition-all"
                                                        style="min-width: 85px;"
                                                        title="Ver detalle del ticket"
                                                    >
                                                        <i class="fa-solid fa-eye mr-1"></i>
                                                        Detalle
                                                    </a>
                                                    <a :href="`/admin/tickets/${ticket.id}/edit`"
                                                        class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold rounded bg-green-500 hover:bg-green-600 text-white shadow transition-all"
                                                        style="min-width: 65px;"
                                                        title="Editar ticket">
                                                        <i class="fa-solid fa-pen-to-square mr-1"></i>
                                                        Editar
                                                    </a>
                                                    <form :action="`/admin/tickets/${ticket.id}`"
                                                        method="POST" class="inline"
                                                        @submit.prevent="if(confirm('¿Eliminar este ticket?')) $el.submit()">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button type="submit"
                                                                class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold rounded bg-red-500 hover:bg-red-600 text-white shadow transition-all"
                                                                style="min-width: 75px;"
                                                        >
                                                            <i class="fa-solid fa-trash mr-1"></i>
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center text-gray-400 py-16">
        <i class="fas fa-ticket-alt fa-2x mb-2"></i>
        <div class="mt-2 text-lg">No hay tickets encontrados para este filtro.</div>
    </div>
@endif
