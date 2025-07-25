<div 
    class="grid grid-cols-2 md:grid-cols-4 gap-6"
    x-data="{ show: false }"
    x-init="setTimeout(() => show = true, 150)"
>
    {{-- Rifas Activas --}}
    <div 
        class="relative bg-white rounded-2xl shadow-xl p-5 border-t-4 transition-all duration-700 ease-out
                transform opacity-0 translate-y-8 hover:scale-105 hover:shadow-2xl cursor-pointer group"
        style="border-color: {{ $primaryColor }}"
        :class="show ? 'opacity-100 translate-y-0' : ''"
    >
        <span class="absolute right-4 top-3 text-[60px] opacity-10 pointer-events-none select-none group-hover:opacity-20 transition">
            <i class="fas fa-bolt" style="color: {{ $primaryColor }}"></i>
        </span>
        <div class="relative z-10">
            <div class="text-xs font-bold uppercase text-gray-500 mb-1">Rifas Activas</div>
            <div class="text-3xl md:text-4xl font-black text-brand" style="color: {{ $primaryColor }}">{{ $rifasActivas }}</div>
            <div class="text-sm text-gray-500 mt-1">Rifas en curso</div>
        </div>
    </div>

    {{-- Tickets Vendidos Hoy --}}
    <div 
        class="relative bg-white rounded-2xl shadow-xl p-5 border-t-4 border-blue-400 transition-all duration-700 ease-out
                transform opacity-0 translate-y-8 hover:scale-105 hover:shadow-2xl cursor-pointer group"
        :class="show ? 'opacity-100 translate-y-0' : ''"
        style="transition-delay: .10s"
    >
        <span class="absolute right-4 top-3 text-[60px] opacity-10 pointer-events-none select-none group-hover:opacity-20 transition text-blue-400">
            <i class="fas fa-ticket-alt"></i>
        </span>
        <div class="relative z-10">
            <div class="text-xs font-bold uppercase text-blue-600 mb-1">Tickets Hoy</div>
            <div class="text-3xl md:text-4xl font-black text-blue-600">{{ $ticketsVendidosHoy }}</div>
            <div class="text-sm text-gray-500 mt-1">Vendidos hoy</div>
        </div>
    </div>

    {{-- Abonos Pendientes --}}
    <div 
        class="relative bg-white rounded-2xl shadow-xl p-5 border-t-4 border-yellow-400 transition-all duration-700 ease-out
                transform opacity-0 translate-y-8 hover:scale-105 hover:shadow-2xl cursor-pointer group"
        :class="show ? 'opacity-100 translate-y-0' : ''"
        style="transition-delay: .20s"
    >
        <span class="absolute right-4 top-3 text-[60px] opacity-10 pointer-events-none select-none group-hover:opacity-20 transition text-yellow-400">
            <i class="fas fa-hand-holding-usd"></i>
        </span>
        <div class="relative z-10">
            <div class="text-xs font-bold uppercase text-yellow-600 mb-1">Abonos Pendientes</div>
            <div class="text-3xl md:text-4xl font-black text-yellow-600">{{ $ticketsConAbono }}</div>
            <div class="text-sm text-gray-500 mt-1">Tickets en reserva</div>
        </div>
    </div>

    {{-- Ingresos Totales --}}
    <div 
        class="relative bg-white rounded-2xl shadow-xl p-5 border-t-4 border-green-500 transition-all duration-700 ease-out
                transform opacity-0 translate-y-8 hover:scale-105 hover:shadow-2xl cursor-pointer group"
        :class="show ? 'opacity-100 translate-y-0' : ''"
        style="transition-delay: .30s"
    >
        <span class="absolute right-4 top-3 text-[60px] opacity-10 pointer-events-none select-none group-hover:opacity-20 transition text-green-400">
            <i class="fas fa-dollar-sign"></i>
        </span>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold uppercase text-green-700">Ingresos Totales</span>
                <span title="Solo pagos confirmados" class="text-gray-400 cursor-pointer">
                    <i class="fas fa-info-circle"></i>
                </span>
            </div>
            <div class="text-3xl md:text-4xl font-black text-green-600">${{ number_format($ingresosTotales, 2) }}</div>
            <div class="flex flex-col mt-1 text-xs text-gray-500 leading-tight">
                <span>+${{ number_format($ingresosHoy, 2) }} <span class="font-medium text-green-700">Hoy</span></span>
                <span>+${{ number_format($ingresosMes, 2) }} <span class="font-medium text-green-700">este mes</span></span>
            </div>
        </div>
    </div>
</div>
