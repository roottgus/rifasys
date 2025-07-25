<div
  x-show="deleteLoteriaModal"
  x-cloak
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
>
  <div @click.away="deleteLoteriaModal = false" class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
    <div class="flex items-center mb-3">
      <span class="text-red-600 mr-2 text-2xl"><i class="fas fa-exclamation-triangle"></i></span>
      <span class="font-bold text-lg text-red-600">Confirmar eliminación</span>
      <button @click="deleteLoteriaModal = false" class="ml-auto text-gray-400 hover:text-red-600 text-lg">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div class="mb-4">
      <p class="font-semibold text-gray-800 mb-2">¿Seguro que deseas eliminar la lotería?</p>
      <p class="text-gray-500 text-xs">Esta acción no se puede deshacer.</p>
    </div>
    <div class="flex justify-end gap-2 mt-4">
      <button @click="deleteLoteriaModal = false" class="px-4 py-2 bg-gray-200 rounded">Cancelar</button>
      <button @click="confirmDeleteLoteria()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
    </div>
  </div>
</div>
