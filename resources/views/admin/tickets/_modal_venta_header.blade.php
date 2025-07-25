<h4 class="text-xl font-semibold mb-4 text-center text-indigo-700 relative">
  <span
    class="opacity-10 absolute -top-6 right-0 text-7xl pointer-events-none select-none"
    :class="{
      'text-indigo-200': accion === 'vender',
      'text-orange-200': accion === 'apartado',
      'text-purple-200': accion === 'abono'
    }"
  >
    <i
      :class="{
        'fas fa-cash-register': accion === 'vender',
        'fas fa-hourglass-half': accion === 'apartado',
        'fas fa-coins': accion === 'abono'
      }"
    ></i>
  </span>
  Gestión de Ticket
  <span class="font-mono" x-text="picked ? String(picked.numero).padStart(padLen,'0') : '--'"></span>
</h4>
<div class="mb-2 flex justify-between text-xs">
  <div>
    <span class="font-bold">Precio:</span>
    <span x-text="picked ? picked.precio_ticket : '—'"></span>
  </div>
  <div>
    <span class="font-bold">Estado:</span>
    <span x-text="picked ? picked.estado : '—'"></span>
  </div>
</div>
<div class="mb-3 flex gap-2 justify-center">
  <template x-for="op in operaciones" :key="op.value">
    <button
      type="button"
      @click="accion = op.value"
      :class="accion === op.value ? op.selectedClass : op.unselectedClass"
      class="relative flex-1 px-2 py-1 rounded-lg font-semibold transition-all flex items-center justify-center overflow-hidden"
    >
      <span class="absolute opacity-10 text-5xl right-2 bottom-0 pointer-events-none">
        <i :class="op.icon"></i>
      </span>
      <span class="relative z-10 flex items-center gap-1 text-base">
        <i :class="op.icon"></i>
        <span x-text="op.label"></span>
      </span>
    </button>
  </template>
</div>
