@extends('layouts.admin')

@section('title', 'Editar Tipo de Lotería')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow">
  <h1 class="text-2xl font-bold mb-6">Editar Tipo de Lotería</h1>

  <form action="{{ route('admin.tipos-loteria.update', $tiposLoteria) }}"
        method="POST">
    @csrf
    @method('PUT')
    @include('admin.tipos-loteria._form', [
      'loterias' => \App\Models\Loteria::pluck('nombre','id')->toArray(),
      'tipo'     => $tiposLoteria
    ])

    <div class="mt-6 flex justify-end">
      <button type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Actualizar
      </button>
      <a href="{{ route('admin.tipos-loteria.index') }}"
         class="ml-4 text-gray-600 hover:underline">
        Cancelar
      </a>
    </div>
  </form>
</div>
@endsection
