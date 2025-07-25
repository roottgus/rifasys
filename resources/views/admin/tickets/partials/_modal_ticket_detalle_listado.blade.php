<div 
    x-data="modalDetalleTicketListado()" 
    x-init="init()"
    x-show="open" 
    x-cloak 
    x-ref="modalDetalleListado"
    class="fixed inset-0 flex items-center justify-center bg-black/60 z-50"
    @keydown.escape.window="cerrar()"
>
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 md:p-8 relative animate-fade-in">

        {{-- Botón cerrar --}}
        <button class="absolute top-4 right-4 text-gray-400 hover:text-primary" @click="cerrar()" aria-label="Cerrar">
            <i class="fa-solid fa-times fa-lg"></i>
        </button>

        <template x-if="ticket">
            <div>
                <div class="flex flex-col md:flex-row md:items-start gap-6 mb-4">
                    {{-- Resumen --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="bg-gray-400 text-white font-bold px-3 py-1 rounded-tl-2xl rounded-br-2xl text-xs uppercase shadow"
                                :class="{
                                    'bg-gray-400': ticket.estado === 'vendido',
                                    'bg-orange-400': ticket.estado === 'reservado',
                                    'bg-purple-400': ticket.estado === 'abonado',
                                    'bg-green-400': ticket.estado === 'disponible'
                                }"
                                x-text="ticket.estado">
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-2xl font-black text-primary flex items-center gap-2 mb-1 drop-shadow-sm">
                                <i class="fa-solid fa-ticket-alt"></i>
                                Detalle de Ticket
                            </h2>
                            <span class="font-mono text-primary-900 bg-primary/10 px-3 py-1 rounded-xl border border-primary/10 shadow ml-2 text-lg">
                                <i class="fa-solid fa-hashtag"></i>
                                <span x-text="padNum(ticket.numero)"></span>
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-2 justify-start">
                            <div class="px-3 py-2 rounded-lg border-2 border-gray-200 bg-white flex flex-col items-center">
                                <span class="text-xs font-medium text-gray-500">Valor Ticket</span>
                                <span class="font-mono font-black text-lg">
                                    $<span x-text="Number(ticket.precio_ticket).toFixed(2)"></span>
                                </span>
                            </div>
                            <div class="px-3 py-2 rounded-lg border-2 border-green-200 bg-green-50 flex flex-col items-center">
                                <span class="text-xs font-medium text-green-700">Total abonado</span>
                                <span class="font-mono font-black text-green-700 text-lg">
                                    $<span x-text="Number(ticket.total_abonado).toFixed(2)"></span>
                                </span>
                            </div>
                            <div class="px-3 py-2 rounded-lg border-2 border-red-200 bg-red-50 flex flex-col items-center">
                                <span class="text-xs font-medium text-red-700">Saldo pendiente</span>
                                <span class="font-mono font-black text-red-700 text-lg">
                                    $<span x-text="Number(ticket.saldo_pendiente).toFixed(2)"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    {{-- QR --}}
                    <div class="flex flex-col items-center justify-center min-w-[110px]">
                        <div class="border-2 border-primary/20 rounded-xl p-2 bg-white shadow-sm">
                            <img
                                :src="`/admin/tickets/${ticket.id}/qr`"
                                alt="QR del ticket"
                                class="w-24 h-24 object-contain"
                                x-show="ticket.id"
                            >
                        </div>
                        <span class="text-xs text-gray-400 mt-2">Escanéame</span>
                    </div>
                </div>

                {{-- Info Cliente & Pagos --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    {{-- Cliente --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 shadow-sm">
                        <h3 class="text-base font-bold text-primary flex items-center gap-2 mb-2">
                            <i class="fa-solid fa-user"></i> Cliente
                        </h3>
                        <div><b>Nombre:</b> <span x-text="ticket.cliente?.nombre ?? '—'"></span></div>
                        <div><b>Cédula:</b> <span x-text="ticket.cliente?.cedula ?? '—'"></span></div>
                        <div><b>Teléfono:</b> <span x-text="ticket.cliente?.telefono ?? '—'"></span></div>
                        <div><b>Dirección:</b> <span x-text="ticket.cliente?.direccion ?? '—'"></span></div>
                    </div>
                    {{-- Pagos --}}
                    <div>
                        <h3 class="text-base font-bold text-primary flex items-center gap-2 mb-2">
                            <i class="fa-solid fa-credit-card"></i> Pagos
                        </h3>
                        <template x-if="ticket.abonos && ticket.abonos.length">
                            <div class="space-y-2 max-h-44 overflow-y-auto pr-1">
                                <template x-for="(abono, idx) in ticket.abonos" :key="abono.id">
                                    <div class="border border-primary/20 rounded-lg px-4 py-2 bg-primary/5 shadow-sm mb-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <i class="fa-solid fa-money-bill-wave text-green-600" x-show="abono.metodo_pago === 'pago_efectivo'"></i>
                                            <i class="fa-solid fa-mobile-alt text-orange-500" x-show="abono.metodo_pago === 'pago_movil'"></i>
                                            <img src="/images/zelle.svg" alt="Zelle" class="w-6 h-6 inline" x-show="abono.metodo_pago === 'zelle'">
                                            <i class="fa-solid fa-university text-indigo-600" x-show="abono.metodo_pago === 'tran_bancaria_nacional'"></i>
                                            <span class="font-semibold" x-text="nombreMetodoPago(abono.metodo_pago)"></span>
                                            <span class="text-xs text-gray-400 ml-2" x-text="abono.fecha"></span>
                                        </div>
                                        <div class="flex gap-3 flex-wrap text-xs">
                                            <div><span class="font-semibold">Banco:</span> <span x-text="abono.banco ?? '—'"></span></div>
                                            <div><span class="font-semibold">Monto:</span> <span class="text-green-700 font-bold">$<span x-text="abono.monto"></span></span></div>
                                            <div><span class="font-semibold">Referencia:</span> <span x-text="abono.referencia"></span></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!ticket.abonos || !ticket.abonos.length">
                            <div class="text-gray-400">Sin pagos registrados.</div>
                        </template>
                    </div>
                </div>

                {{-- Premios especiales (compactos) --}}
                <div class="mt-4">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fa-solid fa-gift text-yellow-400"></i>
                        <span class="text-sm font-bold text-yellow-700">Premios Especiales</span>
                    </div>
                    <div class="ml-6">
                        <template x-if="ticket.premios && Object.keys(ticket.premios).length">
                            <ul class="space-y-1">
    <template x-for="(premio, key) in ticket.premios" :key="key">
        <li class="flex items-center gap-2 text-xs">
            <i class="fa-solid fa-trophy text-yellow-500" x-show="premio.ganador"></i>
            <span x-text="premio.mensaje"></span>
            <span class="ml-2 text-2xs font-bold px-2 py-0.5 rounded"
                :class="{
                    'bg-green-100 text-green-700': premio.participa && !premio.ganador,
                    'bg-yellow-200 text-yellow-900': !premio.participa,
                    'bg-yellow-400 text-white': premio.ganador
                }"
            >
                <span x-show="premio.ganador">🏆 Ganador</span>
                <span x-show="premio.participa && !premio.ganador">Participa</span>
                <span x-show="!premio.participa && !premio.ganador">No participa</span>
            </span>
        </li>
    </template>
</ul>

                        </template>
                        <template x-if="!ticket.premios || !Object.keys(ticket.premios).length">
                            <div class="text-gray-400 text-xs">No aplica a premios especiales.</div>
                        </template>
                    </div>
                </div>

                {{-- Botones pie --}}
                <div class="mt-7 flex flex-col sm:flex-row gap-3 justify-between items-center">
                    <a :href="`/admin/tickets/${ticket.id}/pdf`"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-xl shadow hover:bg-primary/80 hover:scale-105 transition-all focus:ring-2 focus:ring-primary"
                    >
                        <i class="fa-solid fa-print mr-2"></i> Volver a Imprimir
                    </a>
                    <button
                        @click="cerrar()"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl shadow hover:bg-gray-200 hover:scale-105 transition-all"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </template>

        {{-- Loader y error --}}
        <template x-if="loading">
            <div class="flex flex-col items-center py-10 space-y-3">
                <div class="w-20 h-6 rounded bg-gray-200 animate-pulse"></div>
                <div class="w-40 h-4 rounded bg-gray-100 animate-pulse"></div>
                <div class="w-full h-36 rounded-xl bg-gray-50 animate-pulse mt-4"></div>
            </div>
        </template>
        <template x-if="error">
            <div class="text-red-600 font-semibold text-center py-10" x-text="error"></div>
        </template>
    </div>
</div>


<script>
function modalDetalleTicketListado() {
    return {
        open: false,
        loading: false,
        error: null,
        ticket: null,
        abrir(id) {
            this.open = true;
            this.loading = true;
            this.error = null;
            this.ticket = null;
            fetch(`/admin/tickets/${id}/detalle`)
                .then(r => r.json())
                .then(json => {
                    this.ticket = json;
                    this.loading = false;
                })
                .catch(() => {
                    this.error = 'No se pudo cargar el detalle del ticket.';
                    this.loading = false;
                });
        },
        cerrar() {
            this.open = false;
            this.ticket = null;
            this.error = null;
        },
        padNum(num) { 
            if (num === null || num === undefined) return '—';
            return String(num).padStart(3, '0');
        },
        nombreMetodoPago(metodo) {
            const nombres = {
                'pago_movil': 'Pago Móvil',
                'zelle': 'Zelle',
                'tran_bancaria_nacional': 'Transferencia Nacional',
                'pago_efectivo': 'Efectivo',
            };
            return nombres[metodo] || metodo;
        },
        init() {
       window.addEventListener('open-ticket-detail', e => {
           if (e.detail) {
               this.abrir(e.detail);
           }
       });
        }
    }
}
</script>