@extends('layouts.admin')

@section('title', 'Gestión de Loterías')

@section('content')
<div x-data="gestionLoterias()" class="p-6 space-y-6">
  @include('admin.loterias.partials._banner')

  {{-- Aquí quitamos el template de "toast" Alpine --}}

  {{-- Tarjetas superiores con counters --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-primary/10 border-l-4 border-primary rounded-2xl p-4 flex items-center gap-4 shadow-sm">
      <i class="fas fa-dice fa-2x text-primary"></i>
      <div>
        <div class="font-bold text-lg text-primary">Loterías registradas</div>
        <div class="text-2xl font-extrabold text-gray-700" x-text="loteriasArray.length"></div>
      </div>
    </div>
    <div class="bg-emerald-100 border-l-4 border-emerald-400 rounded-2xl p-4 flex items-center gap-4 shadow-sm">
      <i class="fas fa-layer-group fa-2x text-emerald-600"></i>
      <div>
        <div class="font-bold text-lg text-emerald-600">Tipos de Lotería</div>
        <div class="text-2xl font-extrabold text-gray-700" x-text="tiposLoteria.length"></div>
      </div>
    </div>
  </div>

  {{-- Buscador de tipos de lotería --}}
  <div class="flex justify-end mt-2">
    <input type="text" x-model="searchTipo" placeholder="Buscar tipo de lotería..." 
      class="px-3 py-2 border rounded-xl w-80 focus:ring-primary focus:border-primary text-sm transition" />
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    {{-- Loterías --}}
    <div class="bg-white rounded-2xl shadow-lg p-4 border border-primary/10">
      <div class="flex items-center justify-between mb-3">
        <div class="font-bold text-orange-600 text-lg flex items-center gap-2">
          <i class="fas fa-dice"></i> Loterías Creadas
        </div>
        <button @click="openLoteriaModal = true" class="bg-primary hover:bg-primary/80 text-white px-3 py-1.5 rounded shadow flex items-center gap-1 text-sm transition">
          <i class="fas fa-plus-circle"></i> Nueva
        </button>
      </div>
      <template x-if="!loteriasArray.length">
        <div class="text-center text-gray-400 py-8">No hay loterías registradas.</div>
      </template>
      <template x-for="l in loteriasArray" :key="l.id">
        <div class="flex items-center justify-between px-4 py-2 rounded-xl border border-orange-100 bg-orange-50/80 mb-2 hover:shadow transition">
          <div class="flex items-center gap-2 font-semibold text-orange-700">
            <i class="fas fa-dice"></i> <span x-text="l.nombre"></span>
          </div>
          <div class="flex items-center gap-1">
            <button @click="openDeleteLoteriaModal(l)" class="text-red-500 hover:text-red-700 p-1" title="Eliminar"><i class="fas fa-trash"></i></button>
          </div>
        </div>
      </template>
    </div>

    {{-- Tipos de Lotería --}}
    <div class="bg-white rounded-2xl shadow-lg p-4 border border-emerald-100">
      <div class="flex items-center justify-between mb-3">
        <div class="font-bold text-emerald-700 text-lg flex items-center gap-2">
          <i class="fas fa-layer-group"></i> Tipos de Lotería Creados
        </div>
        <button @click="openTipoModal = true" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded shadow flex items-center gap-1 text-sm transition">
          <i class="fas fa-plus-circle"></i> Nuevo tipo
        </button>
      </div>
      <template x-if="!filteredTipos.length">
        <div class="text-center text-gray-400 py-8">No hay tipos registrados o no hay coincidencias.</div>
      </template>
      <template x-for="t in filteredTipos" :key="t.id">
        <div class="flex items-center justify-between px-4 py-2 rounded-xl border border-emerald-100 bg-emerald-50/70 mb-2 hover:shadow transition">
          <div>
            <span class="font-bold text-emerald-700" x-text="t.nombre"></span>
            <small class="ml-2 text-gray-500"><i class="fas fa-dice"></i> <span x-text="getLoteriaNombre(t.loteria_id)"></span></small>
          </div>
          <div class="flex items-center gap-1">
            <button @click="openDeleteTipoLoteriaModal(t)" class="text-red-500 hover:text-red-700 p-1" title="Eliminar"><i class="fas fa-trash"></i></button>
          </div>
        </div>
      </template>
    </div>
  </div>

  @include('admin.loterias.partials._modal-loteria')
  @include('admin.loterias.partials._modal-tipo-loteria')
  @include('admin.loterias.partials._modal-delete-loteria')
  @include('admin.loterias.partials._modal-delete-tipo')
</div>
@endsection

@push('styles')
<style>
  [x-cloak] { display: none !important; }
  .animate-bounce { animation: bounce 0.7s; }
  @keyframes bounce {
    0%   { transform: translateY(-30px); opacity: 0.7;}
    60%  { transform: translateY(6px);}
    90%  { transform: translateY(-2px);}
    100% { transform: translateY(0); opacity: 1;}
  }
</style>
@endpush

@push('scripts')
<script>
  window.loteriasData = @json($loterias);
  window.tiposLoteriaData = @json($tiposLoteria->values()->toArray());
</script>
@vite('resources/js/app.js')

@vite('resources/js/app.js')
@endpush
