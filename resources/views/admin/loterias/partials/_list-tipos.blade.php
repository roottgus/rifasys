<div class="bg-white border-l-4 border-green-300 rounded-xl shadow p-5">
  <div class="flex items-center mb-4">
    <span class="text-green-500 mr-2"><i class="fas fa-layer-group text-2xl"></i></span>
    <span class="font-semibold text-green-700 text-lg">Tipos de Lotería Creados</span>
  </div>
  <div class="space-y-2">
    <template x-for="tipo in tiposLoteria" :key="tipo.id">
      <div class="group flex items-center justify-between rounded-lg px-4 py-2 bg-green-50 border border-green-100 shadow-sm hover:shadow-md transition-all duration-200">
        <div class="flex items-center gap-3">
          <div class="rounded-full bg-green-200 p-2 text-green-600 shadow-inner">
            <i class="fas fa-layer-group text-lg"></i>
          </div>
          <div>
            <div class="font-bold text-green-800 text-base" x-text="tipo.nombre"></div>
            <div class="text-xs text-gray-500" x-show="loterias[tipo.loteria_id]">
              <i class="fas fa-ticket-alt mr-1"></i>
              <span x-text="loterias[tipo.loteria_id]?.nombre"></span>
            </div>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <a :href="`/admin/tipos-loteria/${tipo.id}/edit`"
             class="text-green-500 hover:text-green-700 p-2 rounded hover:bg-green-100 transition" title="Editar">
            <i class="fas fa-edit"></i>
          </a>
          <button
            type="button"
            @click="openDeleteTipoLoteriaModal(tipo)"
            class="text-red-500 hover:text-red-700 p-2 rounded hover:bg-red-100 transition focus:outline-none"
            title="Eliminar">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>
    </template>
    <template x-if="tiposLoteria.length === 0">
      <span class="text-gray-400">No hay tipos registrados.</span>
    </template>
  </div>
</div>
