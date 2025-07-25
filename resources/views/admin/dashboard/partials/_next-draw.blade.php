@php
    $premioEspecial = $proximoPremioEspecial ?? null;
    $diasFaltan = $premioEspecial
        ? \Carbon\Carbon::parse($premioEspecial->fecha_premio)->diffInDays(now())
        : null;
    $esHoy = $premioEspecial
        ? \Carbon\Carbon::parse($premioEspecial->fecha_premio)->isToday()
        : false;
    // Calcula el % de abonos realizados respecto al mínimo
    $progresoAbonos = $premioEspecial && $premioEspecial->abono_minimo && isset($abonosEspecialesCount)
        ? min(100, round(($abonosEspecialesCount / $premioEspecial->abono_minimo) * 100))
        : 0;
@endphp

<div
    class="relative bg-gradient-to-tr from-white via-yellow-50 to-yellow-100 rounded-2xl shadow-xl p-6 mt-10 flex flex-col md:flex-row md:items-center md:gap-10 overflow-hidden"
    x-data="{ show: false }"
    x-init="setTimeout(() => show = true, 100)"
    :class="show ? 'animate-fade-in-up' : ''"
>
    {{-- Icono de fondo con animación glow --}}
    <span class="absolute right-6 bottom-2 text-[90px] md:text-[120px] text-yellow-300 opacity-10 pointer-events-none select-none
                transition-all duration-1000"
          :class="show ? 'opacity-20 blur-[1px]' : ''"
    >
        <i class="fas fa-gift animate-pulse-slow"></i>
    </span>

    <div class="flex items-center mb-4 md:mb-0">
        <div class="mr-5 flex flex-col items-center">
            <div class="bg-yellow-100 text-yellow-600 rounded-full p-3 shadow-md animate-bounce-slow">
                <i class="fas fa-gift text-3xl"></i>
            </div>
            @if($premioEspecial && $diasFaltan !== null && $diasFaltan < 3 && !$esHoy)
                <span class="mt-2 text-xs bg-orange-400 text-white font-semibold px-3 py-1 rounded-full animate-pulse">
                    ¡Falta poco!
                </span>
            @endif
            @if($premioEspecial && $esHoy)
                <span class="mt-2 text-xs bg-green-400 text-white font-semibold px-3 py-1 rounded-full animate-bounce">
                    ¡Es hoy!
                </span>
            @endif
        </div>
        <div>
            @if($premioEspecial)
                <h3 class="text-xl font-bold text-gray-700 mb-1 flex items-center gap-2">
                    Premio Especial para Abonados
                </h3>
                <div class="mb-1 text-gray-800 font-bold text-lg">"{{ $premioEspecial->detalle_articulo ?? $premioEspecial->tipo_premio }}"</div>
                <div class="flex items-center gap-4 mb-2">
                    <span class="inline-block px-3 py-1 rounded-lg font-semibold bg-yellow-100 text-yellow-700 text-sm shadow-sm">
                        {{ \Carbon\Carbon::parse($premioEspecial->fecha_premio)->format('d M Y') }}
                        <span class="mx-1">•</span>
                        {{ \Carbon\Carbon::parse($premioEspecial->hora_premio)->format('H:i') }}
                    </span>
                </div>
                {{-- Barra de progreso de abonos hacia el mínimo necesario --}}
                @if($premioEspecial->abono_minimo && isset($abonosEspecialesCount))
                    <div class="mb-1">
                        <div class="flex justify-between text-xs text-gray-500 font-semibold mb-1">
                            <span>{{ $abonosEspecialesCount }} abonos</span>
                            <span>Mínimo: {{ $premioEspecial->abono_minimo }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="bg-yellow-400 h-3 rounded-full transition-all duration-700 ease-in-out"
                                 style="width: {{ $progresoAbonos }}%">
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Botón para gestionar o ver participantes --}}
                <div class="mt-3">
                    <a href="{{ route('admin.premios.participantes', $premioEspecial->id) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-yellow-400 text-white font-bold shadow hover:bg-yellow-500 transition duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                    >
                        <i class="fas fa-users"></i> Ver Participantes
                    </a>
                </div>
            @else
                {{-- Fallback: Próximo sorteo común si no hay premios especiales --}}
                <h3 class="text-lg font-semibold text-gray-700 mb-1">Próximo Sorteo Regular</h3>
                @if($proximoSorteo)
                    <p class="text-gray-800">
                        "{{ $proximoSorteo->nombre }}"<br>
                        <span class="text-brand font-bold" style="color: {{ $primaryColor }}">
                            {{ $proximoSorteo->fecha_sorteo->format('d M Y') }} a las {{ $proximoSorteo->hora_sorteo->format('H:i') }}
                        </span>
                    </p>
                @else
                    <p class="text-gray-600">No hay futuros sorteos programados.</p>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- Custom animaciones si no tienes Tailwind plugin: --}}
<style>
@keyframes fade-in-up {
    0% { opacity: 0; transform: translateY(32px);}
    100% { opacity: 1; transform: translateY(0);}
}
.animate-fade-in-up { animation: fade-in-up 0.9s cubic-bezier(.28,1.16,.45,.98) 0.1s both;}
@keyframes pulse-slow {
    0%, 100% { opacity: .10;}
    50% { opacity: .18;}
}
.animate-pulse-slow { animation: pulse-slow 2.5s infinite;}
@keyframes bounce-slow {
    0%, 100% { transform: translateY(0);}
    50% { transform: translateY(-4px);}
}
.animate-bounce-slow { animation: bounce-slow 2s infinite;}
</style>
