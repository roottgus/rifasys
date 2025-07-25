<div class="bg-white rounded-xl shadow flex flex-col md:flex-row items-center gap-6 p-5 mb-8 border-l-8 border-primary relative">

    <!-- Mensaje automático si hay poco para vender -->
    <template x-if="porcentaje >= 90">
        <div class="absolute left-6 -top-6 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded p-2 text-xs font-semibold shadow">
            ¡Quedan pocos boletos! Avance: <span x-text="porcentaje"></span>%
        </div>
    </template>

    <div class="flex-1 w-full">

        <div class="font-bold text-primary text-xl mb-1" x-text="nombre"></div>
        <div class="text-gray-500 text-xs mb-2" x-text="fecha ? 'Sorteo: ' + fecha : '—'"></div>
        
        <div class="flex flex-wrap gap-4 mb-2">
            <span class="font-semibold text-gray-700 text-sm">
                Vendidos: <span class="text-primary" x-text="vendidos"></span>
            </span>
            <span class="font-semibold text-gray-700 text-sm">
                Reservados: <span class="text-primary" x-text="reservados"></span>
            </span>
            <span class="font-semibold text-gray-700 text-sm">
                Abonados: <span class="text-primary" x-text="abonados"></span>
            </span>
            <span class="font-semibold text-gray-700 text-sm">
                Total: <span class="text-primary" x-text="total"></span>
            </span>
            <span class="font-semibold text-green-700 text-sm" x-show="typeof total_recaudado !== 'undefined'">
                Recaudado: <span class="text-green-600" x-text="'$' + Number(total_recaudado ?? 0).toLocaleString()"></span>
            </span>
            <span class="font-semibold text-red-700 text-sm">
                Restan: <span class="text-red-600" x-text="total - (vendidos + abonados + reservados + apartados)"></span> boletos
            </span>
        </div>
        
        <!-- Barra de progreso con degradado, número grande centrado y animación -->
        <div class="relative h-12 w-full mb-1 mt-3">
            <div class="absolute inset-0 flex justify-center items-center z-10 pointer-events-none">
                <span class="font-bold text-primary text-2xl drop-shadow" x-text="porcentaje + '%'"></span>
                <template x-if="loading">
                    <svg class="animate-spin h-6 w-6 text-primary ml-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </template>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                <div 
                    class="bg-gradient-to-r from-primary via-blue-400 to-blue-300 h-4 rounded-full transition-all duration-700"
                    :style="`width: ${porcentaje}%`">
                </div>
            </div>
        </div>
        <div class="text-xs text-gray-500 mt-2 text-right">
            Progreso total: <span class="font-bold text-primary" x-text="porcentaje"></span>%
        </div>
    </div>
</div>
