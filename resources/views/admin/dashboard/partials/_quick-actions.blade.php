<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-10">
    <a href="{{ route('admin.tickets.sale') }}"
       class="group flex flex-col items-center justify-center bg-white border-2 border-brand/30 shadow-lg rounded-xl p-5 hover:bg-brand hover:text-white transition-all hover:scale-105"
       style="border-color: {{ $primaryColor }}33;">
        <i class="fas fa-cash-register text-2xl mb-1 group-hover:text-white" style="color: {{ $primaryColor }}"></i>
        <span class="text-base font-semibold">Venta de Tickets</span>
    </a>
    <a href="{{ route('admin.rifas.create') }}"
       class="group flex flex-col items-center justify-center bg-white border-2 border-blue-300 shadow-lg rounded-xl p-5 hover:bg-blue-600 hover:text-white transition-all hover:scale-105">
        <i class="fas fa-plus-circle text-2xl mb-1 group-hover:text-white text-blue-500"></i>
        <span class="text-base font-semibold">Crear nueva rifa</span>
    </a>
    <a href="{{ route('admin.rifas.index') }}"
       class="group flex flex-col items-center justify-center bg-white border-2 border-purple-300 shadow-lg rounded-xl p-5 hover:bg-purple-600 hover:text-white transition-all hover:scale-105">
        <i class="fas fa-list-ul text-2xl mb-1 group-hover:text-white text-purple-500"></i>
        <span class="text-base font-semibold">Ver Rifas</span>
    </a>
    <a href="{{ route('admin.tickets.index', ['filter' => 'reservas']) }}"
       class="group flex flex-col items-center justify-center bg-white border-2 border-yellow-300 shadow-lg rounded-xl p-5 hover:bg-yellow-400 hover:text-white transition-all hover:scale-105">
        <i class="fas fa-bookmark text-2xl mb-1 group-hover:text-white text-yellow-500"></i>
        <span class="text-base font-semibold">Tickets Reservados</span>
    </a>
    <a href="{{ route('admin.settings.payments') }}"
       class="group flex flex-col items-center justify-center bg-white border-2 border-gray-300 shadow-lg rounded-xl p-5 hover:bg-gray-800 hover:text-white transition-all hover:scale-105">
        <i class="fas fa-credit-card text-2xl mb-1 group-hover:text-white text-gray-700"></i>
        <span class="text-base font-semibold">Métodos de pago</span>
    </a>
</div>
