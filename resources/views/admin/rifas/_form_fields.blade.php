<div x-data="formRifa()" x-init="initTipoLoteria()" class="space-y-5">

  {{-- Nombre --}}
  <div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">
      <i class="fas fa-heading mr-1 text-orange-400"></i> Nombre de la Rifa <span class="text-red-500">*</span>
    </label>
    <input type="text"
           name="nombre"
           value="{{ old('nombre', optional($rifa)->nombre) }}"
           class="w-full border border-orange-200 focus:border-orange-400 rounded-lg p-2 focus:ring-2 focus:ring-orange-100 transition"
           required
           placeholder="Ejemplo: Rifa de la Moto AKT 2024">
    @error('nombre')
      <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
    @enderror
  </div>

  {{-- Descripción --}}
  <div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">
      <i class="fas fa-align-left mr-1 text-orange-400"></i> Descripción
    </label>
    <textarea name="descripcion"
              class="w-full border border-orange-200 focus:border-orange-400 rounded-lg p-2 focus:ring-2 focus:ring-orange-100 transition"
              rows="2"
              placeholder="Breve detalle, reglas o premios destacados">{{ old('descripcion', optional($rifa)->descripcion) }}</textarea>
    @error('descripcion')
      <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
    @enderror
  </div>

  {{-- Imagen --}}
  <div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">
      <i class="fas fa-image mr-1 text-orange-400"></i> Imagen (opcional)
      <span class="text-gray-400 font-normal">(JPG, PNG, SVG máx. 2MB)</span>
    </label>
    <input type="file"
           name="imagen"
           class="w-full border border-orange-200 focus:border-orange-400 rounded-lg p-2 bg-white focus:ring-2 focus:ring-orange-100 transition">
    @if(optional($rifa)->imagen)
      <div class="mt-2">
        <img src="{{ asset('storage/rifas/' . $rifa->imagen) }}"
             alt="Imagen actual"
             class="h-16 rounded shadow inline-block mr-2">
        <span class="text-xs text-gray-500">Imagen actual</span>
      </div>
    @endif
    @error('imagen')
      <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
    @enderror
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Lotería --}}
    <div>
      <label class="block text-sm font-semibold text-gray-700 mb-1">
        <i class="fas fa-th-large mr-1 text-orange-400"></i> Lotería <span class="text-red-500">*</span>
      </label>
      <select x-model="loteria_id"
              @change="fetchTipos()"
              name="loteria_id"
              class="w-full border border-orange-200 focus:border-orange-400 rounded-lg p-2 focus:ring-2 focus:ring-orange-100 transition"
              required>
        <option value="">-- Selecciona --</option>
        @foreach($loterias as $id => $nombre)
          <option value="{{ $id }}">{{ $nombre }}</option>
        @endforeach
      </select>
      @error('loteria_id')
        <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
      @enderror
    </div>

    {{-- Tipo de Lotería (hijo del padre seleccionado) --}}
    <div>
      <label class="block text-sm font-semibold text-gray-700 mb-1">
        <i class="fas fa-list-ol mr-1 text-orange-400"></i> Tipo de Lotería <span class="text-red-500">*</span>
      </label>
      <select x-model="tipo_loteria_id"
              name="tipo_loteria_id"
              class="w-full border border-orange-200 focus:border-orange-400 rounded-lg p-2 focus:ring-2 focus:ring-orange-100 transition"
              :disabled="tipos.length === 0"
              required>
        <option value="">-- Selecciona --</option>
        <template x-for="tipo in tipos" :key="tipo.id">
          <option :value="tipo.id" x-text="tipo.nombre"></option>
        </template>
      </select>
      @error('tipo_loteria_id')
        <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- Precio --}}
    <div>
      <label class="block text-sm font-semibold text-gray-700 mb-1">
        <i class="fas fa-dollar-sign mr-1 text-orange-400"></i> Precio por ticket (USD) <span class="text-red-500">*</span>
      </label>
      <input type="number"
             name="precio"
             step="0.01"
             value="{{ old('precio', optional($rifa)->precio) }}"
             class="w-full border border-orange-200 focus:border-orange-400 rounded-lg p-2 focus:ring-2 focus:ring-orange-100 transition"
             required
             placeholder="Ej: 2.00">
      @error('precio')
        <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
      @enderror
    </div>

    {{-- Cantidad de números --}}
    <div>
      <label class="block text-sm font-semibold text-gray-700 mb-1">
        <i class="fas fa-list-ol mr-1 text-orange-400"></i> Cantidad de números <span class="text-red-500">*</span>
      </label>
      <input type="number"
             name="cantidad_numeros"
             min="1"
             value="{{ old('cantidad_numeros', optional($rifa)->cantidad_numeros ?? 100) }}"
             class="w-full border border-orange-200 focus:border-orange-400 rounded-lg p-2 focus:ring-2 focus:ring-orange-100 transition"
             required
             placeholder="Ej: 100">
      @error('cantidad_numeros')
        <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
      @enderror
    </div>

    {{-- Fecha y hora de sorteo --}}
    <div class="grid grid-cols-2 gap-2">
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">
          <i class="fas fa-calendar-day mr-1 text-orange-400"></i> Fecha de sorteo <span class="text-red-500">*</span>
        </label>
        <input type="date"
               name="fecha_sorteo"
               value="{{ old('fecha_sorteo', optional($rifa)->fecha_sorteo) }}"
               class="w-full border border-orange-200 focus:border-orange-400 rounded-lg p-2 focus:ring-2 focus:ring-orange-100 transition"
               required>
        @error('fecha_sorteo')
          <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
        @enderror
      </div>
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">
          <i class="fas fa-clock mr-1 text-orange-400"></i> Hora de sorteo
        </label>
        <input type="time"
               name="hora_sorteo"
               value="{{ old('hora_sorteo', optional($rifa)->hora_sorteo) }}"
               class="w-full border border-orange-200 focus:border-orange-400 rounded-lg p-2 focus:ring-2 focus:ring-orange-100 transition">
        @error('hora_sorteo')
          <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
        @enderror
      </div>
    </div>
  </div>
</div>
{{-- Alpine.js PARA SELECTS DEPENDIENTES --}}
@push('scripts')
<script>
function formRifa() {
  return {
    loteria_id: '{{ old('loteria_id', optional($rifa)->loteria_id) }}',
    tipo_loteria_id: '{{ old('tipo_loteria_id', optional($rifa)->tipo_loteria_id) }}',
    tipos: [],
    fetchTipos() {
      if (!this.loteria_id) {
        this.tipos = [];
        this.tipo_loteria_id = '';
        return;
      }
      fetch(`/admin/loterias/${this.loteria_id}/tipos`)
        .then(res => res.json())
        .then(data => {
          this.tipos = data;
          if (!this.tipos.some(t => t.id == this.tipo_loteria_id)) {
            this.tipo_loteria_id = '';
          }
        });
    },
    initTipoLoteria() {
      if (this.loteria_id) {
        this.fetchTipos();
      }
    }
  }
}
</script>
@endpush