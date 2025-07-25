{{-- resources/views/admin/tickets/partials/_abono_modo.blade.php --}}
<div 
  x-show="accion==='abono' && pickedTickets.length > 1" 
  x-cloak
  class="mb-4 p-4 bg-white border rounded-lg space-y-3"
>
  <p class="font-medium">¿Cómo quieres aplicar el abono?</p>
  <div class="flex gap-2">
    <button
      type="button"
      @click="elegirAbonoModo('global')"
      :class="abonoModo==='global' 
        ? 'bg-blue-600 text-white' 
        : 'bg-gray-200 text-gray-800'"
      class="px-4 py-2 rounded"
    >
      Global
    </button>
    <button
      type="button"
      @click="elegirAbonoModo('ticket')"
      :class="abonoModo==='ticket' 
        ? 'bg-blue-600 text-white' 
        : 'bg-gray-200 text-gray-800'"
      class="px-4 py-2 rounded"
    >
      Por ticket
    </button>
  </div>

  <div x-show="abonoModo==='ticket'" x-cloak class="pt-2 border-t border-gray-200">
    <label for="ticketParaAbono" class="block mb-1 font-medium">
      Selecciona ticket para abono
    </label>
    <select
      id="ticketParaAbono"
      x-model="ticketParaAbono"
      class="w-full border rounded px-3 py-2"
    >
      <template x-for="t in pickedTickets" :key="t.id">
        <option
          :value="t.id"
          x-text="`Ticket #${String(t.numero).padStart(3,'0')} — $${Number(t.precio_ticket).toFixed(2)}`"
        ></option>
      </template>
    </select>
  </div>
</div>
