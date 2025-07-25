<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Bienvenido | Sistema de Rifas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  @php
    $primaryColor = \App\Models\Setting::get('empresa_color', '#ff7f00');
    $logo = \App\Models\Setting::get('empresa_logo');
    $empresa = \App\Models\Setting::get('empresa_nombre', config('app.name'));
    if ($logo && !str_starts_with($logo, 'logos/')) {
        $logo = 'logos/' . $logo;
    }
@endphp

  <style>
    :root {
      --primary-color: {{ $primaryColor }};
    }
    .bg-brand {
      background: linear-gradient(135deg, var(--primary-color) 60%, #111827 100%);
    }
    .text-brand {
      color: var(--primary-color) !important;
    }
    .border-brand {
      border-color: var(--primary-color) !important;
    }
    .shadow-brand {
      box-shadow: 0 8px 40px 0 var(--primary-color, #ff7f00);
    }
  </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 min-h-screen flex items-center justify-center px-4">

  <div class="w-full max-w-md mx-auto bg-white/90 rounded-3xl shadow-brand py-12 px-8 flex flex-col items-center">
      <div class="mb-6 bg-white rounded-full shadow-lg p-4 flex items-center justify-center">
    @if($logo)
        <img src="{{ asset('storage/' . $logo) }}" alt="Logo Empresa" class="w-24 h-24 object-contain rounded-full" />
    @else
        <img src="{{ asset('images/logoejemplo.png') }}" alt="Logo Ejemplo" class="w-24 h-24 object-contain rounded-full" />
    @endif
</div>

      <h1 class="text-3xl sm:text-4xl font-extrabold mb-2 text-center text-brand drop-shadow" style="letter-spacing: 1px;">
        Bienvenido al Panel
      </h1>
      <div class="text-lg mb-6 text-gray-600 text-center">
        Gestiona rifas, tickets y abonos de forma sencilla y profesional.
      </div>
      <a href="{{ route('login') }}"
         class="inline-flex items-center gap-2 bg-brand hover:scale-105 hover:brightness-105 transition transform font-semibold text-white py-3 px-8 rounded-2xl shadow-lg text-lg"
         style="background: var(--primary-color);">
         <i class="fas fa-door-open text-white text-xl"></i>
         Entrar al Panel
      </a>
      <div class="mt-8 text-xs text-gray-400 text-center w-full">
        &copy; {{ date('Y') }} {{ $empresa }}<br>
        Desarrollado por <span class="font-bold text-brand">Publicidad En Red</span>
      </div>
  </div>

</body>
</html>
