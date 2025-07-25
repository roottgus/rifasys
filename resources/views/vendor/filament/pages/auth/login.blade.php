{{-- resources/views/vendor/filament/auth/login.blade.php --}}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Ingreso | Panel de Rifas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-tr from-indigo-900 to-blue-700">

    <div class="bg-white rounded-2xl shadow-xl flex overflow-hidden max-w-4xl w-full">
        {{-- Lado izq: imagen o logo grande --}}
        <div class="hidden lg:flex lg:w-1/2 bg-cover bg-center" style="background-image: url('{{ asset('images/splash-bg.jpg') }}')">
        </div>

        {{-- Lado der: formulario --}}
        <div class="w-full lg:w-1/2 p-8">
            <div class="mb-6 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="mx-auto w-24 h-24 mb-2">
                <h1 class="text-2xl font-bold">Panel Administrativo Rifas</h1>
                <p class="text-gray-500">Accede con tus credenciales</p>
            </div>

            <form method="POST" action="{{ route('filament.auth.authenticate') }}" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contraseña --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Recordarme --}}
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">Recordarme</label>
                </div>

                {{-- Botón --}}
                <div>
                    <button
                        type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        Entrar
                    </button>
                </div>
            </form>

            <p class="mt-6 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} Sistema de Rifas. Todos los derechos reservados.
            </p>
        </div>
    </div>

</body>
</html>
