{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <style>
    [x-cloak] { display: none !important; }
    </style>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ \App\Models\Setting::get('empresa_nombre', config('app.name')) }}</title>

    @vite('resources/css/app.css')

    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    {{-- Favicon dinámico --}}
    @php
        $favicon = \App\Models\Setting::get('empresa_favicon');
        $primaryColor = \App\Models\Setting::get('empresa_color', '#2563eb'); // INDIGO-600 por defecto
        $secondaryColor = '#64748b'; // slate-500 de Tailwind, como secundario
        $accentColor = '#38bdf8'; // sky-400 de Tailwind, para acentos
    @endphp
    @if($favicon)
        <link rel="icon" type="image/png" href="{{ asset('storage/logos/'.$favicon) }}">
    @endif

    {{-- Branding dinámico --}}
    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --secondary-color: {{ $secondaryColor }};
            --accent-color: {{ $accentColor }};
        }
        .bg-primary     { background: var(--primary-color) !important; }
        .text-primary   { color: var(--primary-color) !important; }
        .border-primary { border-color: var(--primary-color) !important; }
        .bg-secondary     { background: var(--secondary-color) !important; }
        .text-secondary   { color: var(--secondary-color) !important; }
        .border-secondary { border-color: var(--secondary-color) !important; }
        .bg-accent     { background: var(--accent-color) !important; }
        .text-accent   { color: var(--accent-color) !important; }
    </style>
</head>

<body x-data class="min-h-screen flex bg-gray-100">

  {{-- Sidebar Pro --}}
  <aside class="w-64 bg-gradient-to-b from-white via-gray-50 to-gray-100 border-r border-gray-200 flex flex-col shadow-xl z-10 relative">
      {{-- Logo/Header --}}
      <div class="h-20 flex items-center justify-center bg-white shadow-md relative">
          @php
    $logo = \App\Models\Setting::get('empresa_logo');
    $empresa = \App\Models\Setting::get('empresa_nombre', config('app.name'));
    if ($logo && !str_starts_with($logo, 'logos/')) {
        $logo = 'logos/' . $logo;
    }
@endphp

@if ($logo)
    <a href="{{ route('admin.dashboard') }}">
        <img src="{{ asset('storage/' . $logo) }}"
             alt="Logo Empresa"
             class="h-14 max-w-[120px] object-contain drop-shadow-md transition hover:scale-105" />
    </a>
@else
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 text-2xl font-bold text-primary tracking-wider hover:opacity-90 transition">
        <span>
            <i class="fa-solid fa-crown text-yellow-400 drop-shadow-md"></i>
        </span>
        {{ $empresa }}
    </a>
@endif



          <span class="absolute top-2 right-4 bg-primary/10 text-primary text-xs px-2 py-1 rounded-full font-semibold shadow-sm">Admin</span>
      </div>

      <nav class="flex-1 overflow-y-auto py-6">
         <ul class="space-y-2 px-3">
    <li>
        <a href="{{ route('admin.dashboard') }}"
           class="group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all
           {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white shadow-lg' : 'text-gray-700 hover:bg-primary/10 hover:text-primary' }}">
            <i class="fas fa-home text-lg group-hover:scale-110 transition"></i>
            Dashboard
        </a>
    </li>
    <li>
        <a href="{{ route('admin.tickets.sale') }}"
           class="group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all
           {{ request()->routeIs('admin.tickets.sale') ? 'bg-primary text-white shadow-lg' : 'text-gray-700 hover:bg-primary/10 hover:text-primary' }}">
            <i class="fas fa-cash-register text-lg group-hover:scale-110 transition"></i>
            Venta de Tickets
        </a>
    </li>
    <li>
    <a href="{{ route('admin.descuentos.index') }}"
       class="group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all
       {{ request()->is('admin/descuentos*') ? 'bg-primary text-white shadow-lg' : 'text-gray-700 hover:bg-primary/10 hover:text-primary' }}">
        <i class="fas fa-percentage text-lg group-hover:scale-110 transition"></i>
        Descuentos
    </a>
</li>

    <li>
        <a href="{{ route('admin.rifas.index') }}"
           class="group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all
           {{ request()->is('admin/rifas*') ? 'bg-primary text-white shadow-lg' : 'text-gray-700 hover:bg-primary/10 hover:text-primary' }}">
            <i class="fas fa-ticket-alt text-lg group-hover:scale-110 transition"></i>
            Gestión de Rifas
        </a>
    </li>
    <li>
        <a href="{{ route('admin.tickets.index') }}"
           class="group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all
           {{ request()->is('admin/tickets*') ? 'bg-primary text-white shadow-lg' : 'text-gray-700 hover:bg-primary/10 hover:text-primary' }}">
            <i class="fas fa-shopping-cart text-lg group-hover:scale-110 transition"></i>
            Gestión de Tickets
        </a>
    </li>
    <li>
        <a href="{{ route('admin.clientes.index') }}"
           class="group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all
           {{ request()->is('admin/clientes*') ? 'bg-primary text-white shadow-lg' : 'text-gray-700 hover:bg-primary/10 hover:text-primary' }}">
            <i class="fas fa-users text-lg group-hover:scale-110 transition"></i>
            Gestión de Clientes
        </a>
    </li>
    <li>
        <a href="{{ route('admin.loterias.gestion') }}"
           class="group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all
           {{ request()->is('admin/loterias*') ? 'bg-primary text-white shadow-lg' : 'text-gray-700 hover:bg-primary/10 hover:text-primary' }}">
            <i class="fas fa-th-large text-lg group-hover:scale-110 transition"></i>
            Gestión de Loterías
        </a>
    </li>
    <li>
        <a href="{{ route('admin.configuracion.empresa') }}"
           class="group flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition-all
           {{ request()->is('admin/configuracion/empresa*') ? 'bg-primary text-white shadow-lg' : 'text-gray-700 hover:bg-primary/10 hover:text-primary' }}">
            <i class="fas fa-building text-lg group-hover:scale-110 transition"></i>
            Mi Empresa
        </a>
    </li>
</ul>

      </nav>

      <div class="px-6 pb-6 mt-auto">
          <div class="text-[11px] text-gray-400 text-center">
              &copy; {{ date('Y') }} {{ $empresa }}<br>
              Desarrollado por <span class="font-bold text-primary">Publicidad En Red</span>
          </div>
      </div>
  </aside>

  {{-- Main content --}}
  <div class="flex-1 flex flex-col">
    <header class="h-16 bg-white border-b border-gray-200 flex items-center px-6 relative">
    
    <div class="ml-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-gray-600 hover:text-gray-800">Cerrar sesión</button>
        </form>
    </div>
</header>

    <main class="flex-1 overflow-y-auto px-6 py-4">
      @yield('content')
    </main>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script>
      window.Rutas = {
        obtenerDescuento: "{{ route('admin.descuentos.obtener-descuento') }}"
      };
    </script>
    @vite('resources/js/app.js')

<script src="{{ asset('impresora/qz-tray/qz-tray.js') }}"></script>



    @yield('scripts')   
    @stack('scripts')


  </div>
</body>
</html>
