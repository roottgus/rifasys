<!-- resources/views/admin/loterias/partials/_modal-loteria.blade.php -->
<div x-show="openLoteriaModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
  <div 
    @click.away="openLoteriaModal = false; loteriaSuccess=''; loteriaErrors=[]"
    class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 relative border-t-4 border-primary"
    x-transition
  >
    <h3 class="text-2xl font-extrabold mb-5 text-primary flex items-center gap-2">
      <i class="fas fa-ticket-alt"></i> Nueva Lotería
    </h3>
    <form @submit.prevent="submitLoteria" autocomplete="off" class="space-y-3">
      <!-- Mensaje de éxito -->
      <div x-show="loteriaSuccess"
        class="mb-2 rounded-xl py-2 px-3 text-green-800 bg-green-100 border border-green-300 text-center font-semibold flex items-center justify-center gap-2 shadow transition"
        x-transition.opacity
      >
        <i class="fas fa-check-circle"></i>
        <span x-text="loteriaSuccess"></span>
      </div>
      <!-- Campo nombre -->
      <div>
        <label for="nombre_loteria" class="block text-sm font-bold text-primary mb-1">Nombre de la Lotería</label>
        <input 
          type="text"
          x-model="loteriaNombre"
          id="nombre_loteria"
          class="mt-1 block w-full rounded-xl border-2 border-primary/30 focus:border-primary focus:ring-2 focus:ring-primary/30 px-4 py-2 text-gray-800 shadow-sm transition"
          placeholder="Ejemplo: Lotería del Táchira"
          required
        >
      </div>
      <!-- Errores -->
      <div x-show="loteriaErrors.length"
        class="rounded-xl py-2 px-3 text-red-800 bg-red-100 border border-red-300 text-center shadow transition"
        x-transition.opacity
      >
        <ul class="space-y-1">
          <template x-for="err in loteriaErrors" :key="err">
            <li x-text="err"></li>
          </template>
        </ul>
      </div>
      <!-- Acciones -->
      <div class="flex justify-end gap-3 pt-2">
        <button type="button"
          class="px-4 py-2 rounded-xl font-semibold text-primary border border-primary/40 bg-white hover:bg-primary/10 transition"
          @click="openLoteriaModal = false; loteriaSuccess=''; loteriaErrors=[]"
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
      @click="openLoteriaModal = false; loteriaSuccess=''; loteriaErrors=[]"
      class="absolute top-3 right-4 text-gray-400 hover:text-primary transition text-2xl font-bold"
      title="Cerrar">
      &times;
    </button>
  </div>
</div>
