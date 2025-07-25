<div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
  <div @click.away="closeModal()" class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 relative">
    <h3 class="text-lg font-bold mb-4 text-primary-700">
        <i class="fas fa-user-edit mr-2"></i>
        <span x-text="editMode ? 'Editar Cliente' : 'Nuevo Cliente'"></span>
    </h3>
    <form @submit.prevent="submit" autocomplete="off" x-bind:class="{ 'opacity-50 pointer-events-none': loading }">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" x-model="nombre" class="mt-1 block w-full rounded border-gray-300" required>
            <template x-if="errors.nombre"><p class="text-red-600 text-xs mt-1" x-text="errors.nombre[0]"></p></template>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Cédula</label>
            <input type="text" x-model="cedula" @blur="buscarCedula" class="mt-1 block w-full rounded border-gray-300" required>
            <span class="text-green-700 text-xs" x-text="mensajeCedula"></span>
            <template x-if="errors.cedula"><p class="text-red-600 text-xs mt-1" x-text="errors.cedula[0]"></p></template>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" x-model="email" @blur="validarEmail" class="mt-1 block w-full rounded border-gray-300">
            <span class="text-red-600 text-xs" x-text="mensajeEmail"></span>
            <template x-if="errors.email"><p class="text-red-600 text-xs mt-1" x-text="errors.email[0]"></p></template>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
            <input type="text" x-model="telefono" @blur="validarTelefono" class="mt-1 block w-full rounded border-gray-300">
            <span class="text-red-600 text-xs" x-text="mensajeTelefono"></span>
            <template x-if="errors.telefono"><p class="text-red-600 text-xs mt-1" x-text="errors.telefono[0]"></p></template>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Dirección</label>
            <input type="text" x-model="direccion" class="mt-1 block w-full rounded border-gray-300">
            <template x-if="errors.direccion"><p class="text-red-600 text-xs mt-1" x-text="errors.direccion[0]"></p></template>
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <button type="button" @click="closeModal()" class="px-4 py-1 bg-gray-200 rounded">Cancelar</button>
            <button type="submit" class="px-5 py-1 bg-primary-600 text-white rounded hover:bg-primary-700">Guardar</button>
        </div>
    </form>
  </div>
</div>
