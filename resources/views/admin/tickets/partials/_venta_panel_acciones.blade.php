<div class="flex flex-row items-center justify-center w-full mt-6">
  <div class="inline-flex rounded-xl shadow bg-white border border-gray-200 overflow-hidden">
    <!-- Venta Total -->
    <button
      type="button"
      @click="accion = 'vender'; iniciarAbono(); irAPago()"
      :class="accion === 'vender'
        ? 'bg-gradient-to-tr from-primary to-green-500 text-white shadow-md scale-105 z-10'
        : 'bg-white text-primary hover:bg-primary/10'"
      class="flex items-center gap-2 px-6 py-3 font-bold text-base transition-all min-w-[140px] relative"
    >
      <i class="fas fa-cash-register fa-lg"></i>
      <span>Venta Total</span>
    </button>
    <!-- Apartado -->
    <button
      type="button"
      @click="accion = 'apartado'; iniciarAbono()"
      :class="accion === 'apartado'
        ? 'bg-gradient-to-tr from-orange-400 to-yellow-400 text-white shadow-md scale-105 z-10'
        : 'bg-white text-orange-500 hover:bg-orange-50'"
      class="flex items-center gap-2 px-6 py-3 font-bold text-base transition-all min-w-[120px] border-l border-gray-200 relative"
    >
      <i class="fas fa-hourglass-half fa-lg"></i>
      <span>Apartado</span>
    </button>
    <!-- Abono Inicial -->
    <button
      type="button"
      @click="accion = 'abono'; iniciarAbono(); irAPago()"
      :class="accion === 'abono'
        ? 'bg-gradient-to-tr from-purple-600 to-fuchsia-500 text-white shadow-md scale-105 z-10'
        : 'bg-white text-purple-700 hover:bg-purple-100'"
      class="flex items-center gap-2 px-6 py-3 font-bold text-base transition-all min-w-[130px] border-l border-gray-200 relative"
    >
      <i class="fas fa-coins fa-lg"></i>
      <span>Abono Inicial</span>
    </button>
  </div>

  <div class="w-full" x-show="accion === 'abono' && pickedTickets && pickedTickets.length === 1" x-transition>
    <input
      type="number"
      min="1"
      :max="pickedTickets[0]?.precio_ticket || 1000000"
      x-model="montoAbono"
      placeholder="Monto abono inicial"
      class="ml-4 border rounded-lg p-2 text-sm focus:ring-2 focus:ring-purple-200 w-40"
    >
  </div>
</div>
