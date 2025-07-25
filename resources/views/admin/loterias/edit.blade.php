@extends('layouts.admin')

@section('title', 'Editar Lotería')

@section('content')
  <form action="{{ route('admin.loterias.update', $loteria) }}"
        method="POST"
        class="max-w-lg space-y-6 bg-white p-6 rounded-lg shadow">
    @csrf
    @method('PUT')

    @include('admin.loterias._form', [
      'loteria'      => $loteria,
      'tiposLoteria' => $tiposLoteria,
    ])

    <div class="pt-4">
      <button type="submit"
              class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
        Actualizar
      </button>
      <a href="{{ route('admin.loterias.index') }}"
         class="ml-4 text-gray-600 hover:underline">
        Cancelar
      </a>
    </div>
  </form>
@endsection
