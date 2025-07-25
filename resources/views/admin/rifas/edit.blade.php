{{-- resources/views/admin/rifas/edit.blade.php --}}
@extends('layouts.admin')
@section('title','Editar Rifa')
@section('content')
  <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Editar Rifa</h1>

    <form action="{{ route('admin.rifas.update', $rifa) }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6">
      @csrf
      @method('PUT')

      {{-- 1) Campos básicos: nombre, descripción, imagen, precio, fecha/hora, etc. --}}
      @include('admin.rifas._form_fields', [
        'rifa'         => $rifa,
        'loterias'     => $loterias,
        'tiposLoteria' => $tiposLoteria,
      ])

      {{-- 2) Modal de premios especiales --}}
      @include('admin.rifas.partials._premios-modal', [
        // si vuelven errores de validación, old('premios') prevalece
        'premios'      => old('premios', $rifa->premiosEspeciales->toArray()),
        'loterias'     => $loterias,
        'tiposLoteria' => $tiposLoteria,
      ])

      {{-- 3) Botón de envío --}}
      <div class="flex justify-end">
        <button type="submit"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
          Actualizar Rifa
        </button>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
<script>
  document.addEventListener('alpine:init', () => {
    // Asegúrate de que window.premiosModal está definido en tu app.js
    Alpine.data('premiosModal', window.premiosModal)
  })
</script>
@endpush
