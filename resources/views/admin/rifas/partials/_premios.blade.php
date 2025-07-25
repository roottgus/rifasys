<div class="bg-white border shadow rounded-xl px-6 py-6">
  <h2 class="text-2xl font-semibold mb-4 flex items-center gap-2">
    <i class="fas fa-gift text-green-500"></i> Premios Especiales
  </h2>
@forelse($rifa->premiosEspeciales as $premio)
  <div class="mb-6 px-4 py-4 bg-green-50 rounded-lg flex flex-col md:flex-row md:items-center md:justify-between gap-4" x-data="premioModal({{ $premio->id }})">
    <!-- IZQUIERDA: Info premio -->
    <div class="flex-1 min-w-0 flex flex-col">
      <!-- Nombre, premio y abono -->
      <div class="mb-3">
        <span class="font-semibold text-base text-gray-800">{{ ucfirst($premio->tipo_premio) }}</span>
        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full font-semibold text-sm ml-2">
          Premio: ${{ number_format($premio->monto, 2) }}
        </span>
        <span class="text-sm text-gray-500 ml-3">
          Abono mínimo: ${{ number_format($premio->abono_minimo,2) }}
        </span>
      </div>
      <!-- Acciones: igual a Ganador Rifa Principal -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <input
          type="number"
          x-model="numeroGanador"
          placeholder="N° ganac"
          class="w-36 border-2 border-orange-200 rounded-lg px-4 py-2 text-lg focus:ring-2 focus:ring-orange-300"
        >
        <button
          @click="confirmarGanador()"
          class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-lg font-semibold shadow hover:bg-indigo-700 flex items-center gap-2 transition"
          type="button"
        >
          <i class="fas fa-check-circle"></i> Confirmar
        </button>
        <button
          @click="open()"
          class="px-6 py-2 bg-blue-700 text-white rounded-lg text-lg font-semibold shadow hover:bg-blue-800 flex items-center gap-2 transition"
          type="button"
        >
          <i class="fas fa-users"></i> Participantes
        </button>
      </div>
      <!-- Resultado tras confirmar -->
      <template x-if="ganador && ganador.numero && ganador.cliente">
        <div class="text-green-700 font-semibold mt-2">
          Ganador: #<span x-text="ganador.numero"></span> – <span x-text="ganador.cliente"></span>
        </div>
      </template>
      <template x-if="errorGanador">
        <div class="text-red-600 font-medium mt-2" x-text="errorGanador"></div>
      </template>
    </div>
    <!-- DERECHA: Cuadro de detalles igual a header principal -->
<div class="flex-shrink-0 mt-4 md:mt-0">
  <div class="bg-white/80 border border-orange-100 rounded-lg shadow-sm px-6 py-4 grid grid-cols-2 gap-x-8 gap-y-2 min-w-[280px] max-w-[400px]">
    <!-- Columna 1 -->
    <div>
      <div class="flex items-center gap-1 bg-blue-50 text-blue-700 px-2 py-0.5 rounded mb-2 text-sm" title="Lotería de este premio">
        <i class="fas fa-star text-blue-400"></i>
        {{ $premio->loteria?->nombre ?? $rifa->loteria?->nombre ?? 'Sin Lotería' }}
      </div>
      <div class="flex items-center gap-1 bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded text-sm" title="Tipo de lotería de este premio">
        <i class="fas fa-layer-group text-indigo-400"></i>
        {{ $premio->tipoLoteria?->nombre ?? $rifa->tipoLoteria?->nombre ?? 'Sin Tipo' }}
      </div>
    </div>
    <!-- Columna 2 -->
    <div>
      <div class="flex items-center gap-1 text-sm mb-2" title="Fecha del sorteo del premio">
        <i class="fas fa-calendar-alt text-orange-400"></i>
        {{ $premio->fecha_premio ? \Carbon\Carbon::parse($premio->fecha_premio)->format('d M Y') : '-' }}
      </div>
      <div class="flex items-center gap-1 text-sm mb-2" title="Hora del sorteo del premio">
        <i class="fas fa-clock text-orange-400"></i>
        {{ $premio->hora_premio ? substr($premio->hora_premio, 0, 5) : '-' }}
      </div>
    </div>
  </div>
</div>


      {{-- Modal Participantes --}}
      <div
        x-show="openModal"
        x-cloak
        @keydown.escape.window="close()"
        @click.self="close()"
        class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4"
        aria-modal="true"
        role="dialog"
      >
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full animate-fade-in" @click.stop>
          <div class="flex justify-between items-center px-6 py-4 border-b">
            <h3 class="text-lg font-bold text-indigo-700">
              <i class="fas fa-users mr-2"></i> Participantes Premio Especial
            </h3>
            <button
              @click="close()"
              class="text-gray-400 hover:text-red-500 text-2xl leading-none"
              aria-label="Cerrar"
              type="button"
            >&times;</button>
          </div>
          <div class="p-6 max-h-96 overflow-y-auto">
            <template x-if="loading">
              <div class="text-center text-gray-500 py-8">
                <i class="fas fa-spinner fa-spin mr-2"></i> Cargando…
              </div>
            </template>
            <template x-if="error">
              <div class="text-center text-red-600 py-8" x-text="error"></div>
            </template>
            <template x-if="!loading && !error && list.length">
              <ul class="space-y-2">
                <template x-for="item in list" :key="item.numero">
                  <li class="flex justify-between items-center border-b pb-2">
                    <span>
                      <i class="fas fa-ticket-alt mr-1"></i>
                      #<span x-text="item.numero"></span>
                    </span>
                    <span class="text-gray-800" x-text="item.cliente"></span>
                  </li>
                </template>
              </ul>
            </template>
            <template x-if="!loading && !error && !list.length">
              <div class="text-center text-gray-400 py-8">
                <i class="fas fa-user-slash mr-1"></i> No hay participantes con el abono mínimo.
              </div>
            </template>
          </div>
          <div class="px-6 py-4 border-t bg-gray-50 text-right">
            <button
              @click="close()"
              class="px-4 py-1.5 bg-gray-400 hover:bg-gray-600 text-white rounded-md text-base transition"
              type="button"
            >Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  @empty
    <div class="text-gray-400 py-8 text-center">
      No hay premios especiales registrados para esta rifa.
    </div>
  @endforelse
</div>
