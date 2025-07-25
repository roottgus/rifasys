<div class="bg-white border-l-4 border-orange-300 rounded-xl shadow p-5">
  <div class="flex items-center mb-4">
    <span class="text-orange-500 mr-2"><i class="fas fa-ticket-alt text-2xl"></i></span>
    <span class="font-semibold text-orange-700 text-lg">Loterías Creadas</span>
  </div>
  <div class="space-y-2">
    <template x-for="loteria in loteriasArray" :key="loteria.id">
      <div class="group flex items-center justify-between rounded-lg px-4 py-2 bg-orange-50 border border-orange-100 shadow-sm hover:shadow-md transition-all duration-200">
        <div class="flex items-center gap-3">
          <div class="rounded-full bg-orange-200 p-2 text-orange-600 shadow-inner">
            <i class="fas fa-ticket-alt text-lg"></i>
          </div>
          <div class="font-bold text-orange-800 text-base" x-text="loteria.nombre"></div>
        </div>
        <div class="flex items-center gap-2">
          <a :href="`/admin/loterias/${loteria.id}/edit`"
             class="text-orange-500 hover:text-orange-700 p-2 rounded hover:bg-orange-100 transition" title="Editar">
            <i class="fas fa-edit"></i>
          </a>
          <button
            type="button"
            @click="openDeleteLoteriaModal(loteria)"
            class="text-red-500 hover:text-red-700 p-2 rounded hover:bg-red-100 transition focus:outline-none"
            title="Eliminar">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>
    </template>
    <template x-if="loteriasArray.length === 0">
      <span class="text-gray-400">No hay loterías registradas.</span>
    </template>
  </div>
</div>
