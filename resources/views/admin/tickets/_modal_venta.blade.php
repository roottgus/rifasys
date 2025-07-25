{{-- resources/views/admin/tickets/_modal_venta.blade.php --}}
<div
  x-data="$store.modalVentaTicket"
  x-show="modalOpen"
  x-cloak
  @open-venta-ticket.window="open($event.detail)"
  @open-venta-seleccionados.window="openVentaSeleccionados($event.detail)"
  @keydown.escape.window="closeModal()"
>
  {{-- Overlay --}}
  <div x-show="modalOpen" class="fixed inset-0 bg-black bg-opacity-70 z-40"></div>

  {{-- Modal centrado --}}
  <div
    x-show="modalOpen"
    class="fixed inset-0 flex items-center justify-center p-4 z-50"
    @keydown.escape.window="closeModal()"
  >
    <div
      class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl border-2 border-primary/20 overflow-visible"
      style="min-width:1000px; max-height:90vh;"
    >

      {{-- PASO 1: Cliente + Tickets --}}
      <template x-if="paso === 1">
        <div class="p-8 flex flex-col md:flex-row gap-6">
          <div class="md:w-1/2 bg-blue-50 rounded-l-3xl p-6 border-r">
            @include('admin.tickets.partials._venta_panel_cliente')
          </div>
          <div class="md:w-1/2 bg-white rounded-r-3xl p-6">
            @include('admin.tickets.partials._venta_panel_tickets')
          </div>
        </div>
      </template>

      {{-- PASO 2: Método de Pago --}}
      <template x-if="paso === 2">
        <div class="p-8">
          @include('admin.tickets.partials._venta_panel_pago')
        </div>
      </template>

      {{-- Paso 3: Éxito --}}
<template x-if="ventaExitosa">
  @include('admin.tickets._modal_venta_exito')
</template>

      {{-- MENSAJE DE ERROR --}}
      <template x-if="errorMensaje">
        <div class="px-8">
          <div class="text-red-600 font-bold text-center" x-text="errorMensaje"></div>
        </div>
      </template>

      {{-- FOOTER --}}
<template x-if="!ventaExitosa">
  <div class="border-t bg-white p-4 flex justify-between items-center rounded-b-3xl">
    <button
      type="button"
      @click="closeModal()"
      class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100 font-semibold"
    >
      Cancelar
    </button>
    <div class="flex gap-3">
      {{-- Botón Atrás solo en PASO 2 --}}
      <button
        type="button"
        @click="paso = 1"
        x-show="paso === 2"
        class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100 font-semibold"
      >
        Atrás
      </button>
      {{-- Solo en PASO 1 --}}
      <button
        type="button"
        @click="clickVentaTotal()"
        class="px-4 py-2 bg-gradient-to-tr from-primary to-green-500 text-white rounded-lg font-semibold"
        :disabled="abonoEnCurso"
        x-show="paso === 1"
      >
        Venta Total
      </button>
      <button
        type="button"
        @click="clickApartado()"
        class="px-4 py-2 bg-gradient-to-tr from-orange-400 to-yellow-400 text-white rounded-lg font-semibold"
        :disabled="abonoEnCurso"
        x-show="paso === 1"
      >
        Apartado
      </button>
      <button
        type="button"
        @click="clickAbonoInicial()"
        class="px-4 py-2 bg-gradient-to-tr from-purple-600 to-fuchsia-500 text-white rounded-lg font-semibold"
        x-show="paso === 1"
      >
        <span x-text="abonoEnCurso ? 'Procesar Abono' : 'Abono Inicial'"></span>
      </button>
      {{-- Solo en PASO 2 --}}
      <button
        type="button"
        @click="procesarVenta()"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold"
        x-show="paso === 2"
        :disabled="!metodoPago || reportFields().some(f => !pagoDatos[f.key]) || errorReferencia || referenciaVerificando"
      >
        Confirmar Pago
      </button>
    </div>
  </div>
</template>
    </div>
  </div>
</div>
