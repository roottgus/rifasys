@extends('layouts.admin')

@section('title', 'Tipos de Lotería')

@section('content')
<div class="p-6">
  <a href="{{ route('admin.tipos-loteria.create') }}"
     class="mb-4 inline-block px-4 py-2 bg-green-600 text-white rounded">
    Nuevo Tipo de Lotería
  </a>

  <div class="overflow-x-auto bg-white shadow rounded-lg">
    <table class="min-w-full">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left">Lotería</th>
          <th class="px-4 py-2 text-left">Tipo</th>
          <th class="px-4 py-2 text-left">Acciones</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @foreach($tipos as $tipo)
          <tr>
            <td class="px-4 py-2">{{ $tipo->loteria->nombre }}</td>
            <td class="px-4 py-2">{{ $tipo->nombre }}</td>
            <td class="px-4 py-2 space-x-2">
              <a href="{{ route('admin.tipos-loteria.edit', $tipo) }}"
                 class="px-2 py-1 bg-blue-500 text-white rounded">
                Editar
              </a>
              <form action="{{ route('admin.tipos-loteria.destroy', $tipo) }}"
                    method="POST" class="inline">
                @csrf @method('DELETE')
                <button type="submit"
                        onclick="return confirm('Eliminar este tipo?')"
                        class="px-2 py-1 bg-red-500 text-white rounded">
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
    {{ $tipos->links() }}
  </div>
</div>
@endsection
