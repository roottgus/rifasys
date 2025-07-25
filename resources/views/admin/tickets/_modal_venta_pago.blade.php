{{-- Paso 2: Selección y datos de método de pago --}}
<div x-show="paso === 2" x-transition>
  <h4 class="text-lg font-semibold mb-3 text-center">
    <i class="fas fa-credit-card text-primary"></i> Método de Pago
  </h4>

  {{-- Mensaje de error general de pago --}}
  <template x-if="errorPago">
    <div class="mb-3 text-red-700 bg-red-100 border border-red-300 rounded px-3 py-2 flex items-center gap-2">
      <i class="fas fa-exclamation-triangle"></i>
      <span x-text="errorPago"></span>
    </div>
  </template>

  {{-- Grid principal: 3 columnas en md+ (cada una con ancho fijo) --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    {{-- ====================================== --}}
    {{-- Columna 1: botones de métodos (200px) --}}
    {{-- ====================================== --}}
    <div class="md:col-span-1 md:w-[250px] mx-auto">
      <template x-for="mp in metodosPagoActivos" :key="mp.key">
        <button
          type="button"
          @click="seleccionarMetodoPago(mp.key)"
          :class="metodoPago === mp.key
            ? 'border-2 border-primary bg-primary/5 text-primary shadow-lg'
            : 'border border-gray-200 bg-white hover:bg-gray-50 text-gray-700'"
          class="w-full flex items-center px-4 py-4 mb-3 rounded-lg relative overflow-hidden group transition-all duration-150 focus:outline-none"
        >
          {{-- Watermark muy tenue --}}
          <span class="absolute left-3 top-1/2 -translate-y-1/2 opacity-10 text-6xl pointer-events-none group-hover:opacity-20 transition-all duration-200">
            <i :class="mp.icon"></i>
          </span>
          <span class="relative z-10 ml-2 text-base font-semibold" x-text="mp.name"></span>
          <span x-show="metodoPago === mp.key" class="ml-auto z-10 relative">
            <i class="fas fa-check-circle text-primary"></i>
          </span>
        </button>
      </template>
    </div>

    {{-- =============================================== --}}
    {{-- Columna 2: datos de la empresa (300px, centrado) --}}
    {{-- =============================================== --}}
    <div class="md:col-span-1 md:w-[300px] mx-auto">
      <template x-if="!metodoPago">
        <div class="text-gray-400 text-center py-8 border border-gray-200 rounded-lg">
          Selecciona un método para ver los datos de la empresa aquí.
        </div>
      </template>

      <template x-if="metodoPago">
        <div class="relative bg-primary/5 border-2 border-primary/20 rounded-xl p-4 animate-fade-in shadow-md overflow-hidden">
          {{-- Watermark icono algo más pequeño --}}
          <span class="absolute left-1/2 bottom-2 -translate-x-1/2 opacity-10 text-[50px] pointer-events-none select-none" style="z-index:1;">
            <i :class="obtenerDetallesMetodo().icon"></i>
          </span>

          <div class="relative z-10 mb-4 text-center">
            <div class="font-bold text-primary text-base" x-text="obtenerDetallesMetodo().name"></div>
            <div class="text-primary/80 text-xs mt-1" x-text="obtenerDetallesMetodo().descripcion"></div>
            <div class="text-gray-500 text-xs mt-1" x-text="obtenerDetallesMetodo().info"></div>
          </div>

          <div class="space-y-3 relative z-10">
            <template x-for="(field, idx) in obtenerDetallesMetodo().fields" :key="field.key">
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-0.5" x-text="field.label"></label>
                <input
                  type="text"
                  :value="field.value"
                  class="w-full border border-gray-200 rounded px-2 py-1 text-xs bg-gray-50 text-gray-700 cursor-not-allowed"
                  readonly
                >
              </div>
            </template>
          </div>
        </div>
      </template>
    </div>

    {{-- ========================================================= --}}
    {{-- Columna 3: formulario “Reportar mi pago” (280px, centrado) --}}
    {{-- ========================================================= --}}
    <div class="md:col-span-1 md:w-[230px] mx-auto">
      <template x-if="!metodoPago">
        <div class="text-gray-400 text-center py-8 border border-gray-200 rounded-lg">
          Aquí aparecerá el formulario para reportar tu pago.
        </div>
      </template>

      <template x-if="metodoPago">
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-md">
          <h5 class="text-lg font-semibold mb-3 text-gray-700">Reportar mi pago</h5>
          <p class="text-sm text-gray-500 mb-4">
            Ingresa los datos del comprobante para que el sistema valide tu pago.
          </p>

          <div class="space-y-4">
            <template x-for="field in reportFields()" :key="field.key">

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1" x-text="field.label"></label>

                {{-- Si el campo es “select” --}}
                <template x-if="field.type === 'select'">
                  <select
                    x-model="pagoDatos[field.key]"
                    class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-primary"
                    required
                  >
                    <option value="" disabled selected>-- Selecciona una opción --</option>
                    <template x-for="opt in field.options" :key="opt.value">
                      <option :value="opt.value" x-text="opt.label"></option>
                    </template>
                  </select>
                </template>

                {{-- Si el campo es “date” --}}
                <template x-if="field.type === 'date'">
                  <input
                    type="date"
                    x-model="pagoDatos[field.key]"
                    class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-primary"
                    required
                  >
                </template>

                {{-- Si el campo es “number” --}}
                <template x-if="field.type === 'number'">
                  <input
                    type="number"
                    step="0.01"
                    x-model="pagoDatos[field.key]"
                    placeholder="Ej: 100.00"
                    class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-primary"
                    required
                  >
                </template>

                {{-- Si el campo es “text” --}}
                <template x-if="field.type === 'text'">
                  <input
                    type="text"
                    x-model="pagoDatos[field.key]"
                    :placeholder="'Ingresa ' + field.label.toLowerCase()"
                    class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-primary"
                    required
                  >
                </template>
              </div>
            </template>
          </div>
        </div>
      </template>
    </div>
  </div>

  {{-- Botones de navegación --}}
  <div class="flex justify-between gap-2 mt-6">
    <button
      type="button"
      @click="paso = 1"
      class="px-3 py-1 border rounded border-gray-300 text-gray-700 hover:bg-gray-100"
    >
      Atrás
    </button>
    
  </div>
</div>
