@extends('layouts.admin')

@section('title', 'Listado de Rifas')

@section('content')
  <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
    <h1 class="text-3xl font-extrabold text-primary flex items-center gap-2">
      <i class="fas fa-ticket-alt"></i> Rifas
    </h1>
    <a href="{{ route('admin.rifas.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-semibold rounded-full shadow-lg transition-transform hover:-translate-y-0.5">
      <i class="fas fa-plus-circle"></i>
      Nueva Rifa
    </a>
  </div>

  @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-900 rounded shadow-sm flex items-center gap-2">
      <i class="fas fa-check-circle"></i>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
    <table class="w-full min-w-[600px]">
      <thead class="bg-gradient-to-r from-primary/5 to-primary/10">
        <tr>
          <th class="px-5 py-3 text-left text-sm font-bold text-primary">ID</th>
          <th class="px-5 py-3 text-left text-sm font-bold text-primary">Nombre</th>
          <th class="px-5 py-3 text-left text-sm font-bold text-primary">Precio</th>
          <th class="px-5 py-3 text-left text-sm font-bold text-primary">Fecha Sorteo</th>
          <th class="px-5 py-3 text-left text-sm font-bold text-primary">Premios Especiales</th>
          <th class="px-5 py-3 text-right text-sm font-bold text-primary">Acciones</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($rifas as $rifa)
          <tr class="hover:bg-primary/5 transition @if($rifa->fecha_sorteo < now()) bg-gray-100 text-gray-400 @endif">
            <td class="px-5 py-3 text-gray-500 font-semibold">{{ $rifa->id }}</td>
            <td class="px-5 py-3 font-bold text-gray-800">
              <span class="flex items-center gap-2">
                <i class="fas fa-star text-primary"></i>
                {{ $rifa->nombre }}
              </span>
            </td>
            <td class="px-5 py-3">
              <span class="inline-block px-2 py-1 bg-green-100 text-green-700 rounded-xl font-mono text-xs shadow">
                {{ '$'.number_format($rifa->precio, 2) }}
              </span>
            </td>
            <td class="px-5 py-3">
              <span class="inline-flex items-center gap-2 text-gray-500 font-medium">
                <i class="fas fa-calendar-alt text-primary"></i>
                {{ $rifa->fecha_sorteo->format('d M Y H:i') }}

                @if($rifa->fecha_sorteo >= now())
                  <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-bold shadow border border-green-300 animate-pulse">
                    ACTIVA
                  </span>
                @else
                  <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-bold shadow border border-red-300 blink">
                    FINALIZADA
                  </span>
                @endif
              </span>
            </td>
            <td class="px-5 py-3">
              @if($rifa->premiosEspeciales->count())
                <span class="inline-flex items-center px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold shadow" title="Cantidad de premios especiales">
                  {{ $rifa->premiosEspeciales->count() }} premios
                  <i class="fas fa-gift ml-1"></i>
                </span>
              @else
                <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-400 rounded-full text-xs font-semibold shadow" title="Sin premios especiales">
                  No
                </span>
              @endif
            </td>
            <td class="px-5 py-3 text-right space-x-1 whitespace-nowrap">
              <a href="{{ route('admin.rifas.show', $rifa) }}"
                 class="inline-flex items-center gap-1 px-3 py-1 bg-green-50 text-green-700 rounded-full hover:bg-green-100 transition"
                 title="Ver detalles">
                <i class="fas fa-eye"></i>
                <span class="hidden sm:inline">Ver</span>
              </a>
              <a href="{{ route('admin.rifas.edit', $rifa) }}"
                 class="inline-flex items-center gap-1 px-3 py-1 bg-blue-50 text-blue-700 rounded-full hover:bg-blue-100 transition"
                 title="Editar">
                <i class="fas fa-edit"></i>
                <span class="hidden sm:inline">Editar</span>
              </a>
              <form action="{{ route('admin.rifas.destroy', $rifa) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1 px-3 py-1 bg-red-50 text-red-700 rounded-full hover:bg-red-100 transition"
                        onclick="return confirm('¿Eliminar esta rifa?')" title="Eliminar">
                  <i class="fas fa-trash"></i>
                  <span class="hidden sm:inline">Eliminar</span>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-5 py-8 text-center text-gray-400">No hay rifas registradas.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-6 flex justify-end">
    {{ $rifas->links() }}
  </div>
@endsection

@push('styles')
<style>
    .blink {
        animation: blink-animation 1s steps(2, start) infinite;
        -webkit-animation: blink-animation 1s steps(2, start) infinite;
    }
    @keyframes blink-animation {
        to {
            visibility: hidden;
        }
    }
</style>
@endpush
