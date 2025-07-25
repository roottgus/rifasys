@extends('layouts.admin')

@section('title', 'Loterías')

@section('content')
<div class="p-6">
  <a href="{{ route('admin.loterias.create') }}"
     class="mb-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded">
    Nueva Lotería
  </a>

  <div class="overflow-x-auto bg-white shadow rounded-lg">
    <table class="min-w-full">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left">Nombre</th>
          <th class="px-4 py-2 text-left">Tipo de Lotería</th>
          <th class="px-4 py-2 text-left">Acciones</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @foreach($loterias as $lot)
          <tr>
            <td class="px-4 py-2">{{ $lot->nombre }}</td>
            <td class="px-4 py-2">{{ $lot->tipo_loteria }}</td>
            <td class="px-4 py-2 space-x-2">
              <a href="{{ route('admin.loterias.edit', $lot) }}"
                 class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                Editar
              </a>
              <form action="{{ route('admin.loterias.destroy', $lot) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button type="submit" onclick="return confirm('¿Eliminar esta lotería?')"
                        class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                  Eliminar
                </button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $loterias->links() }}
  </div>
</div>
@endsection
