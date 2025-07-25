@php
    $logoRifasys = \App\Models\Setting::get('empresa_logo');
    if ($logoRifasys && !str_starts_with($logoRifasys, 'logos/')) {
        $logoRifasys = 'logos/' . $logoRifasys;
    }
    // Logo de Publicidad En Red subido manualmente (ruta absoluta al archivo subido)
    $logoAgencia = asset('storage/externo/logoppal.png');
@endphp

<footer
    class="pt-8 pb-5 shadow-inner"
    style="background: linear-gradient(to top, #2563eb 0%, #22223b 100%);"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">

            {{-- Izquierda: Solo logo de Rifasys --}}
            <div class="flex items-center justify-center md:justify-start w-full md:w-auto">
                @if($logoRifasys)
                    <img src="{{ asset('storage/' . $logoRifasys) }}"
                        alt="Logo Rifasys"
                        class="h-9 w-auto object-contain" />
                @endif
            </div>

            {{-- Centro: Derechos reservados --}}
            <div class="text-center text-xs md:text-sm text-gray-200/80 flex-1">
                &copy; {{ date('Y') }} Rifasys. Todos los derechos reservados.
            </div>

            {{-- Derecha: Solo logo de la agencia --}}
            <div class="flex items-center justify-center md:justify-end w-full md:w-auto">
                <img src="{{ asset('storage/externo/logoppal.png') }}" alt="Publicidad En Red" class="h-8 w-auto object-contain" />

            </div>
        </div>
    </div>
</footer>
