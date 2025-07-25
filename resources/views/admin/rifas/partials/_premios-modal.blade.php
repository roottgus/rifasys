@php
    use Illuminate\Support\Str;

    // Calcula si hay premios por defecto
    $initialHas  = old('has_premios', !empty($premios));
    $rawPremios  = old('premios', $premios);

    // Premios listos para JS
    $premiosData = collect($rawPremios)
        ->map(fn($p) => [
            'loteria_id'      => $p['loteria_id'] ?? '',
            'tipo_loteria_id' => $p['tipo_loteria_id'] ?? '',
            'tipo_premio'     => $p['tipo_premio'] ?? '',
            'monto'           => $p['monto'] ?? '',
            'detalle_articulo'=> $p['detalle_articulo'] ?? '',
            'abono_minimo'    => $p['abono_minimo'] ?? '',
            'fecha_premio'    => $p['fecha_premio'] ?? '',
            'hora_premio'     => $p['hora_premio'] ?? '',
            'descripcion'     => $p['descripcion'] ?? '',
        ])
        ->values()
        ->toJson();

    // Loterías [{ id, nombre }]
    $loteriasJs = collect($loterias)
        ->map(fn($nombre, $id) => ['id' => $id, 'nombre' => $nombre])
        ->values()
        ->toJson();

    // Tipos de lotería [{ id, nombre, loteria_id }]
    $tiposJs = \App\Models\TipoLoteria::select('id', 'nombre', 'loteria_id')->get()->toJson();

    // Errores relacionados a premios (solo para este modal)
    $errorsJs = collect($errors->messages())
        ->filter(fn($msgs, $key) => Str::startsWith($key, 'premios'))
        ->flatten()
        ->toJson();
@endphp

<div
  x-data='premiosModal({
    initialHas: {{ $initialHas ? "true" : "false" }},
    initialPremios: {!! $premiosData !!},
    loterias: {!! $loteriasJs !!},
    tiposLoteria: {!! $tiposJs !!},
    premioErrors: {!! $errorsJs !!}
  })'
  x-cloak
>

  <input type="hidden" name="has_premios" :value="hasPremios ? 1 : 0">

  <label class="inline-flex items-center space-x-2 mt-4">
    <input type="checkbox"
           x-model="hasPremios"
           @change="toggleModal()"
           class="form-checkbox h-5 w-5 text-orange-600">
    <span>Tendrá premios especiales?</span>
  </label>

  <!-- Modal Premios -->
  <div
    x-show="modalOpen"
    @keydown.escape.window="modalOpen = false"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
  >
    <div
      @click.away="modalOpen = false"
      class="relative bg-white rounded-lg p-6 w-full max-w-md max-h-[80vh] overflow-auto"
    >
      <button @click="modalOpen = false"
              class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl font-bold">
        &times;
      </button>

      <h2 class="text-xl font-semibold mb-4">Premios Especiales</h2>

      <!-- Errores -->
      <template x-if="premioErrors.length">
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
          <ul class="list-disc pl-5">
            <template x-for="(err, i) in premioErrors" :key="i">
              <li x-text="err"></li>
            </template>
          </ul>
        </div>
      </template>

      <!-- Premios dinámicos -->
      <template x-for="(premio, idx) in premios" :key="idx">
        <div class="mb-4 border rounded p-3 space-y-2">
          <!-- Lotería -->
          <div>
            <label class="block text-sm font-semibold">Lotería <span class="text-red-500">*</span></label>
            <select
              :name="`premios[${idx}][loteria_id]`"
              x-model="premio.loteria_id"
              class="w-full border rounded p-1"
              @change="onLoteriaChange(premio)"
              required
            >
              <option value="">-- Selecciona --</option>
              <template x-for="lot in loterias" :key="lot.id">
                <option :value="lot.id" x-text="lot.nombre"></option>
              </template>
            </select>
          </div>

          <!-- Tipo de Lotería dependiente -->
          <div>
            <label class="block text-sm font-semibold">Tipo de Lotería <span class="text-red-500">*</span></label>
            <select
              :name="`premios[${idx}][tipo_loteria_id]`"
              x-model="premio.tipo_loteria_id"
              class="w-full border rounded p-1"
              :disabled="!premio.loteria_id"
              required
            >
              <option value="">-- Selecciona --</option>
              <template x-for="tipo in tiposLoteriaByLoteriaId(premio.loteria_id)" :key="tipo.id">
                <option :value="tipo.id" x-text="tipo.nombre"></option>
              </template>
            </select>
          </div>

          <!-- Tipo de Premio -->
          <div>
            <label class="block text-sm font-semibold">Tipo de Premio <span class="text-red-500">*</span></label>
            <select
              :name="`premios[${idx}][tipo_premio]`"
              x-model="premio.tipo_premio"
              class="w-full border rounded p-1"
              required
            >
              <option value="">-- Selecciona --</option>
              <option value="dinero">Dinero (USD)</option>
              <option value="articulo">Artículo</option>
              <option value="moto">Moto</option>
              <option value="otro">Otro</option>
            </select>
          </div>

          <!-- Monto o Detalle (según tipo) -->
          <template x-if="premio.tipo_premio === 'dinero'">
            <div>
              <label class="block text-sm font-semibold">
                Monto (USD) <span class="text-red-500">*</span>
                <i class="fas fa-info-circle ml-1 text-xs text-gray-400" title="Solo si el premio es dinero"></i>
              </label>
              <input
                :name="`premios[${idx}][monto]`"
                x-model="premio.monto"
                type="number" step="0.01"
                class="w-full border rounded p-1"
                :required="premio.tipo_premio === 'dinero'"
              >
            </div>
          </template>
          <template x-if="premio.tipo_premio !== 'dinero' && premio.tipo_premio !== ''">
            <div>
              <label class="block text-sm font-semibold">
                Detalle del Premio <span class="text-red-500">*</span>
                <i class="fas fa-info-circle ml-1 text-xs text-gray-400" title="Descripción breve del artículo, moto, u otro premio"></i>
              </label>
              <input
                :name="`premios[${idx}][detalle_articulo]`"
                x-model="premio.detalle_articulo"
                type="text"
                class="w-full border rounded p-1"
                :required="premio.tipo_premio !== 'dinero'"
              >
            </div>
          </template>

          <!-- Abono mínimo -->
          <div>
            <label class="block text-sm font-semibold">
              Abono Mínimo (USD) <span class="text-red-500">*</span>
              <i class="fas fa-info-circle ml-1 text-xs text-gray-400" title="Monto mínimo abonado para participar en este premio especial."></i>
            </label>
            <input
              :name="`premios[${idx}][abono_minimo]`"
              x-model="premio.abono_minimo"
              type="number" step="0.01"
              class="w-full border rounded p-1"
              required
            >
          </div>

          <!-- Fecha & Hora -->
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block text-sm font-semibold">Fecha Premio <span class="text-red-500">*</span></label>
              <input
                :name="`premios[${idx}][fecha_premio]`"
                x-model="premio.fecha_premio"
                type="date"
                class="w-full border rounded p-1"
                required
              >
            </div>
            <div>
              <label class="block text-sm font-semibold">Hora Premio <span class="text-red-500">*</span></label>
              <input
                :name="`premios[${idx}][hora_premio]`"
                x-model="premio.hora_premio"
                type="time"
                class="w-full border rounded p-1"
                required
              >
            </div>
          </div>

          <!-- Descripción (opcional) -->
          <div>
            <label class="block text-sm font-semibold">Descripción</label>
            <input
              :name="`premios[${idx}][descripcion]`"
              x-model="premio.descripcion"
              type="text"
              class="w-full border rounded p-1"
            >
          </div>

          <button type="button"
                  @click="removePremio(idx)"
                  class="mt-2 text-red-600 hover:underline text-sm">
            Eliminar
          </button>
        </div>
      </template>

      <button type="button"
              @click="addPremio()"
              class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
              x-show="hasPremios">
        + Añadir premio
      </button>

      <div class="mt-6 flex justify-end">
        <button type="button"
                @click="modalOpen = false"
                class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
          Listo
        </button>
      </div>
    </div>
  </div>
</div>
