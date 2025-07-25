@extends('layouts.admin')

@section('title', 'Configuración de Empresa')

@section('content')
<div class="max-w-xl mx-auto bg-white shadow-lg rounded-2xl p-8">
    <h2 class="text-2xl font-bold mb-6 text-primary flex items-center gap-2">
        <i class="fas fa-building"></i>
        Configuración de la Empresa
    </h2>

    @if(session('success'))
        <div class="mb-4 text-green-700 bg-green-100 p-3 rounded">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.configuracion.empresa.update') }}" method="POST" enctype="multipart/form-data" class="space-y-7">
        @csrf

        {{-- Nombre de la empresa --}}
        <div>
            <label class="block text-sm font-semibold mb-1">
                <i class="fas fa-font mr-1 text-primary"></i>
                Nombre de la empresa
            </label>
            <input type="text" name="empresa_nombre"
                   value="{{ old('empresa_nombre', $settings['empresa_nombre']) }}"
                   required
                   class="w-full border rounded p-2 focus:ring-2 focus:ring-primary/30 focus:border-primary"
                   placeholder="Ejemplo: RifaGana C.A.">
            @error('empresa_nombre') <div class="text-red-600 text-xs">{{ $message }}</div> @enderror
            <span class="text-xs text-gray-500 ml-1">
                Este nombre aparecerá en la parte superior del sistema y en los reportes.
            </span>
        </div>
        {{-- Título del Dashboard --}}
<div>
    <label class="block text-sm font-semibold mb-1"><i class="fas fa-font mr-1 text-primary"></i>Título del Panel Administrativo</label>
    <input type="text" name="dashboard_title"
        value="{{ old('dashboard_title', $settings['dashboard_title'] ?? 'Dashboard Administrativo') }}"
        class="w-full border rounded p-2 focus:ring-2 focus:ring-primary/30 focus:border-primary">
    @error('dashboard_title') <div class="text-red-600 text-xs">{{ $message }}</div> @enderror
    <span class="ml-2 text-xs text-gray-500 block mt-2">
        El texto que aparecerá como título principal en el panel administrativo.
    </span>
</div>
        {{-- Logo de la empresa --}}
<div>
    <label class="block text-sm font-semibold mb-1">
        <i class="fas fa-image mr-1 text-primary"></i>
        Logo de la empresa
    </label>
    <input type="file" name="empresa_logo" accept="image/*" class="w-full"
        onchange="previewLogo(event)">
    @error('empresa_logo') <div class="text-red-600 text-xs">{{ $message }}</div> @enderror
    <div class="mt-2 flex gap-4 items-center">
        {{-- Logo actual --}}
        @php
            $logo = $settings['empresa_logo'] ?? null;
            if ($logo && !str_starts_with($logo, 'logos/')) {
                $logo = 'logos/' . $logo;
            }
        @endphp
        @if ($settings['empresa_logo'])
            <img id="currentLogo" src="{{ asset('storage/'.$logo) }}"
                class="h-16 rounded shadow border" alt="Logo actual">
        @endif
        {{-- Vista previa --}}
        <img id="previewLogo" src="#" class="h-16 rounded shadow border hidden" alt="Vista previa logo"/>
    </div>
    @if ($settings['empresa_logo'])
        <button type="submit" name="eliminar_logo" value="1"
            class="text-red-600 hover:text-red-800 text-xs ml-2 font-bold bg-transparent border-none mt-1 transition">
            Eliminar logo
        </button>
    @endif
    <span class="text-xs text-gray-500 ml-1">
        Tu logo se mostrará en la barra lateral y en los documentos exportados (PDF, reportes).
    </span>
</div>


        {{-- Favicon de la empresa --}}
        <div>
            <label class="block text-sm font-semibold mb-1">
                <i class="fas fa-star mr-1 text-primary"></i>
                Icono de acceso rápido (Favicon)
            </label>
            <input type="file" name="empresa_favicon" accept="image/x-icon,image/png" class="w-full"
                onchange="previewFavicon(event)">
            @error('empresa_favicon') <div class="text-red-600 text-xs">{{ $message }}</div> @enderror
            <div class="mt-2 flex gap-4 items-center">
                @if (isset($settings['empresa_favicon']) && $settings['empresa_favicon'])
                    <img id="currentFavicon" src="{{ asset('storage/logos/'.$settings['empresa_favicon']) }}"
                        class="h-8 w-8 rounded shadow border" alt="Favicon actual">
                @endif
                <img id="previewFavicon" src="#" class="h-8 w-8 rounded shadow border hidden" alt="Vista previa favicon"/>
            </div>
            @if (isset($settings['empresa_favicon']) && $settings['empresa_favicon'])
                <button type="submit" name="eliminar_favicon" value="1"
                    class="text-red-600 hover:text-red-800 text-xs ml-2 font-bold bg-transparent border-none mt-1 transition">
                    Eliminar favicon
                </button>
            @endif
            <span class="text-xs text-gray-500 ml-1">
                Este pequeño ícono aparecerá en la pestaña del navegador y al guardar accesos directos.
            </span>
        </div>

        {{-- Color primario --}}
        <div>
            <label class="block text-sm font-semibold mb-1">
                <i class="fas fa-palette mr-1 text-primary"></i>
                Color principal de tu empresa
            </label>
            <input type="color" name="empresa_color"
                value="{{ old('empresa_color', $settings['empresa_color'] ?? '#0d47a1') }}"
                class="w-16 h-8 p-0 border-0 rounded shadow cursor-pointer align-middle">
            @error('empresa_color') <div class="text-red-600 text-xs">{{ $message }}</div> @enderror
            <span class="ml-2 text-xs text-gray-500 block mt-2">
                <strong>¿Qué cambia si modificas este color?</strong> <br>
                El color seleccionado será utilizado automáticamente en:
                <ul class="list-disc pl-5">
                    <li>Botones principales (ejemplo: Guardar, Siguiente)</li>
                    <li>Encabezados y títulos destacados</li>
                    <li>Elementos visuales como fondos y bordes importantes</li>
                </ul>
                <span class="block mt-1">
                    <i class="fas fa-info-circle text-primary"></i>
                    Así, la plataforma siempre lucirá con la identidad visual de tu empresa.<br>
                    Puedes cambiar este color en cualquier momento y los cambios se aplicarán de inmediato en todo el sistema.
                </span>
            </span>
        </div>

        <div class="pt-2">
            <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-6 py-2 rounded font-bold shadow transition flex items-center gap-2">
                <i class="fas fa-save"></i>
                Guardar cambios
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function previewLogo(event) {
    const preview = document.getElementById('previewLogo');
    const current = document.getElementById('currentLogo');
    if(event.target.files.length){
        const file = event.target.files[0];
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        if(current) current.classList.add('hidden');
    } else {
        preview.src = '#';
        preview.classList.add('hidden');
        if(current) current.classList.remove('hidden');
    }
}

function previewFavicon(event) {
    const preview = document.getElementById('previewFavicon');
    const current = document.getElementById('currentFavicon');
    if(event.target.files.length){
        const file = event.target.files[0];
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        if(current) current.classList.add('hidden');
    } else {
        preview.src = '#';
        preview.classList.add('hidden');
        if(current) current.classList.remove('hidden');
    }
}
</script>
@endpush
