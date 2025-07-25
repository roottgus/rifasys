{{-- resources/views/admin/abonos/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Editar Abono')

@section('content')
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Editar Abono #{{ $abono->id }}</h1>

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
      <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

    <form action="{{ route('admin.abonos.update', $abono) }}" method="POST">
      @csrf
      @method('PUT')

      @include('admin.abonos.partials._form')

      <div class="mt-6 flex space-x-4">
        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Actualizar
        </button>
        <a href="{{ route('admin.abonos.index') }}"
           class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
          Cancelar
        </a>
      </div>
    </form>
  </div>
@endsection
