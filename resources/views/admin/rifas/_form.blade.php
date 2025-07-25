{{-- resources/views/admin/rifas/_form.blade.php --}}
@props([ 'action', 'method' => 'POST', 'rifa' => null, 'loterias', 'tiposLoteria' ])

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
  @csrf
  @if($method === 'PUT') @method('PUT') @endif

  {{-- Nombre de la Rifa --}}
  <x-form.input
    name="nombre"
    label="Nombre de la Rifa"
    :value="old('nombre', $rifa->nombre ?? '')"
    required
  />

  {{-- Descripción (opcional) --}}
  <x-form.textarea
    name="descripcion"
    label="Descripción (opcional)"
  >{{ old('descripcion', $rifa->descripcion ?? '') }}</x-form.textarea>

  {{-- Selección de Lotería --}}
  <x-form.select
    name="loteria_id"
    label="Lotería"
    :options="$loterias"
    :selected="old('loteria_id', $rifa->loteria_id ?? '')"
    placeholder="-- Selecciona una lotería --"
    required
  />

  {{-- Selección de Tipo de Lotería --}}
  <x-form.select
    name="tipo_loteria"
    label="Tipo de Lotería"
    :options="$tiposLoteria"
    :selected="old('tipo_loteria', $rifa->tipo_loteria ?? '')"
    placeholder="-- Selecciona un tipo --"
    required
  />

  {{-- Precio y Cantidad de Números --}}
  <div class="grid grid-cols-2 gap-4">
    <x-form.input
      name="precio"
      label="Precio (USD)"
      type="number"
      step="0.01"
      :value="old('precio', $rifa->precio ?? '')"
      required
    />
    <x-form.select
      name="cantidad_numeros"
      label="Cantidad de Números"
      :options="['100' => '100', '500' => '500', '1000' => '1,000']"
      :selected="old('cantidad_numeros', $rifa->cantidad_numeros ?? '')"
      placeholder="-- Selecciona cantidad --"
      required
    />
  </div>

  {{-- Fecha y Hora de Sorteo --}}
  <div class="grid grid-cols-2 gap-4">
    <x-form.input
      name="fecha_sorteo"
      label="Fecha de Sorteo"
      type="date"
      :value="old('fecha_sorteo', optional($rifa)->fecha_sorteo?->toDateString())"
      required
    />
    <x-form.input
      name="hora_sorteo"
      label="Hora de Sorteo"
      type="time"
      :value="old('hora_sorteo', optional($rifa)->hora_sorteo?->format('H:i'))"
      required
    />
  </div>

  {{-- Imagen (opcional) --}}
  <x-form.input
    name="imagen"
    label="Imagen (opcional)"
    type="file"
  />

  {{-- Premios Especiales (modal) --}}
  @include('admin.rifas.partials._premios-modal', [
    'premios'      => old('premios', $rifa?->premiosEspeciales->toArray() ?? []),
    'loterias'     => $loterias,
    'tiposLoteria' => $tiposLoteria,
  ])

  {{-- Botones de acción --}}
  <div class="pt-4 flex items-center">
    <button type="submit"
            class="px-6 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
      {{ $rifa ? 'Actualizar Rifa' : 'Guardar Rifa' }}
    </button>
    <a href="{{ route('admin.rifas.index') }}"
       class="ml-4 text-gray-600 hover:underline">
      Cancelar
    </a>
  </div>
</form>
