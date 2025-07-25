@extends('layouts.admin')

@section('title', 'Nueva Lotería')

@section('content')

  {{-- BLOQUE DE ERRORES DE VALIDACIÓN --}}
  @if ($errors->any())
    <div class="mb-4 p-3 bg-red-100 text-red-600 rounded">
      <ul class="list-disc pl-5">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.loterias.store') }}"
        method="POST"
        class="max-w-lg space-y-6 bg-white p-6 rounded-lg shadow">
    @csrf

    @include('admin.loterias._form', ['loteria' => null])

    <div class="pt-4">
      <button type="submit"
              class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
        Guardar Lotería
      </button>
      <a href="{{ route('admin.loterias.gestion') }}"
         class="ml-4 text-gray-600 hover:underline">
        Cancelar
      </a>
    </div>
  </form>
@endsection
