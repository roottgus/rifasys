<!-- resources/views/admin/loterias/partials/_modal-tipo-loteria.blade.php -->
<div x-show="openTipoModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
  <div 
    @click.away="openTipoModal = false; tipoSuccess=''; tipoErrors=[]"
    class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 relative border-t-4 border-primary"
    x-transition
  >
    <h3 class="text-2xl font-extrabold mb-5 text-primary flex items-center gap-2">
      <i class="fas fa-layer-group text-green-500"></i> Nuevo Tipo de Lotería
    </h3>
    <form @submit.prevent="submitTipoLoteria" autocomplete="off" class="space-y-3">
      <!-- Mensaje de éxito -->
      <div x-show="tipoSuccess"
        class="mb-2 rounded-xl py-2 px-3 text-green-800 bg-green-100 border border-green-300 text-center font-semibold flex items-center justify-center gap-2 shadow transition"
        x-transition.opacity
      >
        <i class="fas fa-check-circle"></i>
        <span x-text="tipoSuccess"></span>
      </div>
      <!-- Campo nombre -->
      <div>
        <label for="tipo_nombre" class="block text-sm font-bold text-primary mb-1">Nombre del Tipo</label>
        <input 
          type="text"
          x-model="tipoNombre"
          id="tipo_nombre"
          class="mt-1 block w-full rounded-xl border-2 border-primary/30 focus:border-primary focus:ring-2 focus:ring-primary/30 px-4 py-2 text-gray-800 shadow-sm transition"
          placeholder="Ejemplo: Triple A"
          required
        >
      </div>
      <!-- Asociar a lotería -->
      <div>
        <label for="tipo_loteria_id" class="block text-sm font-bold text-primary mb-1">Asociar a Lotería</label>
        <select
          x-model="tipoLoteriaId"
          id="tipo_loteria_id"
          class="mt-1 block w-full rounded-xl border-2 border-primary/30 focus:border-primary focus:ring-2 focus:ring-primary/30 px-4 py-2 text-gray-800 shadow-sm transition"
          required
        >
          <option value="">-- Selecciona --</option>
          <template x-for="loteria in loteriasArray" :key="loteria.id">
            <option :value="loteria.id" x-text="loteria.nombre"></option>
          </template>
        </select>
      </div>
      <!-- Errores -->
      <div x-show="tipoErrors.length"
        class="rounded-xl py-2 px-3 text-red-800 bg-red-100 border border-red-300 text-center shadow transition"
        x-transition.opacity
      >
        <ul class="space-y-1">
          <template x-for="err in tipoErrors" :key="err">
            <li x-text="err"></li>
          </template>
        </ul>
      </div>
      <!-- Acciones -->
      <div class="flex justify-end gap-3 pt-2">
        <button type="button"
          class="px-4 py-2 rounded-xl font-semibold text-primary border border-primary/40 bg-white hover:bg-primary/10 transition"
          @click="openTipoModal = false; tipoSuccess=''; tipoErrors=[]"
        >
          Cancelar
        </button>
        <button type="submit"
          class="px-5 py-2 rounded-xl font-bold bg-primary text-white shadow hover:bg-primary/90 transition"
        >
          <i class="fas fa-save mr-1"></i> Guardar
        </button>
      </div>
    </form>
    <!-- Botón cerrar (X) arriba derecha -->
    <button type="button"
      @click="openTipoModal = false; tipoSuccess=''; tipoErrors=[]"
      class="absolute top-3 right-4 text-gray-400 hover:text-primary transition text-2xl font-bold"
      title="Cerrar">
      &times;
    </button>
  </div>
</div>
