<div class="max-w-5xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    {{-- 1) Nombre + ESTADO --}}
    <div class="bg-white rounded-2xl shadow-md p-5 flex items-center gap-4 border-2 border-primary/10">
        <div class="p-3 bg-primary/10 text-primary rounded-full shadow-sm">
            <i class="fas fa-tag fa-lg"></i>
        </div>
        <div>
            <div class="flex items-center gap-2 mb-1">
                <p class="text-xl font-bold text-gray-800 mb-0" x-text="getRifaNombre()">--</p>
                <span
                    :class="[
                        'ml-2 px-3 py-1 rounded-full text-xs font-bold border shadow transition-all',
                        (getRifa() && getRifa().fecha_sorteo && new Date(getRifa().fecha_sorteo) < new Date())
                            ? 'bg-red-100 text-red-700 border-red-400 animate-pulse'
                            : 'bg-green-100 text-green-700 border-green-400 blink'
                    ]"
                    x-text="(getRifa() && getRifa().fecha_sorteo && new Date(getRifa().fecha_sorteo) < new Date()) ? 'FINALIZADA' : 'ACTIVA'">
                </span>
            </div>
            <p class="text-xs font-semibold text-primary uppercase tracking-wide mb-0.5">Rifa seleccionada</p>
        </div>
    </div>

    {{-- 2) Fecha · Hora · Lotería · Precio --}}
    <div class="bg-white rounded-2xl shadow-md p-5 flex items-start gap-4 border-2 border-primary/10">
        <div class="p-3 bg-emerald-100 text-emerald-600 rounded-full shadow-sm">
            <i class="fas fa-calendar-day fa-lg"></i>
        </div>
        <div class="text-gray-800 text-sm space-y-1">
            <p>
                <span class="font-medium text-primary">Sorteo:</span>
                <span x-text="getRifaFechaSorteo() + (getRifaHoraSorteo() ? ' · ' + getRifaHoraSorteo() : '')">--</span>
            </p>
            <p>
                <span class="font-medium text-primary">Lotería:</span>
                <span x-text="getRifaLoteria()">--</span>
                <small class="text-gray-500">( <span x-text="getRifaTipoLoteria()">--</span> )</small>
            </p>
            <p>
                <span class="font-medium text-primary">Precio:</span>
                <span class="font-bold text-emerald-600" x-text="'$' + getRifaPrecio()">0.00</span>
            </p>
        </div>
    </div>

    {{-- 3) Premios especiales --}}
    <div
        class="bg-white rounded-2xl shadow-md p-5 flex items-center gap-4 border-2 border-yellow-100 cursor-pointer hover:bg-yellow-50 transition group"
        @click="openPremiosModal()"
        title="Ver detalles de premios especiales"
    >
        <div class="p-3 bg-yellow-100 text-yellow-600 rounded-full shadow-sm group-hover:bg-yellow-200 group-hover:text-yellow-700 transition">
            <i class="fas fa-gift fa-lg"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wide mb-0.5">Premios Especiales</p>
            <div class="flex items-end gap-2">
                <p class="text-2xl font-extrabold text-gray-800 mb-0" x-text="countPremios()">0</p>
                <span class="text-xs text-gray-500">ver</span>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Premios Especiales -->
<div
  x-show="premiosModalOpen"
  x-cloak
  class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
  @keydown.escape.window="closePremiosModal()"
  @click.self="closePremiosModal()"
>
  <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-7 border-t-4 border-yellow-400">
    <h3 class="text-2xl font-extrabold mb-4 text-yellow-600 flex items-center gap-2">
      <i class="fas fa-gift"></i> Premios Especiales
    </h3>
    <template x-if="countPremios()">
      <div class="space-y-4 max-h-96 overflow-y-auto">
        <template x-for="p in getPremiosEspeciales()" :key="p.id">
          <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 shadow-sm hover:shadow-md transition">
            <div class="font-bold text-yellow-800 text-lg mb-1 flex items-center gap-2">
              <i class="fas fa-trophy"></i>
              <span x-text="p.tipo_premio"></span>
            </div>
            <div class="text-sm text-gray-700 mb-1" x-text="'Premio: $' + parseFloat(p.monto).toFixed(2)"></div>
            <div class="text-xs text-gray-500 mb-1"
                 x-text="'Lotería: ' + ((p.loteria && p.loteria.nombre) ? p.loteria.nombre : (getRifaLoteria() || 'Sin Lotería'))"></div>
            <div class="text-xs text-gray-500 mb-1"
                 x-text="'Tipo: ' + ((p.tipo_loteria && p.tipo_loteria.nombre) ? p.tipo_loteria.nombre : (getRifaTipoLoteria() || 'Sin Tipo'))"></div>
            <div class="text-xs text-gray-500 mb-1"
                 x-text="'Fecha Sorteo: ' + formatPremioFecha(p.fecha_premio, p.hora_premio)"></div>
            <template x-if="p.detalle_articulo">
              <div class="text-xs text-gray-600 italic" x-text="'Detalle: ' + p.detalle_articulo"></div>
            </template>
          </div>
        </template>
      </div>
    </template>
    <template x-if="!countPremios()">
      <div class="text-gray-400 text-center py-10">No hay premios especiales registrados.</div>
    </template>
    <div class="text-right mt-6">
      <button @click="closePremiosModal()" class="px-5 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-semibold shadow transition">
        Cerrar
      </button>
    </div>
  </div>
</div>
