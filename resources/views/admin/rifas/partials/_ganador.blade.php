<div
  x-data="{
    openModal: false,
    list: [],
    cliente: null,
    loading: false,
    error: null,
    ganadorNumero: '',
    principalData: null,
    principalError: '',
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
    },
    confirmarGanador(rifaId) {
      this.principalError = '';
      this.principalData = null;
      if (!this.ganadorNumero) {
        this.principalError = 'Indica el número ganador';
        return;
      }
      fetch(`/admin/rifas/${rifaId}/winner`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
        },
        body: JSON.stringify({ numero: this.ganadorNumero }),
      })
        .then(res => res.json())
        .then(data => {
          if (data.cliente) {
            this.principalData = {
              numero: data.ticket.numero,
              cliente: data.cliente.nombre,
            };
            this.principalError = '';
          } else {
            this.principalError = data.message || 'No se pudo confirmar ganador';
          }
        })
        .catch(() => {
          this.principalError = 'Error confirmando ganador';
        });
    }
  }"
  class="bg-white border shadow rounded-xl px-6 py-6 mb-8"
>
  <h2 class="text-xl sm:text-2xl font-semibold mb-4 flex items-center gap-2">
    <i class="fas fa-crown text-yellow-400"></i> Ganador Rifa Principal
  </h2>

  <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-4">
    <input
      type="number"
      x-model="ganadorNumero"
      placeholder="N° ganador"
      class="w-32 border-2 border-orange-200 rounded-lg px-3 py-1.5 text-base focus:ring-2 focus:ring-orange-300"
    />
    <button
      @click="confirmarGanador({{ $rifa->id }})"
      class="px-4 py-1.5 bg-indigo-600 text-white rounded-md text-base font-semibold shadow hover:bg-indigo-700 flex items-center gap-2 transition"
      type="button"
    >
      <i class="fas fa-check-circle"></i> Confirmar
    </button>
    <button
      @click="open({{ $rifa->id }})"
      class="px-4 py-1.5 bg-blue-600 text-white rounded-md text-base font-semibold shadow hover:bg-blue-700 flex items-center gap-2 transition"
      type="button"
    >
      <i class="fas fa-users"></i> Participantes
    </button>
  </div>

  <!-- Resultado tras confirmar -->
  <template x-if="principalData">
    <div class="text-green-700 font-semibold mt-2">
      Ganador: #<span x-text="principalData.numero"></span> – <span x-text="principalData.cliente"></span>
    </div>
  </template>
  <template x-if="principalError">
    <div class="text-red-600 font-medium mt-2" x-text="principalError"></div>
  </template>

  <!-- MODAL LOCAL DE PARTICIPANTES -->
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
          class="px-4 py-1.5 bg-gray-400 hover:bg-gray-600 text-white rounded-md text-base transition"
          type="button"
        >Cerrar</button>
      </div>
    </div>
  </div>
</div>
