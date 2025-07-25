{{-- resources/views/admin/abonos/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Listado de Abonos')

@section('content')
  <div class="p-6">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Listado de Abonos</h1>
      <a href="{{ route('admin.abonos.create') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
        Nuevo Abono
      </a>
    </div>

    <table class="min-w-full bg-white shadow rounded-lg overflow-hidden">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase">#</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase">Ticket</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase">Rifa</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase">Monto</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase">Método</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase">Referencia</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase">Fecha</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 uppercase">Acciones</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        @foreach($abonos as $abono)
        <tr>
          <td class="px-4 py-2 text-sm text-gray-700">
            {{ $loop->iteration + ($abonos->currentPage() - 1) * $abonos->perPage() }}
          </td>
          <td class="px-4 py-2 text-sm text-gray-700">#{{ $abono->ticket->numero }}</td>
          <td class="px-4 py-2 text-sm text-gray-700">{{ $abono->ticket->rifa->nombre }}</td>
          <td class="px-4 py-2 text-sm text-gray-700">${{ number_format($abono->monto, 2) }}</td>
          <td class="px-4 py-2 text-sm text-gray-700">{{ $abono->paymentMethod->name ?? '—' }}</td>
          <td class="px-4 py-2 text-sm text-gray-700">{{ $abono->reference_number }}</td>
          <td class="px-4 py-2 text-sm text-gray-700">{{ $abono->created_at->format('d M Y') }}</td>
          <td class="px-4 py-2 text-sm text-gray-700 space-x-2">
            <a href="{{ route('admin.abonos.edit', $abono) }}" class="text-blue-600 hover:underline">Editar</a>
            <form action="{{ route('admin.abonos.destroy', $abono) }}" method="POST" class="inline">
              @csrf
              @method('DELETE')
              <button type="submit" onclick="return confirm('¿Eliminar este abono?')" class="text-red-600 hover:underline">
                Eliminar
              </button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <div class="mt-4">
      {{ $abonos->withQueryString()->links() }}
    </div>
  </div>
@endsection
