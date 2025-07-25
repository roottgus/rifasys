{{-- resources/views/admin/tickets/partials/_venta_panel_tickets.blade.php --}}
<div class="relative overflow-hidden">

  <h3 class="text-xl font-bold text-primary mb-5 flex items-center gap-2">
    <i class="fas fa-ticket-alt"></i> Venta múltiple de tickets
  </h3>

  <div class="flex flex-wrap gap-2 mb-3">
    <template x-for="t in pickedTickets" :key="t.id">
      <span class="px-2 py-1 rounded bg-green-100 text-green-800 font-mono text-sm shadow">
        <span x-text="String(t.numero).padStart(padLen, '0')"></span>
      </span>
    </template>
  </div>

  <div class="mb-3 bg-gray-50 rounded-xl shadow-inner px-3 py-2">
    <div class="flex flex-wrap gap-3 items-center text-sm text-gray-800">
      <div>
        <span class="font-semibold">Subtotal:</span>
        <span class="font-mono"
          x-text="'$'+ Number(descuentos.subtotal).toLocaleString('es-VE',{minimumFractionDigits:2})">
        </span>
      </div>
      <template x-if="descuentos.descuento > 0">
        <div>
          <span class="font-semibold text-blue-700">Descuento:</span>
          <span class="font-mono text-blue-700" x-text="descuentos.descuento + '%'"></span>
          <span class="ml-1 font-mono text-blue-700"
            x-text="'(-$' + Number(descuentos.montoDescuento).toLocaleString('es-VE',{minimumFractionDigits:2}) + ')'">
          </span>
        </div>
      </template>
      <div>
        <span class="font-semibold">Total a pagar:</span>
        <span class="text-green-700 font-mono font-bold"
          x-text="'$' + Number(descuentos.totalAPagar).toLocaleString('es-VE',{minimumFractionDigits:2})">
        </span>
      </div>
      <div>
        <span class="font-semibold">Tickets seleccionados:</span>
        <span class="text-primary font-bold" x-text="pickedTickets.length"></span>
      </div>
    </div>
  </div>

  <template x-if="descuentos.motivoDescuento">
    <div class="mt-2 text-xs text-blue-700 font-semibold">
      <i class="fas fa-gift"></i>
      <span x-text="descuentos.motivoDescuento"></span>
    </div>
  </template>

  {{-- BLOQUE DE ABONO --}}
  <div 
    x-show="accion === 'abono'" 
    class="mt-6 p-4 bg-white border rounded-lg space-y-4"
  >
    <!-- 1) Campo de Monto de Abono -->
    <div>
      <label for="montoAbono" class="block font-medium mb-1">Monto de abono</label>
      <input
        id="montoAbono"
        type="number"
        step="0.01"
        min="0"
        x-model="montoAbono"
        placeholder="Ej. 20.00"
        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-primary"
      />
    </div>

    <!-- 2) Modo de abono (solo si hay >1 ticket) -->
    <div x-show="pickedTickets.length > 1" class="space-y-3">
      <p class="font-medium">¿Cómo quieres aplicar el abono?</p>
      <div class="flex gap-2">
        <button
          type="button"
          @click="elegirAbonoModo('global')"
          :class="abonoModo === 'global' 
            ? 'bg-blue-600 text-white' 
            : 'bg-gray-200 text-gray-800'"
          class="px-4 py-2 rounded"
        >
          Global
        </button>
        <button
          type="button"
          @click="elegirAbonoModo('ticket')"
          :class="abonoModo === 'ticket' 
            ? 'bg-blue-600 text-white' 
            : 'bg-gray-200 text-gray-800'"
          class="px-4 py-2 rounded"
        >
          Por ticket
        </button>
      </div>

      <!-- 3) Selector de ticket (solo si modo === 'ticket') -->
      <div x-show="abonoModo === 'ticket'" class="pt-2 border-t border-gray-200">
        <label for="ticketParaAbono" class="block mb-1 font-medium">
          Selecciona ticket para abono
        </label>
        <select
          id="ticketParaAbono"
          x-model="ticketParaAbono"
          class="w-full border rounded px-3 py-2"
        >
          <template x-for="t in pickedTickets" :key="t.id">
            <option :value="t.id">
              Ticket #<span x-text="t.numero"></span> — $
              <span x-text="Number(t.precio_ticket).toFixed(2)"></span>
            </option>
          </template>
        </select>
      </div>
    </div>
    <!-- NO botón de procesar aquí, solo campos -->
  </div>
  {{-- FIN BLOQUE DE ABONO --}}

  @include('admin.tickets.partials._venta_panel_descuento')
  
</div>
