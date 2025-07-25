<div
    x-data="{ show: false, copyText(text) { navigator.clipboard.writeText(text); $dispatch('notify', { message: '¡Copiado!', type: 'success' }); } }"
    x-init="setTimeout(() => show = true, 200)"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    class="relative flex flex-col md:flex-row items-start md:items-center justify-between bg-gradient-to-r from-orange-50 to-yellow-50 border border-orange-100 rounded-xl shadow px-6 py-6 mb-2 cursor-pointer transition-all duration-300 hover:shadow-xl hover:border-orange-200"
    tabindex="0"
    aria-label="Detalles de la rifa {{ $rifa->nombre }}"
>
    <!-- Izquierda: Ícono, Nombre y Precio -->
    <div class="flex items-center gap-4 flex-1 min-w-0 mb-4 md:mb-0">
        <div class="flex-shrink-0 text-4xl text-orange-400 animate-pulse">
            <i class="fas fa-ticket-alt" aria-hidden="true"></i>
        </div>
        <div>
            <h1
                class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900 hover:text-orange-500 transition-colors cursor-pointer"
                @click="copyText('{{ $rifa->nombre }}')"
                title="Clic para copiar el nombre"
            >
                {{ $rifa->nombre }}
            </h1>
            <div class="mt-1">
                <span class="bg-green-100 text-green-800 px-4 py-1 rounded-full font-semibold text-base shadow-sm" title="Precio por ticket">
                    Precio ticket: ${{ number_format($rifa->precio, 2) }}
                </span>
            </div>
            @if(!empty($rifa->estado))
                <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-bold bg-gradient-to-r from-orange-300 to-yellow-300 text-orange-900 shadow"
                      :class="{
                        'bg-green-100 text-green-800': '{{ $rifa->estado }}' === 'activa',
                        'bg-gray-200 text-gray-600': '{{ $rifa->estado }}' === 'finalizada',
                        'bg-yellow-200 text-yellow-800': '{{ $rifa->estado }}' === 'en venta'
                      }"
                      title="Estado de la rifa"
                >
                    {{ ucfirst($rifa->estado) }}
                </span>
            @endif
        </div>
    </div>
    <!-- Derecha: Bloques tipo grid EN TARJETA ESPECIAL -->
    <div class="w-full md:w-auto">
        <div class="bg-white/80 border border-orange-100 rounded-lg shadow-sm px-6 py-4 grid grid-cols-2 gap-x-8 gap-y-2 min-w-[280px] max-w-[400px]">
            <!-- Columna 1 -->
            <div>
                <div class="flex items-center gap-1 bg-blue-50 text-blue-700 px-2 py-0.5 rounded mb-2 text-sm" title="Lotería asociada a esta rifa">
                    <i class="fas fa-star text-blue-400"></i>
                    {{ $rifa->loteria->nombre ?? 'Sin Lotería' }}
                </div>
                <div class="flex items-center gap-1 bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded text-sm" title="Tipo de sorteo de la lotería">
                    <i class="fas fa-layer-group text-indigo-400"></i>
                    {{ $rifa->tipoLoteria->nombre ?? 'Sin Tipo' }}
                </div>
            </div>
            <!-- Columna 2 -->
            <div>
                <div class="flex items-center gap-1 text-sm mb-2" title="Fecha del sorteo">
                    <i class="fas fa-calendar-alt text-orange-400" aria-hidden="true"></i>
                    {{ \Carbon\Carbon::parse($rifa->fecha_sorteo)->format('d M Y') }}
                </div>
                <div class="flex items-center gap-1 text-sm mb-2" title="Hora del sorteo">
                    <i class="fas fa-clock text-orange-400" aria-hidden="true"></i>
                    {{ \Carbon\Carbon::parse($rifa->hora_sorteo)->format('H:i') }}
                </div>
            </div>
        </div>
    </div>
</div>
