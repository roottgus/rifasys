<div class="my-2">
  <!-- Loader mientras llega el descuento -->
  <template x-if="descuentos.cargandoDescuento">
    <div class="flex items-center gap-2 text-sm text-gray-500">
      <i class="fas fa-spinner fa-spin"></i> Calculando descuento...
    </div>
  </template>

  <!-- Detalles de descuento aplicado -->
  <template x-if="!descuentos.cargandoDescuento && descuentos.descuento > 0">
    <div class="bg-green-50 border border-green-200 p-3 rounded-lg text-green-800 text-sm space-y-1">
      <div>
        <strong>¡Descuento aplicado!</strong>
        <span class="ml-1" x-text="descuentos.descuento + '%'"></span> OFF
      </div>
      <div>
        Precio por ticket:
        <strong
          x-text="
            '$' +
            Number(descuentos.precioConDescuento)
              .toLocaleString('es-VE', { minimumFractionDigits: 2 })
          "
        ></strong>
      </div>
      <div>
        Total con descuento:
        <strong
          x-text="
            '$' +
            Number(descuentos.totalAPagar)
              .toLocaleString('es-VE', { minimumFractionDigits: 2 })
          "
        ></strong>
      </div>
      <div class="text-xs text-gray-600" x-text="descuentos.motivoDescuento"></div>
    </div>
  </template>

  <!-- Mensaje cuando no hay descuento -->
  <template x-if="!descuentos.cargandoDescuento && descuentos.descuento === 0">
    <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg text-yellow-800 text-sm">
      Sin descuento para la cantidad seleccionada.
    </div>
  </template>
</div>
