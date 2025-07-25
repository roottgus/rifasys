<div
    x-show="modalTicketsOpen || modalOpen"
    x-transition:enter="transition-all ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-90"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition-all ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-90"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
>
    <div
        class="bg-white rounded-2xl shadow-2xl p-0 sm:p-0 w-full max-w-2xl relative flex flex-col border border-primary/20"
        @click.away="cerrarModalTickets ? cerrarModalTickets() : (typeof close === 'function' ? close() : null)"
        style="max-height: 90vh;"
        x-data="{
    search: '',
    estado: '',
    pagina: 1,
    porPagina: 15,
    padLength: 3,
            get ticketsFiltrados() {
                let out = Array.isArray(tickets) ? tickets : [];
                if (this.search) {
                    out = out.filter(t =>
                        String(t.numero).includes(this.search) ||
                        (t.rifa_nombre && t.rifa_nombre.toLowerCase().includes(this.search.toLowerCase()))
                    );
                }
                if (this.estado) {
                    out = out.filter(t => t.estado === this.estado);
                }
                return out;
            },
            get totalPaginas() {
                return Math.max(1, Math.ceil(this.ticketsFiltrados.length / this.porPagina));
            },
            get ticketsPagina() {
                const start = (this.pagina - 1) * this.porPagina;
                return this.ticketsFiltrados.slice(start, start + this.porPagina);
            },
            setPagina(p) {
                if (p >= 1 && p <= this.totalPaginas) this.pagina = p;
            },
            resumenEstados() {
                let r = { total: 0, vendido: 0, abonado: 0, reservado: 0, apartado: 0 };
                (Array.isArray(tickets) ? tickets : []).forEach(t => {
                    r.total++;
                    if (r[t.estado] !== undefined) r[t.estado]++;
                });
                return r;
            }
        }"
    >
        <!-- Encabezado PRO -->
        <div class="p-4 flex items-center justify-between border-b bg-gradient-to-r from-primary/10 to-blue-50 rounded-t-2xl sticky top-0 z-10">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-primary/10 text-primary font-bold text-2xl">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <div class="text-lg font-bold text-primary flex items-center gap-1">
                        <span x-text="(typeof clienteTickets !== 'undefined' && clienteTickets && clienteTickets.nombre)
                        ? clienteTickets.nombre
                        : ((typeof cliente !== 'undefined' && cliente && cliente.nombre) ? cliente.nombre : '')"></span>
                    </div>
                    <div class="flex gap-2 mt-1">
                        <template x-if="resumenEstados().total > 0">
                            <span class="text-xs text-gray-500 font-semibold">
                                <i class="fas fa-ticket-alt"></i>
                                <span x-text="resumenEstados().total"></span> tickets
                            </span>
                        </template>
                        <template x-if="resumenEstados().vendido > 0">
                            <span class="text-xs px-2 py-0.5 bg-gray-300 text-green-700 rounded-full font-semibold">
                                Vendidos <span x-text="resumenEstados().vendido"></span>
                            </span>
                        </template>
                        <template x-if="resumenEstados().abonado > 0">
                            <span class="text-xs px-2 py-0.5 bg-purple-300 text-purple-700 rounded-full font-semibold">
                                Abonados <span x-text="resumenEstados().abonado"></span>
                            </span>
                        </template>
                        <template x-if="resumenEstados().reservado > 0">
                            <span class="text-xs px-2 py-0.5 bg-red-300 text-red-700 rounded-full font-semibold">
                                Reservados <span x-text="resumenEstados().reservado"></span>
                            </span>
                        </template>
                        <template x-if="resumenEstados().apartado > 0">
                            <span class="text-xs px-2 py-0.5 bg-orange-100 text-orange-800 rounded-full font-semibold">
                                Apartados <span x-text="resumenEstados().apartado"></span>
                            </span>
                        </template>
                    </div>
                </div>
            </div>
            <button
                class="text-gray-400 hover:text-primary"
                @click="cerrarModalTickets ? cerrarModalTickets() : (typeof close === 'function' ? close() : null)"
            >
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <!-- Filtros internos -->
        <div class="flex flex-col sm:flex-row items-center gap-3 px-4 py-2 border-b bg-white sticky top-[72px] z-10">
            <input
                x-model="search"
                type="text"
                placeholder="Buscar por número o rifa..."
                class="flex-1 px-3 py-1 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary/20 text-sm"
            >
            <select
                x-model="estado"
                class="px-2 py-1 rounded border border-gray-300 focus:ring-primary/20 text-sm"
            >
                <option value="">Todos</option>
                <option value="vendido">Vendido</option>
                <option value="abonado">Abonado</option>
                <option value="reservado">Reservado</option>
                
                <option value="disponible">Disponible</option>
            </select>
        </div>

        <!-- Contenido scrollable -->
        <div class="flex-1 overflow-y-auto p-4" style="max-height: 60vh;">
            <!-- Loading -->
            <template x-if="(typeof ticketsLoading !== 'undefined' ? ticketsLoading : false) || (typeof loading !== 'undefined' ? loading : false)">
                <div class="py-8 flex flex-col items-center text-primary">
                    <i class="fas fa-spinner fa-spin text-2xl"></i>
                    <p class="mt-2">Cargando tickets...</p>
                </div>
            </template>

            <!-- Sin tickets -->
            <template x-if="!((typeof ticketsLoading !== 'undefined' ? ticketsLoading : false) || (typeof loading !== 'undefined' ? loading : false)) && (ticketsFiltrados.length === 0)">
                <div class="py-8 flex flex-col items-center text-gray-400">
                    <i class="fas fa-exclamation-circle text-xl"></i>
                    <p class="mt-2">No tiene tickets registrados con estos filtros.</p>
                </div>
            </template>

            <!-- Tabla de tickets -->
            <template x-if="!((typeof ticketsLoading !== 'undefined' ? ticketsLoading : false) || (typeof loading !== 'undefined' ? loading : false)) && (ticketsFiltrados.length > 0)">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm mb-2 border border-gray-100 rounded">
                        <thead class="sticky top-0 bg-gradient-to-r from-blue-50 to-white z-10">
                            <tr>
                                <th class="font-semibold text-left px-2 py-2">#</th>
                                <th class="font-semibold text-left px-2 py-2">Rifa</th>
                                <th class="font-semibold text-left px-2 py-2">Estado</th>
                                <th class="font-semibold text-left px-2 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="ticket in ticketsPagina" :key="ticket.id">
                                <tr class="hover:bg-primary/5 transition">
                                    <td class="px-2 py-1 font-mono relative text-center w-20" style="min-width: 60px;">
    <span class="relative z-10" x-text="String(ticket.numero).padStart(padLength, '0')"></span>
    <span
        class="absolute inset-0 flex items-center justify-center pointer-events-none z-0"
        style="opacity: 0.10; font-size: 1.7em;"
    >
        <i class="fas fa-ticket-alt"></i>
    </span>
</td>
<td class="px-2 py-1" x-text="ticket.rifa_nombre"></td>
                                    <td class="px-2 py-1">
                                        <span
    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold"
    :class="{
        'bg-gray-300 text-gray-700': ticket.estado === 'vendido',
        'bg-purple-100 text-purple-700': ticket.estado === 'abonado',
        'bg-red-100 text-red-700': ticket.estado === 'reservado',
        'bg-green-100 text-green-700': ticket.estado === 'disponible',
        'bg-orange-100 text-orange-700': ticket.estado === 'apartado',
    }"
>
    <!-- Íconos según estado -->
    <template x-if="ticket.estado === 'vendido'">
    <i class="fas fa-check-circle text-green-600"></i>
</template>

    <template x-if="ticket.estado === 'abonado'"><i class="fas fa-dollar-sign"></i></template>
    <template x-if="ticket.estado === 'reservado'"><i class="fas fa-hand-paper"></i></template>
    <template x-if="ticket.estado === 'apartado'"><i class="fas fa-pause-circle"></i></template>
    <template x-if="ticket.estado === 'disponible'"><i class="fas fa-circle"></i></template>
    <span x-text="ticket.estado"></span>
</span>
                                    </td>
                                    <td class="px-2 py-1">
                                        <a
                                            :href="`/admin/tickets/${ticket.id}`"
                                            class="inline-flex items-center gap-1 bg-primary/10 text-primary px-3 py-1 rounded shadow-sm font-semibold hover:bg-primary/20 transition"
                                            target="_blank"
                                            title="Ver detalle"
                                        >
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>

            <!-- Paginación interna -->
            <template x-if="totalPaginas > 1">
                <div class="flex justify-center mt-4 gap-1">
                    <button
                        class="px-2 py-1 text-sm rounded hover:bg-primary/10"
                        :disabled="pagina === 1"
                        :class="pagina === 1 ? 'text-gray-300' : 'text-primary font-bold'"
                        @click="setPagina(pagina - 1)"
                    >
                        Anterior
                    </button>
                    <template x-for="p in Array.from({length: totalPaginas}, (_, i) => i + 1)">
                        <button
                            class="px-2 py-1 text-sm rounded font-bold"
                            :class="pagina === p ? 'bg-primary text-white' : 'text-primary hover:bg-primary/10'"
                            @click="setPagina(p)"
                            x-text="p"
                        ></button>
                    </template>
                    <button
                        class="px-2 py-1 text-sm rounded hover:bg-primary/10"
                        :disabled="pagina === totalPaginas"
                        :class="pagina === totalPaginas ? 'text-gray-300' : 'text-primary font-bold'"
                        @click="setPagina(pagina + 1)"
                    >
                        Siguiente
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>
