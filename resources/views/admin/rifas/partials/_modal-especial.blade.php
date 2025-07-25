{{-- Modal: Detalle Premio Especial --}}

<template x-if="$store.participantes.especialOpen">
  <div
    x-cloak
    x-on:keydown.escape.window="$store.participantes.closeEspecial()"
    x-on:click.away="$store.participantes.closeEspecial()"
    class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4"
  >
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-8 animate-fade-in">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-indigo-700">
          <i class="fas fa-ticket-alt mr-2"></i>
          Ticket #<span x-text="$store.participantes.especialData.ticket.numero"></span>
        </h2>
        <span
          x-text="$store.participantes.especialData.solvente ? '✓ Solvente' : '✗ Insolvente'"
          :class="$store.participantes.especialData.solvente
            ? 'bg-green-100 text-green-700'
            : 'bg-red-100 text-red-700'"
          class="px-2 py-1 rounded-full text-sm font-bold"
        ></span>
      </div>
      <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
          <p class="text-xs text-gray-500">Cliente</p>
          <p class="font-medium text-gray-700" x-text="$store.participantes.especialData.cliente.nombre"></p>
        </div>
        <div>
          <p class="text-xs text-gray-500">Teléfono</p>
          <p class="font-medium text-gray-700" x-text="$store.participantes.especialData.cliente.telefono"></p>
        </div>
        <div class="col-span-2">
          <p class="text-xs text-gray-500">Dirección</p>
          <p class="font-medium text-gray-700" x-text="$store.participantes.especialData.cliente.direccion"></p>
        </div>
        <div>
          <p class="text-xs text-gray-500">Precio ticket</p>
<p class="font-medium text-indigo-700">
  $<span x-text="(+$store.participantes.especialData.ticket.precio_ticket).toFixed(2)"></span>
</p>

        </div>
        <div>
          <p class="text-xs text-gray-500">Abono mínimo exigido</p>
          <p class="font-medium text-indigo-700">
            $<span x-text="$store.participantes.especialData.abono_minimo.toFixed(2)"></span>
          </p>
        </div>
        <div>
          <p class="text-xs text-gray-500">Total abonado</p>
          <p class="font-medium text-green-700">
            $<span x-text="$store.participantes.especialData.total_abonos.toFixed(2)"></span>
          </p>
        </div>
        <template x-if="!$store.participantes.especialData.solvente">
          <div>
            <p class="text-xs text-gray-500">Faltante</p>
            <p class="font-medium text-red-700">
              $<span x-text="$store.participantes.especialData.faltante.toFixed(2)"></span>
            </p>
          </div>
        </template>
      </div>
      <table class="w-full table-auto mb-6 border-t">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-2 py-2 text-left text-xs font-bold text-gray-600">Fecha</th>
            <th class="px-2 py-2 text-right text-xs font-bold text-gray-600">Monto</th>
            <th class="px-2 py-2 text-left text-xs font-bold text-gray-600">Método</th>
            <th class="px-2 py-2 text-left text-xs font-bold text-gray-600">Referencia</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="ab in $store.participantes.especialData.abonos" :key="ab.fecha + ab.monto">
            <tr>
              <td class="px-2 py-1 text-xs text-gray-500" x-text="ab.fecha"></td>
              <td class="px-2 py-1 text-xs text-right text-gray-800" x-text="ab.monto.toFixed(2)"></td>
              <td class="px-2 py-1 text-xs text-gray-600" x-text="ab.metodo_pago"></td>
              <td class="px-2 py-1 text-xs text-gray-600" x-text="ab.referencia"></td>
            </tr>
          </template>
        </tbody>
      </table>
      <div class="text-center mt-4">
        <button
          @click="$store.participantes.closeEspecial()"
          class="px-5 py-2 bg-gray-400 hover:bg-gray-600 text-white rounded-lg font-semibold"
        >
          Cerrar
        </button>
      </div>
    </div>
  </div>
</template>
