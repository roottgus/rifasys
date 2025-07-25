<div x-show="deleteTipoLoteriaModal" x-cloak 
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
  <div @click.away="deleteTipoLoteriaModal = false"
       class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 relative animate-fadeIn">

    <!-- Botón cerrar arriba a la derecha -->
    <button type="button"
      class="absolute top-3 right-4 text-gray-400 hover:text-red-600 text-xl transition"
      @click="deleteTipoLoteriaModal = false"
      aria-label="Cerrar">
      <i class="fas fa-times"></i>
    </button>
    
    <!-- Cabecera de advertencia -->
    <div class="flex items-center mb-4 gap-2">
      <span class="text-red-500 text-2xl"><i class="fas fa-exclamation-triangle"></i></span>
      <h3 class="font-bold text-lg text-red-600">Confirmar eliminación</h3>
    </div>
    <div class="text-gray-700 mb-5">
      ¿Seguro que desea eliminar el tipo de lotería?<br>
      <span class="text-xs text-gray-500">Esta acción no se puede deshacer.</span>
    </div>
    <div class="flex justify-end gap-2">
      <button type="button"
              class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition"
              @click="deleteTipoLoteriaModal = false">Cancelar</button>
      <button type="button"
              class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition font-semibold"
              @click="confirmDeleteTipoLoteria()">Eliminar</button>
    </div>
  </div>
</div>
