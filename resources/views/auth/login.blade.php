<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | {{ config('app.name') }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    body { font-family: 'Inter', sans-serif; }
    .split-bg {
      background: linear-gradient(135deg, #003388 0%, #0367fa 100%);
      min-height: 100%;
      width: 100%;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-b from-slate-900 to-slate-800">
  <div class="flex w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden mx-4 my-8">

    <!-- LADO IZQUIERDO SIEMPRE VISIBLE EN ESCRITORIO -->
    <div class="flex flex-col justify-center items-center w-1/2 split-bg p-10">
  <img src="{{ asset('storage/' . (isset($settings['empresa_logo']) ? $settings['empresa_logo'] : 'logos/logosys.png')) }}"
       class="w-30 h-24 mb-6 drop-shadow-2xl mx-auto"
       alt="Logo">
  <h2 class="text-white text-3xl font-extrabold mb-2 text-center drop-shadow">¡Bienvenido!</h2>
  <p class="text-red-300 text-base text-center mb-8 drop-shadow">
    Gestiona rifas, tickets y abonos<br>de forma sencilla y profesional.
  </p>
  
</div>


    <!-- LADO DERECHO: Formulario de login -->
    <div class="flex flex-col justify-center items-center w-1/2 px-8 py-10">
      <form method="POST" action="{{ route('login') }}" class="w-full max-w-sm space-y-5" id="loginForm" autocomplete="on">
          @csrf
          <input
              type="email"
              name="email"
              required
              autofocus
              class="w-full border rounded-lg p-3 text-base focus:ring-2 focus:ring-blue-400"
              placeholder="Correo electrónico"
              autocomplete="username"
          >
          <input
              type="password"
              name="password"
              required
              class="w-full border rounded-lg p-3 text-base focus:ring-2 focus:ring-blue-400"
              placeholder="Contraseña"
              autocomplete="current-password"
          >
          <div class="flex justify-between items-center text-xs text-gray-500">
              <label>
                  <input type="checkbox" name="remember" class="accent-blue-600"> Recordarme
              </label>
              <a href="{{ route('password.request') }}" class="text-blue-700 hover:underline">¿Olvidaste tu contraseña?</a>
          </div>
          <button type="submit" class="w-full py-3 bg-gradient-to-r from-blue-700 to-blue-500 text-white font-semibold rounded-xl shadow hover:from-blue-800 hover:to-blue-600 transition">
              Entrar
          </button>
      </form>

      <div class="mt-7 text-center text-sm text-gray-400">
        ¿No tienes cuenta?
        <a href="#" class="text-blue-700 font-semibold hover:underline">Contacta a tu administrador</a>
      </div>
      <div class="mt-6 text-center text-xs text-gray-400">
        © {{ date('Y') }} Rifasys<br>
        Desarrollado por <a href="https://publicidadenred.com" target="_blank" class="text-blue-700 underline font-semibold">Publicidad en Red</a>
      </div>
    </div>
  </div>
</body>
</html>
