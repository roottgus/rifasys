<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Ticket - {{ $settings['empresa_nombre'] }}</title>
    @vite(['resources/css/app.css'])
    <meta name="robots" content="noindex,nofollow">
    <style>
        .marca-agua-empresa {
            position: absolute;
            bottom: 20px;
            right: 20px;
            opacity: 0.08;
            width: 120px;
            z-index: 0;
            pointer-events: none;
        }
        .error-x svg {
            animation: bounceIn 0.7s cubic-bezier(.25,1.7,.7,1.6) both;
        }
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.7) rotate(-25deg);}
            50% { opacity: 1; transform: scale(1.08) rotate(6deg);}
            75% { transform: scale(0.92) rotate(-4deg);}
            100% { opacity: 1; transform: scale(1) rotate(0);}
        }
        @media (max-width: 480px) {
            .verif-card {
                padding: 1rem !important;
                border-radius: 1rem !important;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-white to-[{{ $settings['empresa_color'] ?? '#0d47a1' }}] min-h-screen flex items-center justify-center p-2">

    @php
        $logo = $settings['empresa_logo'] ?? null;
        if ($logo && !str_starts_with($logo, 'logos/')) {
            $logo = 'logos/' . $logo;
        }
    @endphp

    <div class="bg-white shadow-2xl rounded-2xl p-8 max-w-md w-full text-center border-2 verif-card relative" style="border-color: {{ $settings['empresa_color'] ?? '#0d47a1' }};">

        {{-- Logo de empresa --}}
        @if ($logo)
            <img src="{{ asset('storage/'.$logo) }}"
                 class="mx-auto mb-2 h-14 w-auto rounded shadow-sm" alt="Logo empresa"
                 style="object-fit: contain; max-width: 120px;"/>
        @endif

        <div class="text-xl font-bold mb-2" style="color: {{ $settings['empresa_color'] ?? '#0d47a1' }}">
            {{ $settings['empresa_nombre'] }}
        </div>

        {{-- Mensaje de Éxito --}}
        @if (!empty($success))
            <div class="mb-4 bg-green-100 text-green-800 rounded-lg py-2 px-3 font-semibold shadow flex items-center justify-center gap-2">
                <i class="fa-solid fa-check-circle"></i>
                <span>{{ $success }}</span>
            </div>
        @endif

        {{-- Mensaje de Error --}}
        @if (!empty($error))
            <div class="mb-4 error-x flex flex-col items-center justify-center">
                {{-- Animación SVG de X --}}
                <svg viewBox="0 0 64 64" width="52" height="52" fill="none">
                    <circle cx="32" cy="32" r="30" fill="#F87171"/>
                    <path d="M22 22l20 20M42 22L22 42" stroke="#fff" stroke-width="5" stroke-linecap="round"/>
                </svg>
                <div class="mt-3 bg-red-100 text-red-700 rounded-lg py-2 px-3 font-semibold shadow flex items-center justify-center gap-2">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    <span>{{ $error }}</span>
                </div>
            </div>
        @endif

        {{-- Pantalla de ticket válido --}}
        @if (empty($error))
            <div class="mt-2 text-lg font-semibold tracking-tight" style="color: {{ $settings['empresa_color'] ?? '#0d47a1' }}">
                Verificación de Ticket
            </div>
            <div class="text-xs text-gray-500 mb-3">Valida que estos datos coincidan con tu ticket o recibo de compra.</div>

            {{-- Datos del Ticket, organizados en columnas --}}
            <div class="mb-5 grid grid-cols-1 gap-2 text-left text-base max-w-xs mx-auto">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-certificate text-primary"></i>
                    <span class="font-medium text-gray-700 flex-1">Rifa:</span>
                    <span class="font-mono text-right">{{ $ticket->rifa->nombre }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-hashtag text-primary"></i>
                    <span class="font-medium text-gray-700 flex-1">Número:</span>
                    <span class="bg-gray-100 rounded px-2 py-0.5 text-base font-mono" style="color: {{ $settings['empresa_color'] ?? '#0d47a1' }};">
                        {{ str_pad($ticket->numero, 3, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-user text-primary"></i>
                    <span class="font-medium text-gray-700 flex-1">Cliente:</span>
                    <span class="truncate text-right">{{ optional($ticket->cliente)->nombre ?? '—' }}</span>
                </div>
                @if(optional($ticket->cliente)->telefono)
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-phone text-primary"></i>
                        <span class="font-medium text-gray-700 flex-1">Teléfono:</span>
                        <a href="tel:{{ optional($ticket->cliente)->telefono }}" class="text-blue-700 hover:underline text-right">{{ optional($ticket->cliente)->telefono }}</a>
                    </div>
                @endif
                @if(optional($ticket->cliente)->direccion)
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-primary"></i>
                        <span class="font-medium text-gray-700 flex-1">Dirección:</span>
                        <span class="truncate text-right">{{ optional($ticket->cliente)->direccion }}</span>
                    </div>
                @endif
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-primary"></i>
                    <span class="font-medium text-gray-700 flex-1">Estado:</span>
                    <span class="uppercase text-right font-semibold
                        @if($ticket->estado === 'verificado') text-green-700
                        @elseif($ticket->estado === 'abonado') text-yellow-700
                        @elseif($ticket->estado === 'vendido') text-blue-700
                        @elseif($ticket->estado === 'anulado') text-red-600
                        @else text-gray-700
                        @endif
                    ">
                        {{ strtoupper($ticket->estado) }}
                    </span>
                </div>
            </div>

            {{-- Premios Especiales --}}
            @if (isset($premiosEspeciales) && count($premiosEspeciales))
                <div class="mb-3 bg-gradient-to-r from-blue-50 to-white border border-blue-200 rounded-xl p-4 text-left max-w-xs mx-auto">
                    <div class="font-semibold text-blue-700 mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-gift"></i> Premios Especiales de la Rifa
                    </div>
                    <ul class="space-y-3">
                        @foreach ($premiosEspeciales as $premio)
                            <li class="p-3 rounded-lg flex flex-col gap-1
                                @if($premio['participa'])
                                    bg-green-50 border-l-4 border-green-500
                                @else
                                    bg-red-50 border-l-4 border-red-400
                                @endif">
                                <div class="font-bold text-gray-700">{{ $premio['nombre'] }}</div>
                                <div class="text-xs text-gray-500">{{ $premio['detalle'] }}</div>
                                <div class="text-xs mt-1 text-gray-400">
                                    Fecha: {{ $premio['fecha'] }} {{ $premio['hora'] ? ' - '.$premio['hora'] : '' }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    Monto Premio: ${{ $premio['monto'] }}
                                </div>
                                <div class="text-xs">
                                    Mínimo Abono: <span class="font-bold">${{ $premio['abono_minimo'] }}</span>
                                </div>
                                <div class="text-right">
                                    @if($premio['participa'])
                                        <span class="inline-flex items-center gap-1 text-green-600 font-semibold">
                                            <i class="fa-solid fa-check-circle"></i> Participa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-red-500 font-semibold">
                                            <i class="fa-solid fa-times-circle"></i>
                                            No participa
                                        </span>
                                        <div class="text-xs text-gray-500">
                                            (Abonado: ${{ $premio['total_abonado'] }} / requiere ${{ $premio['abono_minimo'] }})
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif

        {{-- Pie --}}
        <div class="mt-6 text-xs text-gray-400 font-mono">
            {{ $settings['empresa_nombre'] }} &mdash; Verificación Online<br>
            <span>Desarrollado para uso oficial y administrativo.</span>
        </div>
        @if ($logo)
            <img src="{{ asset('storage/'.$logo) }}"
                alt="Marca de agua"
                class="marca-agua-empresa" />
        @endif
    </div>

    {{-- FontAwesome desde NPM --}}
    <script type="module">
        import { library, dom } from '@fortawesome/fontawesome-svg-core';
        import { faGift, faUser, faCertificate, faHashtag, faPhone, faLocationDot, faCheckCircle, faTimesCircle, faExclamationTriangle, faCircleInfo } from '@fortawesome/free-solid-svg-icons';
        library.add(faGift, faUser, faCertificate, faHashtag, faPhone, faLocationDot, faCheckCircle, faTimesCircle, faExclamationTriangle, faCircleInfo);
        dom.watch();
    </script>
</body>
</html>
