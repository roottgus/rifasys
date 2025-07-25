@props(['loterias' => [], 'tipo' => null])

<div class="space-y-4">
  {{-- Lotería padre (OBLIGATORIO) --}}
  <div>
    <label for="loteria_id" class="block text-sm font-bold text-gray-700">
      Lotería <span class="text-red-500">*</span>
    </label>
    <select name="loteria_id" id="loteria_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
            required>
      <option value="">-- Selecciona una lotería --</option>
      @foreach($loterias as $id => $nombre)
        <option value="{{ $id }}"
          {{ old('loteria_id', $tipo->loteria_id ?? '') == $id ? 'selected' : '' }}>
          {{ $nombre }}
        </option>
      @endforeach
    </select>
    @error('loteria_id')
      <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
  </div>

  {{-- Nombre del tipo --}}
  <div>
    <label for="nombre" class="block text-sm font-bold text-gray-700">
      Nombre del Tipo <span class="text-red-500">*</span>
    </label>
    <input type="text" name="nombre" id="nombre"
           value="{{ old('nombre', $tipo->nombre ?? '') }}"
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
           required>
    @error('nombre')
      <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
  </div>
</div>
