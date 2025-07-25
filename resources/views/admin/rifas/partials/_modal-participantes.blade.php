<!-- resources/views/admin/rifas/partials/_modal-participantes.blade.php -->
<div x-data="{
    openModal: false,
    list: [],
    cliente: null,
    loading: false,
    error: null,
    open(rifaId) {
      this.openModal = true;
      this.loading = true;
      this.error = null;
      this.list = [];
      fetch(`/admin/rifas/${rifaId}/participantes`)
        .then(res => {
          if (!res.ok) throw new Error('Error consultando participantes');
          return res.json();
        })
        .then(data => {
          this.list = data;
        })
        .catch(() => {
          this.error = 'Error cargando participantes';
        })
        .finally(() => {
          this.loading = false;
        });
    },
    close() {
      this.openModal = false;
    }
  }"
>
  <!-- Botón para abrir el modal -->
  <button
    @click="open({{ $rifa->id }})"
    class="px-6 py-2 bg-blue-600 text-white rounded-lg text-lg font-semibold shadow hover:bg-blue-700 flex items-center gap-2"
    type="button"
  >
    <i class="fas fa-users"></i> Ver Participantes
  </button>

  <!-- Modal -->
  <div
    x-show="openModal"
    x-cloak
    @keydown.escape.window="close()"
    @click.self="close()"
    class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4"
  >
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full animate-fade-in" @click.stop>
      <!-- Header -->
      <div class="flex justify-between items-center px-6 py-4 border-b">
        <h3 class="text-lg font-bold text-indigo-700">
          <i class="fas fa-users mr-2"></i> Participantes (Rifa Principal)
        </h3>
        <button
          @click="close()"
          class="text-gray-400 hover:text-red-500 text-2xl leading-none"
          aria-label="Cerrar"
          type="button"
        >&times;</button>
      </div>
      <!-- Body -->
      <div class="p-6 max-h-96 overflow-y-auto">
        <template x-if="loading">
          <div class="text-center text-gray-500 py-8">
            <i class="fas fa-spinner fa-spin mr-2"></i> Cargando participantes solventes…
          </div>
        </template>
        <template x-if="error">
          <div class="text-center text-red-600 py-8" x-text="error"></div>
        </template>
        <template x-if="!loading && !error && list.length">
          <ul class="space-y-2">
            <template x-for="item in list" :key="item.numero">
              <li class="flex justify-between items-center border-b pb-2">
                <span>
                  <i class="fas fa-ticket-alt mr-1"></i>
                  #<span x-text="item.numero"></span>
                </span>
                <span class="text-gray-800" x-text="item.cliente"></span>
              </li>
            </template>
          </ul>
        </template>
        <template x-if="!loading && !error && !list.length">
          <div class="text-center text-gray-400 py-8">
            <i class="fas fa-user-slash mr-1"></i>
            No hay participantes solventes en la rifa principal.
          </div>
        </template>
      </div>
      <div class="px-6 py-4 border-t bg-gray-50 text-right">
        <button
          @click="close()"
          class="px-4 py-2 bg-gray-400 hover:bg-gray-600 text-white rounded-lg"
          type="button"
        >Cerrar</button>
      </div>
    </div>
  </div>
</div>
