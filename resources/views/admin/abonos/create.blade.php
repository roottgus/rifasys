{{-- resources/views/admin/abonos/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Registrar Abono')

@section('content')
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Registrar Abono</h1>

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
      <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

    <form action="{{ route('admin.abonos.store') }}" method="POST">
      @csrf

      @include('admin.abonos.partials._form')

      <div class="mt-6">
        <button type="submit"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
          Guardar Abono
        </button>
      </div>
    </form>
  </div>
@endsection
