@extends('layouts.admin')

@section('title', 'Editar Regla de Descuento')

@section('content')
  <h1 class="text-2xl font-extrabold mb-6">Editar Regla de Descuento</h1>

  <form action="{{ route('descuentos.update', $descuento) }}" method="POST" class="max-w-lg bg-white rounded-xl shadow p-8 space-y-6">
    @csrf
    @method('PUT')

    <div>
      <label for="rifa_id" class="block text-sm font-bold text-primary mb-1">Rifa</label>
      <select name="rifa_id" id="rifa_id" required class="w-full border-primary/30 rounded-xl px-4 py-2">
        <option value="">Seleccione una rifa</option>
        @foreach($rifas as $rifa)
          <option value="{{ $rifa->id }}" {{ old('rifa_id', $descuento->rifa_id) == $rifa->id ? 'selected' : '' }}>{{ $rifa->nombre }}</option>
        @endforeach
      </select>
      @error('rifa_id')
        <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
      @enderror
    </div>

    <div>
      <label for="cantidad_minima" class="block text-sm font-bold text-primary mb-1">Cantidad mínima de tickets</label>
      <input type="number" min="1" name="cantidad_minima" id="cantidad_minima" class="w-full border-primary/30 rounded-xl px-4 py-2" value="{{ old('cantidad_minima', $descuento->cantidad_minima) }}" required>
      @error('cantidad_minima')
        <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
      @enderror
    </div>

    <div>
      <label for="porcentaje" class="block text-sm font-bold text-primary mb-1">% de Descuento</label>
      <input type="number" min="1" max="100" step="0.01" name="porcentaje" id="porcentaje" class="w-full border-primary/30 rounded-xl px-4 py-2" value="{{ old('porcentaje', $descuento->porcentaje) }}" required>
      @error('porcentaje')
        <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
      @enderror
    </div>

    <div class="flex gap-2">
      <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-6 py-2 rounded-xl font-bold shadow flex items-center gap-2">
        <i class="fas fa-check"></i> Guardar Cambios
      </button>
      <a href="{{ route('admin.descuentos.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-xl font-bold shadow flex items-center gap-2">
        <i class="fas fa-times"></i> Cancelar
      </a>
    </div>
  </form>
@endsection
