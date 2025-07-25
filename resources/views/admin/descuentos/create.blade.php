@extends('layouts.admin')

@section('title', 'Nueva Regla de Descuento')

@section('content')
<h1 class="text-2xl font-extrabold mb-6">Nueva Regla de Descuento</h1>

{{-- Aviso Explicativo --}}
<div class="mb-7 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg shadow-sm text-gray-800 flex items-start gap-3">
    <span class="mt-0.5 text-yellow-500">
        <i class="fas fa-info-circle fa-lg"></i>
    </span>
    <div>
        <strong class="block text-yellow-800 mb-1">¿Cómo funciona?</strong>
        <span class="text-sm">
            Define aquí reglas automáticas de descuento para las ventas de tickets de cada rifa.
            Por ejemplo: <b>si compras 5 tickets o más</b> aplica un descuento especial.
        </span>
    </div>
</div>

{{-- Formulario --}}
<div class="max-w-md bg-white shadow-lg rounded-2xl mx-auto p-8 border border-gray-100">
    <form action="{{ route('admin.descuentos.store') }}" method="POST" autocomplete="off">
        @csrf

        {{-- Rifa --}}
        <div class="mb-6">
            <label for="rifa_id" class="block text-sm font-semibold text-gray-700 mb-1">
                <i class="fas fa-ticket-alt text-primary mr-1"></i>
                Rifa <span class="text-red-500">*</span>
            </label>
            <select id="rifa_id" name="rifa_id" required
                class="w-full border-2 border-primary/20 rounded-lg px-4 py-2 focus:border-primary focus:ring focus:ring-primary/20 transition">
                <option value="" disabled selected>Seleccione una rifa</option>
                @foreach($rifas as $rifa)
                    <option value="{{ $rifa->id }}">{{ $rifa->nombre }}</option>
                @endforeach
            </select>
            @error('rifa_id')
                <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Cantidad mínima --}}
        <div class="mb-6">
            <label for="cantidad_minima" class="block text-sm font-semibold text-gray-700 mb-1">
                <i class="fas fa-layer-group text-primary mr-1"></i>
                Cantidad mínima de tickets <span class="text-red-500">*</span>
                <span class="ml-1 text-gray-400" title="Desde cuántos tickets comprados se activa el descuento">
                    <i class="fas fa-question-circle"></i>
                </span>
            </label>
            <input type="number" min="1" step="1" id="cantidad_minima" name="cantidad_minima" required
                class="w-full border-2 border-primary/20 rounded-lg px-4 py-2 focus:border-primary focus:ring focus:ring-primary/20 transition"
                placeholder="Ej: 5">
            @error('min_cantidad')
                <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Porcentaje Descuento --}}
        <div class="mb-8">
            <label for="porcentaje" class="block text-sm font-semibold text-gray-700 mb-1">
                <i class="fas fa-percent text-primary mr-1"></i>
                % de Descuento <span class="text-red-500">*</span>
                <span class="ml-1 text-gray-400" title="Porcentaje de descuento a aplicar (ejemplo: 20)">
                    <i class="fas fa-question-circle"></i>
                </span>
            </label>
            <input type="number" min="1" max="100" step="1" id="porcentaje" name="porcentaje" required
                class="w-full border-2 border-primary/20 rounded-lg px-4 py-2 focus:border-primary focus:ring focus:ring-primary/20 transition"
                placeholder="Ej: 20">
            @error('porcentaje')
                <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Botones --}}
        <div class="flex gap-3">
            <button type="submit"
                class="flex-1 px-5 py-2 bg-primary text-white rounded-lg font-bold shadow hover:bg-primary/90 transition flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Guardar
            </button>
            <a href="{{ route('admin.descuentos.index') }}"
                class="flex-1 px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-bold shadow transition flex items-center justify-center gap-2">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
