@extends('layouts.admin')

@section('title', 'Venta de Tickets')

{{-- Estilos especiales de animación y badges --}}
@include('admin.tickets.partials._rifa_styles')


@section('content')
<script>
    window.rifasData = @json($rifas);
    window.metodosPagoActivos = @json($metodosPagoActivos);
    window.ticketsData = @json($tickets); 
</script>

<div class="p-6 space-y-8" x-data="salePage(window.rifasData)" x-init="init()">
    {{-- Select de rifas --}}
    <div class="flex flex-col sm:flex-row items-center gap-6 mb-8">
        <h2 class="text-xl font-bold text-primary flex items-center gap-2">
            <i class="fas fa-gift"></i>
            Selecciona la Rifa para la Venta
        </h2>
        <div class="flex-1 flex items-center gap-3">
            <div class="relative w-72">
                <select
                    x-model="selectedRifa"
                    @change="onChangeRifa()"
                    class="peer w-full px-4 py-2 pr-10 bg-white border-2 border-primary/40 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary text-gray-800 font-semibold transition"
                >
                    <option value="" disabled selected>-- Elige una rifa --</option>
                    @foreach($rifas as $r)
                        <option value="{{ $r['id'] }}">{{ $r['nombre'] }}</option>
                    @endforeach
                </select>
                <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-primary/70 text-lg">
                    <i class="fas fa-chevron-down"></i>
                </span>
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-primary/70">
                    <i class="fas fa-ticket-alt"></i>
                </span>
            </div>
            <div class="ml-auto">
                @include('admin.tickets.partials._rifa_sticky_badge')
            </div>
        </div>
    </div>

    {{-- Alerta si la rifa está finalizada --}}
    @include('admin.tickets.partials._alerta_finalizada')

    {{-- Tarjetas de filtros --}}
    @include('admin.tickets._cards')

    {{-- Detalles de la rifa seleccionada --}}
    @include('admin.tickets._rifa_details')

    {{-- Notificación última venta --}}
    @include('partials._ultima_venta_nav')

    {{-- Leyenda de colores/tickets --}}
    <div class="flex flex-wrap items-center gap-4 text-sm mb-4">
        <div class="flex items-center gap-2">
            <span class="inline-block w-5 h-5 bg-green-200 rounded"></span>
            <span>Disponible</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-block w-5 h-5 bg-gray-300 rounded"></span>
            <span>Vendido</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-block w-5 h-5 bg-red-300 rounded"></span>
            <span>Reservado</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-block w-5 h-5 bg-purple-200 rounded"></span>
            <span>Abonado</span>
        </div>
        <div class="ml-2 flex items-center gap-1">
            <i class="fas fa-info-circle text-gray-400" title="Colores según estado del ticket"></i>
            <span class="text-xs text-gray-400 hidden sm:inline">Colores según estado</span>
        </div>
    </div>

    {{-- Buscador y exportación --}}
    <div class="flex flex-wrap gap-4 items-center mb-2 relative">
        <div class="relative w-full max-w-lg">
            <input
                type="text"
                x-model="searchGlobal"
                @input="filtrarTicketsAvanzado"
                class="border-2 border-primary/40 rounded-xl px-4 py-2 pl-10 text-sm w-full shadow-sm focus:ring focus:border-primary transition"
                placeholder="Buscar por número, nombre o cédula..."
                autocomplete="off"
            >
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-primary/70 text-lg pointer-events-none">
                <i class="fas fa-search"></i>
            </span>
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs hidden sm:inline">
                Ej: 123, Juan, 12345678
            </span>
        </div>
        <span class="ml-auto text-gray-500 text-xs hidden md:inline">
            Total tickets: <span x-text="filteredTickets.length"></span>
        </span>
        {{-- Dropdown exportación --}}
        <div class="relative ml-2" x-data="{ exportMenu: false }">
            <button
                @click="exportMenu = !exportMenu"
                class="px-2 py-2 bg-gray-700 hover:bg-gray-900 text-white rounded shadow flex items-center gap-1"
                type="button"
                title="Exportar o Imprimir"
            >
                <i class="fas fa-file-export"></i>
                <span class="hidden sm:inline">Exportar / Imprimir</span>
                <i class="fas fa-chevron-down ml-1"></i>
            </button>
            <div
                x-show="exportMenu"
                @click.away="exportMenu = false"
                class="absolute right-0 mt-2 w-56 bg-white rounded shadow-lg z-50 border text-gray-800"
                x-cloak
            >
                <div class="py-2">
                    <button @click="exportarPDF(filter); exportMenu=false" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                        <i class="fas fa-file-pdf text-red-600 mr-2"></i>
                        Exportar PDF (<span x-text="filtroNombre(filter)"></span>)
                    </button>
                    <button @click="imprimirGrid(filter); exportMenu=false" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                        <i class="fas fa-print text-gray-700 mr-2"></i>
                        Imprimir (<span x-text="filtroNombre(filter)"></span>)
                    </button>
                    <div class="border-t my-2"></div>
                    <div class="text-xs text-gray-400 px-4 pb-2">Exportar por estado</div>
                    <template x-for="f in estadosExportar" :key="f.value">
                        <button @click="exportarPDF(f.value); exportMenu=false" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                            <i class="fas fa-file-pdf text-red-600 mr-2"></i>
                            PDF – <span x-text="f.label"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Barra flotante de selección múltiple --}}
    <template x-if="selectedTickets.length > 0">
        <div class="fixed bottom-5 left-1/2 -translate-x-1/2 z-50 bg-white shadow-2xl rounded-xl px-8 py-4 flex items-center gap-8 border-2 border-primary/40" style="min-width:320px;">
            <div class="flex flex-col items-center">
                <span class="font-bold text-lg text-primary" x-text="selectedTickets.length"></span>
                <span class="text-xs text-gray-400">Seleccionados</span>
            </div>
            <div class="flex flex-col items-center">
                <span class="font-bold text-lg text-green-700"
                      x-text="'$' + Number(totalSeleccionados).toLocaleString('es-VE', {minimumFractionDigits:2})"></span>
                <span class="text-xs text-gray-400">Total a pagar</span>
            </div>
            <button
                @click="venderSeleccionados()"
                :disabled="getRifa() && getRifa().fecha_sorteo && (new Date(getRifa().fecha_sorteo) < new Date())"
                class="bg-primary hover:bg-primary/90 text-white px-6 py-2 rounded-lg font-bold shadow flex items-center gap-2"
                :class="getRifa() && getRifa().fecha_sorteo && (new Date(getRifa().fecha_sorteo) < new Date()) ? 'opacity-60 cursor-not-allowed' : ''"
            >
                <i class="fas fa-shopping-cart"></i> Vender seleccionados
            </button>
            <button
                @click="clearSelectedTickets()"
                class="ml-2 px-2 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg shadow"
                title="Limpiar selección"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>
    </template>

    {{-- Grid compacto de tickets --}}
    <div class="relative bg-gray-50 p-3 rounded-lg overflow-auto">
        {{-- Loader overlay --}}
        <div
            x-show="loading"
            class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-70 z-20"
            x-transition.opacity
            style="backdrop-filter: blur(2px);"
        >
            <svg class="animate-spin w-12 h-12 text-primary" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            <span class="ml-4 text-primary font-semibold text-xl">Actualizando...</span>
        </div>

        {{-- Toast/snackbar --}}
        <div x-show="showToast" x-transition
             class="fixed bottom-4 right-4 bg-primary text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2">
            <svg class="w-6 h-6 text-white mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            ¡Tickets actualizados!
        </div>

        <template x-if="loading">
            <p class="text-center text-gray-500 py-4">Cargando…</p>
        </template>

        <template x-if="!loading && filteredTickets.length">
            <div class="grid gap-1" style="grid-template-columns: repeat(auto-fill, minmax(2.5rem, 1fr));">
                <template x-for="t in filteredTickets" :key="t.id">
                    <div
                        @click="
   t.estado === 'disponible'
     && !(getRifa() && getRifa().fecha_sorteo && (new Date(getRifa().fecha_sorteo) < new Date()))
     ? toggleTicketSel(t)
     : openTicketDetail(t)
 "
                        class="w-10 h-10 flex items-center justify-center text-xs font-mono select-none rounded-md cursor-pointer border-2 transition relative"
                        :class="{
                            'bg-gray-300 border-gray-400 text-gray-500': t.estado === 'vendido',
                            'bg-red-300 border-red-400 text-white': t.estado === 'reservado',
                            'bg-purple-200 border-purple-400 text-purple-800': t.estado === 'abonado',
                            'bg-green-200 border-green-400 hover:bg-green-300': t.estado === 'disponible',
                            '!border-4 !border-orange-500 !ring-2 !ring-primary/70 !z-10': t.estado === 'disponible' && selectedTickets.some(pt => pt.id === t.id),
                            'opacity-50 cursor-not-allowed': getRifa() && getRifa().fecha_sorteo && (new Date(getRifa().fecha_sorteo) < new Date())
                        }"
                        :title="t.estado === 'disponible'
                                 ? (getRifa() && new Date(getRifa().fecha_sorteo) < new Date()
                                    ? 'Rifa finalizada'
                                    : (selectedTickets.some(pt => pt.id === t.id)
                                       ? 'Quitar selección'
                                       : 'Seleccionar ticket'))
                                 : ''"
                    >
                        <span x-text="String(t.numero).padStart(padLen,'0')"></span>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="!loading && !filteredTickets.length">
            <p class="text-center text-gray-500 py-4">No hay tickets para esta categoría.</p>
        </template>

        {{-- Cargar más --}}
        <div class="text-center mt-4" x-show="filteredTickets.length >= gridLimit">
            <button
                @click="cargarMas()"
                class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded shadow"
                type="button"
            >
                Cargar más tickets
            </button>
        </div>
    </div>
</div>

{{-- MODAL DE VENTA (independiente del contenedor) --}}
@include('admin.tickets._modal_venta')

{{-- MODAL DE DETALLE --}}
@include('admin.tickets.partials._modal_ticket_detalle')

@endsection
