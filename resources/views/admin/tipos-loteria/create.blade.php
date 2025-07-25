@extends('layouts.admin')

@section('title', 'Nuevo Tipo de Lotería')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow">
  <h1 class="text-2xl font-bold mb-6">Nuevo Tipo de Lotería</h1>

  <form action="{{ route('admin.tipos-loteria.store') }}" method="POST">
    @csrf
    {{-- Aquí incluimos el partial del form, pasamos el array de loterías --}}
    @include('admin.tipos-loteria._form', [
      'loterias' => \App\Models\Loteria::pluck('nombre','id')->toArray(),
      'tipo'     => null
    ])

    <div class="mt-6 flex justify-end">
      <button type="submit"
              class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
        Guardar
      </button>
      <a href="{{ route('admin.loterias.gestion') }}"
         class="ml-4 text-gray-600 hover:underline">
        Cancelar
      </a>
    </div>
  </form>
</div>
@endsection
