@extends('layouts.admin')
@section('title','Nueva Rifa')
@section('content')
  <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Nueva Rifa</h1>

    {{-- MENSAJE PROFESIONAL DE ATENCIÓN --}}
    @if (count($loterias) == 0 || count($tiposLoteria) == 0)
        <div class="mb-6 px-4 py-3 rounded-lg bg-yellow-50 border border-yellow-300 flex items-start gap-3 shadow">
            <span class="mt-1 text-yellow-500">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
            </span>
            <div class="flex-1">
                <div class="font-semibold text-yellow-800 text-base mb-1">¡Atención!</div>
                <div class="text-yellow-800 text-sm leading-relaxed">
                    Antes de crear una rifa, asegúrate de que ya tienes registrada al menos una <b>lotería</b> y un <b>tipo de lotería</b>.<br>
                    Puedes agregarlas desde el menú <span class="font-semibold text-yellow-700">Gestión de Loterías</span>.
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.rifas.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6">
      @csrf
      @method('POST')

      {{-- 1) Campos básicos: nombre, descripción, precio, fecha/hora, etc --}}
      @include('admin.rifas._form_fields', [
        'rifa'         => null,
        'loterias'     => $loterias,
        'tiposLoteria' => $tiposLoteria,
      ])

      {{-- 2) Aquí incrustamos tu modal de premios especiales --}}
      @include('admin.rifas.partials._premios-modal', [
        'premios'      => old('premios', []),
        'loterias'     => $loterias,
        'tiposLoteria' => $tiposLoteria,
      ])

      {{-- 3) Botón de envío --}}
      <div class="flex justify-end">
        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Crear Rifa
        </button>
      </div>
    </form>
  </div>
@endsection

{{-- 4) Asegurarnos de que Alpine conoce el componente --}}
@push('scripts')
<script>
  document.addEventListener('alpine:init', () => {
    // presupone que tu app.js ya expone globalmente `premiosModal`
    Alpine.data('premiosModal', window.premiosModal)
  })
</script>
@endpush
