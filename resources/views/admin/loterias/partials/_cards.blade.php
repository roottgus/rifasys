<div class="flex justify-center gap-6">
  {{-- Agregar Lotería --}}
  <button type="button"
    @click="openLoteriaModal = true"
    class="relative block p-5 w-72 bg-white rounded-xl shadow group transition-all duration-200 hover:shadow-2xl hover:border-indigo-400 border border-indigo-100"
    style="overflow:hidden;">
    <div class="absolute right-3 bottom-3 opacity-10 group-hover:opacity-20 transition-opacity duration-300 pointer-events-none text-indigo-300 text-6xl">
      <i class="fas fa-ticket-alt"></i>
    </div>
    <div class="flex items-center mb-1">
      <span class="text-indigo-600 mr-2"><i class="fas fa-plus-circle fa-lg"></i></span>
      <h2 class="text-lg font-semibold text-indigo-800">Agregar Lotería</h2>
    </div>
    <p class="text-gray-600 text-sm">Define un nuevo nombre de Lotería.</p>
  </button>

  {{-- Agregar Tipo de Lotería --}}
  <button type="button"
    @click="openTipoModal = true"
    class="relative block p-5 w-72 bg-white rounded-xl shadow group transition-all duration-200 hover:shadow-2xl hover:border-green-400 border border-green-100"
    style="overflow:hidden;">
    <div class="absolute right-3 bottom-3 opacity-10 group-hover:opacity-20 transition-opacity duration-300 pointer-events-none text-green-400 text-6xl">
      <i class="fas fa-layer-group"></i>
    </div>
    <div class="flex items-center mb-1">
      <span class="text-green-600 mr-2"><i class="fas fa-layer-group fa-lg"></i></span>
      <h2 class="text-lg font-semibold text-green-800">Agregar Tipo de Lotería</h2>
    </div>
    <p class="text-gray-600 text-sm">Crea un nuevo tipo (ej: "Triple C") y asígnalo a una Lotería existente.</p>
  </button>
</div>
