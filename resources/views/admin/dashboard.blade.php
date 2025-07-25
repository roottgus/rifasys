@extends('layouts.admin')
@section('title', \App\Models\Setting::get('dashboard_title', 'Dashboard Administrativo'))

@section('content')
@php
    $primaryColor = \App\Models\Setting::get('empresa_color', '#ff7f00');
    $dashboardTitle = \App\Models\Setting::get('dashboard_title', 'Dashboard Administrativo');
@endphp

{{-- Modal de bienvenida --}}
<div x-data="{ open: {{ $showWelcomeModal ? 'true' : 'false' }} }" x-show="open" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
     x-cloak
     >
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-8 relative">
        <button @click="open = false" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 focus:outline-none" aria-label="Cerrar modal">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <h2 class="text-2xl font-bold mb-4 text-center" style="color: {{ $primaryColor }}">Bienvenido a Rifasys</h2>
        <p class="text-center text-gray-700 mb-6">
            Gracias por confiar en nuestro sistema profesional de gestión de rifas, tickets y abonos.<br>
            Explora las funcionalidades y no dudes en contactarnos para soporte.
        </p>
        <div class="flex justify-center">
            <button @click="open = false" 
        style="background-color: {{ $primaryColor }};" 
        class="px-6 py-2 text-white rounded hover:opacity-90 focus:outline-none transition">
    Comenzar
</button>

        </div>
    </div>
</div>

<div class="p-4 md:p-8 space-y-10 bg-gradient-to-br from-white via-gray-50 to-gray-100 rounded-3xl">

    {{-- Título principal --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-brand drop-shadow-sm" style="color: {{ $primaryColor }}">
            <i class="fas fa-chart-pie mr-3"></i>
            {{ $dashboardTitle }}
        </h1>
        <div class="flex items-center gap-2">
            <span class="inline-block bg-brand/10 text-brand font-bold px-3 py-1 rounded-lg text-xs" style="color: {{ $primaryColor }}; background: {{ $primaryColor }}11;">{{ date('d M Y') }}</span>
        </div>
    </div>

    {{-- Agrega separación superior y mayor separación inferior a los KPIs --}}
    <div class="mt-4">
        @include('admin.dashboard.partials._kpis')
    </div>

    {{-- SEPARADOR FIJO ENTRE SECCIONES --}}
    <div class="h-4 md:h-2 flex items-center justify-center">
        <div class="w-full h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
    </div>

    <div>
        @include('admin.dashboard.partials._quick-actions')
    </div>

    {{-- ... demás partials ... --}}
    @include('admin.dashboard.partials._next-draw')
    <div class="mt-10">
        @include('admin.dashboard.partials._charts')
    </div>

    @include('admin.dashboard.partials._ultimos-tickets')
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Ventas Diarias
  const ventasCtx = document.getElementById('ventasDiariasChart').getContext('2d');
  new Chart(ventasCtx, {
    type: 'line',
    data: {
      labels: {!! json_encode($ventasFechas) !!},
      datasets: [{
        label: 'Ventas',
        data: {!! json_encode($ventasDatos) !!},
        fill: true,
        tension: 0.3
      }]
    },
    options: {
      responsive: true,
      scales: { y: { beginAtZero: true } }
    }
  });

  // Abonos por Método
  const abonosCtx = document.getElementById('abonosMetodoChart').getContext('2d');
  new Chart(abonosCtx, {
    type: 'bar',
    data: {
      labels: {!! json_encode($metodosAbono) !!},
      datasets: [{
        label: 'Abonos',
        data: {!! json_encode($abonosDatos) !!},
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      scales: { y: { beginAtZero: true } }
    }
  });
</script>
@endpush
