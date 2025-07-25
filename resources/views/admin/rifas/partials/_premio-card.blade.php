{{-- 
  Partial: Tarjeta Premio Especial
  Props: 
    - $premio (App\Models\PremioEspecial)
    - $color (string, opcional: 'green', 'yellow', 'blue'…)
--}}
@php
  $color = $color ?? 'green';
@endphp

<div 
    x-data="{ numero: '' }"
    class="bg-{{ $color }}-50 text-{{ $color }}-600 border border-{{ $color }}-200 rounded-lg p-4 shadow transition flex flex-col gap-2"
>
    <h3 class="text-lg font-semibold capitalize flex items-center gap-2">
        @if($premio->tipo_premio === 'dinero')
            <i class="fas fa-money-bill-wave"></i>
        @elseif($premio->tipo_premio === 'articulo')
            <i class="fas fa-gift"></i>
        @elseif($premio->tipo_premio === 'moto')
            <i class="fas fa-motorcycle"></i>
        @else
            <i class="fas fa-award"></i>
        @endif
        {{ ucfirst($premio->tipo_premio) }}
    </h3>
    <p class="text-xs text-gray-500">Abono mínimo: ${{ number_format($premio->abono_minimo, 2) }}</p>
    @if($premio->detalle_articulo)
        <p class="text-xs text-gray-700">{{ $premio->detalle_articulo }}</p>
    @endif

    <div class="flex space-x-2 mt-2">
        <button
            @click="Alpine.store('participantes').fetch({{ $premio->id }})"
            class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs"
        >
            Ver participantes
        </button>
        <a href="{{ route('admin.premios.participantes.pdf', $premio) }}"
           target="_blank"
           class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs"
        >
            Descargar PDF
        </a>
    </div>

    <div class="flex items-center space-x-2 mt-2">
        <input
            type="number"
            x-model="numero"
            placeholder="N° ganador"
            class="w-24 border rounded p-2 text-sm"
        />
        <button
            @click="Alpine.store('participantes').confirmEspecial({{ $premio->id }}, numero)"
            class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-xs"
        >Confirmar Premio</button>
    </div>
</div>
