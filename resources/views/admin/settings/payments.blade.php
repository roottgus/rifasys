@extends('layouts.admin')

@section('title', 'Configuración de Métodos de Pago')

@section('content')
<div class="max-w-5xl mx-auto py-10" x-data="paymentSettings()">
    {{-- Toast --}}
    @include('admin.settings.partials._payments-toast')

    {{-- Descripción profesional --}}
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-indigo-900 flex items-center justify-center gap-3">
            <i class="fas fa-credit-card text-indigo-600"></i>
            Configuración de Métodos de Pago
        </h1>
        <p class="mt-3 text-gray-500 max-w-2xl mx-auto text-lg">
            Administra y personaliza tus métodos de pago. Puedes tener múltiples variantes (por ejemplo, varias cuentas internacionales o Zelle).<br>
            ¡Hazlo simple, seguro y profesional!
        </p>
    </div>

    <form method="POST" action="{{ route('admin.settings.payments.save') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Panel izquierdo: lista de métodos --}}
            <div class="col-span-1 bg-gray-50 p-4 rounded-xl shadow min-h-[420px] relative">
                <h2 class="text-lg font-semibold mb-4 text-indigo-800 flex items-center gap-2">
                    <i class="fas fa-list-ul text-indigo-500"></i>
                    Métodos Disponibles
                </h2>
                <button type="button"
                        @click="selectedIdx = null; showAll = true"
                        class="flex items-center w-full px-3 py-3 mb-2 rounded-lg border border-dashed border-indigo-400 bg-indigo-50 hover:bg-indigo-100 transition gap-3 font-semibold text-indigo-800"
                        :class="showAll ? 'shadow-lg ring-2 ring-indigo-400' : ''">
                    <i class="fas fa-layer-group text-indigo-600"></i>
                    Ver todos los métodos activos
                </button>
                {{-- Listar todos los métodos por variante --}}
                <template x-for="(method, idx) in methods" :key="method.id || method._key">
                    <div class="mb-2">
                        <button type="button"
                            @click="selectMethod(idx); showAll = false"
                            class="flex items-center w-full px-3 py-3 rounded-lg transition border border-transparent
                            text-left gap-3 hover:bg-indigo-50"
                            :class="selectedIdx === idx && !showAll
                                    ? 'bg-indigo-100 border-indigo-400 shadow text-indigo-900'
                                    : 'bg-white text-gray-700'">
                            <span class="flex items-center justify-center text-2xl">
    <template x-if="method.key === 'zelle'">
        <img :src="method.icon" class="h-7 w-7 object-contain inline-block" alt="Zelle" />
    </template>
    <template x-if="method.key !== 'zelle'">
        <span x-html="method.icon"></span>
    </template>
</span>
                            <span>
                                <span class="font-semibold" x-text="method.name"></span>
                                <span x-text="method.alias ? ` (${method.alias})` : ''" class="text-xs ml-1 text-indigo-500"></span>
                                <span x-show="!method.enabled" class="text-xs ml-2 px-2 py-1 rounded bg-gray-300 text-gray-700">Inactivo</span>
                            </span>
                            <span class="ml-auto flex items-center gap-2">
                                <input type="hidden" :name="'methods['+idx+'][enabled]'" value="0">
                                <input 
                                    type="checkbox" 
                                    :name="'methods['+idx+'][enabled]'"
                                    class="form-checkbox h-5 w-5 text-indigo-600"
                                    x-model="method.enabled"
                                    :value="1"
                                    @change="toggleEnabled(idx)"
                                >
                            </span>
                        </button>
                    </div>
                </template>

                {{-- Botón flotante para abrir el modal --}}
                <div class="mt-6 flex justify-center">
                    <button
                        type="button"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow font-bold flex items-center gap-2 transition"
                        @click="openAddModal = true"
                    >
                        <i class="fas fa-plus-circle"></i>
                        Agregar nuevo método de pago
                    </button>
                </div>
                @include('admin.settings.partials._payments-methods-add-modal')
            </div>

            {{-- Panel derecho: detalles o resumen --}}
            <div class="col-span-2 bg-white p-8 rounded-xl shadow flex flex-col">
                {{-- Mostrar todas las tarjetas si showAll --}}
                <template x-if="showAll">
                    <div>
                        <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center gap-2">
                            <i class="fas fa-wallet text-indigo-600"></i>
                            Métodos de Pago Activos
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <template x-if="methods.filter(m => m.enabled).length === 0">
                                <div class="col-span-3 text-center text-gray-400 py-8">
                                    <i class="fas fa-credit-card text-3xl mb-2"></i>
                                    <div>No hay métodos activos actualmente.</div>
                                </div>
                            </template>
                            <template x-for="(method, idx) in methods.filter(m => m.enabled)" :key="method.id || method._key">
                                @include('admin.settings.partials._payments-method-card')
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Mostrar detalle de método individual --}}
                <template x-if="selectedMethod && !showAll">
                    @include('admin.settings.partials._payments-method-edit')
                </template>

                {{-- Mensaje de bienvenida --}}
                <template x-if="!selectedMethod && !showAll">
                    @include('admin.settings.partials._payments-welcome')
                </template>
            </div>
        </div>
    </form>

    {{-- Modal confirmación para eliminar variante --}}
    <div 
      x-show="showDeleteModal"
      x-transition.opacity
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40"
      style="display: none;"
    >
      <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-8 relative animate-fade-in">
        <div class="text-center mb-4">
          <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-3"></i>
          <h2 class="font-bold text-xl text-red-600 mb-2">Eliminar Variante</h2>
          <p class="text-gray-700 text-base">¿Seguro que deseas eliminar esta variante de método de pago?</p>
        </div>
        <div class="flex justify-center gap-4 mt-6">
          <button 
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-800 font-semibold"
            @click="cancelDeleteVariant"
            type="button"
          >Cancelar</button>
          <button 
            class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-white font-bold"
            @click="confirmDeleteVariant"
            type="button"
          >Sí, Eliminar</button>
        </div>
      </div>
    </div>
</div>

{{-- Script Alpine separado para mantener orden --}}
@include('admin.settings.partials._payments-script')

@endsection
