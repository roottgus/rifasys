{{-- resources/views/admin/tickets/partials/_venta_panel_pago.blade.php --}}
<div x-show="paso === 2" x-transition>
    <h4 class="text-lg font-semibold mb-3 text-center">
        <i class="fas fa-credit-card text-primary"></i> Método de Pago
    </h4>

    <template x-if="errorPago">
        <div class="mb-3 text-red-700 bg-red-100 border border-red-300 rounded px-3 py-2 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i>
            <span x-text="errorPago"></span>
        </div>
    </template>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Columna 1: Botones de métodos de pago --}}
        <div class="md:col-span-1 md:w-[250px] mx-auto flex flex-col gap-3">
            <template x-for="mp in metodosPagoActivos" :key="mp.key">
                <button
                    type="button"
                    @click="seleccionarMetodoPago(mp.key)"
                    :class="metodoPago === mp.key
                        ? 'border-2 border-primary bg-white ring-4 ring-primary/10 scale-[1.035] shadow-lg'
                        : 'border border-gray-200 bg-white hover:border-primary/40 hover:shadow-md transition'"
                    class="relative flex items-center px-5 py-4 rounded-2xl overflow-hidden group transition-all duration-200 w-full focus:outline-none"
                >
                    {{-- Watermark icon --}}
                    <span class="absolute right-4 bottom-2 opacity-10 group-hover:opacity-15 text-5xl pointer-events-none transition-all duration-200 scale-110">
                        <template x-if="mp.key === 'zelle'">
                            <img src="/images/zelle.svg" class="w-12 h-12" alt="Zelle"/>
                        </template>
                        <template x-if="mp.key !== 'zelle'">
                            <i :class="mp.icon"></i>
                        </template>
                    </span>
                    <span class="flex-shrink-0 inline-flex items-center justify-center rounded-full bg-primary/10 group-hover:bg-primary/20 mr-3 text-xl w-10 h-10 transition">
                        <template x-if="mp.key === 'zelle'">
                            <img src="/images/zelle.svg" class="w-7 h-7" alt="Zelle" />
                        </template>
                        <template x-if="mp.key !== 'zelle'">
                            <i :class="mp.icon" class="text-primary"></i>
                        </template>
                    </span>
                    <span class="relative z-10 text-left flex-1">
                        <span class="block text-[15px] font-bold leading-tight tracking-tight"
                            x-text="mp.name"></span>
                        <span class="block text-xs text-gray-500 truncate" x-show="mp.alias" x-text="mp.alias"></span>
                    </span>
                    <span x-show="metodoPago === mp.key" class="ml-3 relative z-10">
                        <i class="fas fa-check-circle text-primary text-lg drop-shadow"></i>
                    </span>
                    <span
                        x-show="metodoPago === mp.key"
                        class="absolute inset-0 rounded-2xl border-2 border-primary pointer-events-none animate-pulse"
                        style="z-index:2;"
                    ></span>
                </button>
            </template>
        </div>
        {{-- Columna 2: datos de la empresa --}}
        <div class="md:col-span-1 md:w-[300px] mx-auto">
            <template x-if="!metodoPago">
                <div class="text-gray-400 text-center py-8 border border-gray-200 rounded-lg">
                    Selecciona un método para ver los datos de la empresa aquí.
                </div>
            </template>
            <template x-if="metodoPago">
                <div class="relative bg-primary/5 border-2 border-primary/20 rounded-xl px-3 py-3 animate-fade-in shadow-md overflow-hidden">
                    <span class="absolute left-1/2 bottom-2 -translate-x-1/2 opacity-10 text-[60px] pointer-events-none select-none" style="z-index:1;">
                        <i :class="obtenerDetallesMetodo().icon"></i>
                    </span>
                    <div class="relative z-10 mb-4 text-center">
                        <div class="font-bold text-primary text-base" x-text="obtenerDetallesMetodo().name"></div>
                        <div class="text-primary/80 text-xs mt-1" x-text="obtenerDetallesMetodo().descripcion"></div>
                        <div class="text-gray-500 text-xs mt-1" x-text="obtenerDetallesMetodo().info"></div>
                    </div>
                    <div class="space-y-3 relative z-10">
                        <template x-for="(field, idx) in obtenerDetallesMetodo().fields" :key="field.key">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-0.5" x-text="field.label"></label>
                                <input
                                    type="text"
                                    :value="field.value"
                                    class="w-full border border-gray-200 rounded px-2 py-1 text-xs bg-gray-50 text-gray-700 cursor-not-allowed"
                                    readonly
                                >
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
        {{-- Columna 3: formulario “Reportar mi pago” --}}
        <div class="md:col-span-1 md:w-[230px] mx-auto">
            <template x-if="!metodoPago">
                <div class="text-gray-400 text-center py-8 border border-gray-200 rounded-lg">
                    Aquí aparecerá el formulario para reportar tu pago.
                </div>
            </template>
            {{-- ...todo igual arriba... --}}
<template x-if="metodoPago">
    <div
        class="bg-white border-2 border-primary/10 rounded-2xl p-6 shadow-lg animate-fade-in flex flex-col items-center relative overflow-hidden"
        x-transition:enter="transition transform ease-out duration-200"
        x-transition:enter-start="scale-95 opacity-0"
        x-transition:enter-end="scale-100 opacity-100"
        x-transition:leave="transition transform ease-in duration-150"
        x-transition:leave-start="scale-100 opacity-100"
        x-transition:leave-end="scale-95 opacity-0"
    >
        <span class="absolute right-4 top-4 opacity-10 text-4xl pointer-events-none select-none">
            <i class="fas fa-file-invoice-dollar"></i>
        </span>
        <div class="mb-2 w-full text-center">
            <h5 class="text-xl font-bold text-primary mb-1">Reportar mi pago</h5>
            <p class="text-xs text-gray-500 mb-4">
                Ingresa los datos del comprobante para que el sistema valide tu pago.
            </p>
        </div>

        <form class="space-y-4 w-full" autocomplete="off" @submit.prevent>
            <template x-for="field in reportFields()" :key="field.key">
                <!-- ...campos de formulario como tienes actualmente... -->
                <div class="relative">
                    <label class="block text-xs font-semibold text-gray-600 mb-1" x-text="field.label"></label>
                    <template x-if="field.type === 'select'">
                        <select
                            x-model="pagoDatos[field.key]"
                            class="w-full border border-primary/30 rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary/30 bg-gray-50"
                            required
                        >
                            <option value="" disabled selected>-- Selecciona una opción --</option>
                            <template x-for="opt in field.options" :key="opt.value">
                                <option :value="opt.value" x-text="opt.label"></option>
                            </template>
                        </select>
                    </template>
                    <template x-if="field.type === 'date'">
                        <input
                            type="date"
                            x-model="pagoDatos[field.key]"
                            :max="hoyStr"
                            class="w-full border border-primary/30 rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary/30 bg-gray-50"
                            required
                        >
                    </template>
                    <template x-if="field.type === 'number'">
                        <input
                            type="number"
                            step="0.01"
                            x-model="pagoDatos[field.key]"
                            placeholder="Ej: 100.00"
                            class="w-full border border-primary/30 rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary/30 bg-gray-50"
                            required
                        >
                    </template>
                    <template x-if="field.type === 'text'">
                        <div>
                            <input
                                type="text"
                                x-model="pagoDatos[field.key]"
                                :placeholder="'Ingresa ' + field.label.toLowerCase()"
                                @input="field.key === 'referencia' && validarReferenciaUnica()"
                                class="w-full border border-primary/30 rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary/30 bg-gray-50"
                                required
                            >
                            <!-- Error debajo del campo referencia -->
                            <template x-if="field.key === 'referencia' && errorReferencia">
                                <div class="text-xs text-red-600 mt-1 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span x-text="errorReferencia"></span>
                                </div>
                            </template>
                            <!-- Loader debajo del campo referencia -->
                            <template x-if="field.key === 'referencia' && referenciaVerificando">
                                <div class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <span>Validando referencia...</span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </form>
    </div>
</template>


    
</div>
