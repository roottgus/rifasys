{{-- resources/views/admin/tickets/partials/_modal_ticket_detalle.blade.php --}}
<div
  x-data="ticketDetail()"
  x-show="detalleModalOpen"
  x-cloak
  @keydown.escape.window="closeDetalleModal()"
  class="fixed inset-0 flex items-center justify-center bg-black/60 z-50"
>
  <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-8 relative animate-fade-in">

    {{-- Badge de estado --}}
    <template x-if="detalleTicketData">
      <span
        class="absolute -top-5 left-0 px-4 py-1 rounded-tl-2xl rounded-br-2xl text-base font-extrabold shadow text-white"
        :class="{
          'bg-gray-400': detalleTicketData.estado === 'vendido',
          'bg-orange-400': detalleTicketData.estado === 'reservado',
          'bg-purple-400': detalleTicketData.estado === 'abonado',
          'bg-green-400': detalleTicketData.estado === 'disponible'
        }"
        x-text="detalleTicketData.estado"
        style="z-index:2;"
      ></span>
    </template>

    {{-- Botón cerrar --}}
    <button
      class="absolute top-4 right-4 text-gray-400 hover:text-primary"
      @click="closeDetalleModal()"
      aria-label="Cerrar"
    >
      <i class="fa-solid fa-times fa-lg"></i>
    </button>

    {{-- Header --}}
    <div class="flex flex-col items-center mb-4">
      <div class="rounded-2xl border-4 border-primary/30 bg-white px-8 py-3 shadow-md flex flex-col items-center min-w-[340px]">
        <h2 class="text-2xl font-black text-primary flex items-center gap-2 mb-1">
          <i class="fa-solid fa-ticket-alt"></i>
          Detalle de Ticket
          <span class="ml-2 font-mono text-lg text-primary-900 bg-primary/10 px-3 py-1 rounded-xl border border-primary/10 shadow">
            <i class="fa-solid fa-hashtag"></i>
            <span x-text="padNum(detalleTicketData?.numero)"></span>
          </span>
        </h2>

        {{-- Resumen: Valor, Abonado, Saldo --}}
        <template x-if="detalleTicketData">
          <div class="flex gap-5 mt-2 text-base font-semibold">
            <div class="px-4 py-1 rounded bg-gray-50 border border-gray-200 flex flex-col items-center">
              <span class="text-xs font-medium text-gray-500">Valor Ticket</span>
              <span class="font-mono">
                $<span x-text="Number(detalleTicketData.precio_ticket).toFixed(2)"></span>
              </span>
            </div>
            <div class="px-4 py-1 rounded bg-green-50 border border-green-200 flex flex-col items-center">
              <span class="text-xs font-medium text-green-700">Total abonado</span>
              <span class="font-mono text-green-700 font-bold">
                $<span x-text="Number(detalleTicketData.total_abonado).toFixed(2)"></span>
              </span>
            </div>
            <div class="px-4 py-1 rounded bg-red-50 border border-red-200 flex flex-col items-center">
              <span class="text-xs font-medium text-red-700">Saldo pendiente</span>
              <span class="font-mono text-red-700 font-bold">
                $<span x-text="Number(detalleTicketData.saldo_pendiente).toFixed(2)"></span>
              </span>
            </div>
          </div>
        </template>
      </div>
    </div>

    {{-- Loader --}}
    <template x-if="detalleTicketLoading">
      <div class="flex flex-col items-center py-10 space-y-3">
        <div class="w-20 h-6 rounded bg-gray-200 animate-pulse"></div>
        <div class="w-40 h-4 rounded bg-gray-100 animate-pulse"></div>
        <div class="w-full h-36 rounded-xl bg-gray-50 animate-pulse mt-4"></div>
      </div>
    </template>

    {{-- Error --}}
    <template x-if="detalleTicketError">
      <div class="text-red-600 font-semibold text-center py-10" x-text="detalleTicketError"></div>
    </template>

    {{-- Detalle --}}
    <template x-if="detalleTicketData">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-4">

        {{-- Cliente --}}
        <div class="space-y-3 bg-gray-50 border border-blue-100 rounded-xl px-4 py-3 shadow-sm">
          <h3 class="text-lg font-bold text-primary/80 mb-2 flex items-center gap-1">
            <i class="fa-solid fa-user"></i> Cliente
          </h3>
          <div><b>Nombre:</b> <span x-text="detalleTicketData.cliente?.nombre ?? '—'"></span></div>
          <div><b>Cédula:</b> <span x-text="detalleTicketData.cliente?.cedula ?? '—'"></span></div>
          <div><b>Teléfono:</b> <span x-text="detalleTicketData.cliente?.telefono ?? '—'"></span></div>
          <div><b>Dirección:</b> <span x-text="detalleTicketData.cliente?.direccion ?? '—'"></span></div>
        </div>

        {{-- Abonos --}}
        <div>
          <h3 class="text-lg font-bold text-primary/80 mb-2 flex items-center gap-1">
            <i class="fa-solid fa-credit-card"></i> Pagos
          </h3>
          <template x-if="detalleTicketData.abonos?.length">
            <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
              <template x-for="abono in detalleTicketData.abonos" :key="abono.id">
                <div class="border rounded-lg px-3 py-2 bg-primary/5 mb-1">
                  <div class="space-y-1">
                    <div class="flex gap-2 items-center">
                      <span class="font-semibold">Método:</span>
                      <span x-text="nombreMetodoPago(abono.metodo_pago)"></span>
                    </div>
                    <div class="flex gap-2 items-center" x-show="abono.metodo_pago !== 'zelle'">
                      <span class="font-semibold">Banco:</span>
                      <span x-text="abono.banco"></span>
                    </div>
                    <div class="flex gap-2 items-center">
                      <span class="font-semibold">Monto:</span>
                      <span class="text-green-700 font-bold">
                        $<span x-text="Number(abono.monto).toFixed(2)"></span>
                      </span>
                    </div>
                    <div class="flex gap-2 items-center">
                      <span class="font-semibold">Referencia:</span>
                      <span x-text="abono.referencia"></span>
                    </div>
                    <div class="flex gap-2 items-center">
                      <span class="font-semibold">Fecha/Hora:</span>
                      <span x-text="abono.fecha"></span>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </template>
          <template x-if="!detalleTicketData.abonos?.length">
            <div class="text-gray-400">Sin pagos registrados.</div>
          </template>
        </div>
      </div>
    </template>

    {{-- Pie --}}
    <div class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-3">
      <a
        :href="detalleTicketData ? `/admin/tickets/${detalleTicketData.id}/pdf` : '#'"
        target="_blank"
        class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-xl shadow hover:bg-primary/80 hover:scale-105 transition-all focus:ring-2 focus:ring-primary"
      >
        <i class="fa-solid fa-print mr-2"></i> Volver a Imprimir
      </a>
      <button
        @click="closeDetalleModal()"
        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl shadow hover:bg-gray-200 hover:scale-105 transition-all"
      >
        Cerrar
      </button>
    </div>
  </div>
</div>
