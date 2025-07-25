@if($saldoPendiente > 0)
<div x-data="abonoForm()" class="space-y-0">

    {{-- MENSAJE DE ÉXITO/ERROR --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
             class="w-full flex justify-center transition-all duration-700">
            <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 mb-4 flex items-center gap-2 shadow text-green-700 animate-fade-in">
                <i class="fa-solid fa-circle-check text-2xl text-green-500"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
             class="w-full flex justify-center transition-all duration-700">
            <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-4 flex items-center gap-2 shadow text-red-700 animate-fade-in">
                <i class="fa-solid fa-circle-xmark text-2xl text-red-500"></i>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.tickets.abonar', $ticket->id) }}"
        class="flex flex-col gap-4 bg-gradient-to-br from-green-50 via-white to-white rounded-2xl border border-green-100 shadow-xl p-6 mt-2 w-full max-w-[540px] mx-auto">
        @csrf

        <!-- Título y descripción -->
        <div class="mb-1 flex items-center gap-2">
            <i class="fa-solid fa-plus-circle text-green-600 text-lg"></i>
            <span class="font-semibold text-green-900 text-base">Registrar Abono Manual</span>
        </div>
        <div class="text-xs text-gray-500 mb-2 ml-7 -mt-2">
            Utilice este formulario solo para pagos o abonos directos hechos fuera del sistema.
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Monto siempre visible -->
            <div>
                <label class="block text-xs font-bold mb-1">Monto a abonar</label>
                <div class="relative">
                    <input type="number" step="0.01"
                        :max="maximo"
                        name="monto"
                        x-model="monto"
                        @input="validarMonto()"
                        class="w-full border border-gray-300 rounded-lg pl-9 pr-2 py-2 text-[13px] focus:ring-2 focus:ring-green-300 transition-all"
                        :placeholder="'Máximo: $' + maximo" required>
                    <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-[15px]">$</span>
                </div>
                <template x-if="errorMonto">
                    <div class="text-red-700 bg-red-100 border border-red-300 rounded px-2 py-1 mt-1 text-xs flex items-center gap-1">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span x-text="errorMonto"></span>
                    </div>
                </template>
            </div>

            <!-- Método de pago -->
            <div>
                <label class="block text-xs font-bold mb-1">Método de pago</label>
                <div class="relative">
                    <select name="metodo_pago" x-model="metodo_pago" @change="resetCampos()"
                            class="w-full border border-gray-300 rounded-lg pl-9 pr-9 py-2 text-[13px] focus:ring-2 focus:ring-green-300 bg-white text-gray-700 appearance-none transition-all font-semibold"
                            required>
                        <option value="">Seleccione...</option>
                        <option value="Pago_movil">Pago móvil</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Zelle">Zelle</option>
                        <option value="Efectivo">Efectivo</option>
                    </select>
                    <i class="fa-solid fa-money-check-dollar text-green-400 absolute left-2 top-1/2 -translate-y-1/2 text-[15px]"></i>
                    <svg class="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>

            <!-- Campos condicionales según método de pago -->
            <!-- ... Aquí van los templates condicionales tal como los tienes ... -->

            <!-- Zelle -->
            <template x-if="metodo_pago === 'Zelle'">
                <div>
                    <label class="block text-xs font-bold mb-1">Correo Titular (Zelle)</label>
                    <input type="email" name="correo" x-model="correo"
                        class="w-full border border-gray-300 rounded-lg pl-7 pr-2 py-2 text-[13px] focus:ring-2 focus:ring-green-300"
                        placeholder="Correo asociado a Zelle">
                </div>
            </template>
            <template x-if="metodo_pago === 'Zelle'">
                <div>
                    <label class="block text-xs font-bold mb-1">Banco Receptor</label>
                    <input type="text" name="banco" x-model="banco"
                        class="w-full border border-gray-300 rounded-lg pl-7 pr-2 py-2 text-[13px] focus:ring-2 focus:ring-green-300"
                        placeholder="Banco receptor (si aplica)">
                </div>
            </template>
            <!-- ... demás templates condicionales ... -->

            <!-- Referencia (NO para Efectivo) -->
            <template x-if="metodo_pago !== 'Efectivo'">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold mb-1">Referencia</label>
                    <input type="text" name="referencia" x-model="referencia" @input.debounce.500ms="validarReferenciaUnica"
                        class="w-full border border-gray-300 rounded-lg pl-7 pr-2 py-2 text-[13px] focus:ring-2 focus:ring-green-300"
                        placeholder="Referencia de pago" autocomplete="off">
                    <template x-if="errorReferencia">
                        <div class="text-red-700 bg-red-100 border border-red-300 rounded px-2 py-1 mt-1 text-xs flex items-center gap-1">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span x-text="errorReferencia"></span>
                        </div>
                    </template>
                    <template x-if="referenciaVerificando">
                        <span class="text-xs text-gray-400 ml-1">Verificando...</span>
                    </template>
                </div>
            </template>

            <!-- Fecha del pago (siempre visible) -->
            <div>
                <label class="block text-xs font-bold mb-1">Fecha del pago</label>
                <div class="relative">
                    <input type="datetime-local" name="fecha_pago" x-model="fecha_pago" @change="validarFechaPago"
                        class="w-full border border-gray-300 rounded-lg pl-9 pr-2 py-2 text-[13px] focus:ring-2 focus:ring-green-300"
                        :max="fechaMaxima" value="{{ now()->format('Y-m-d\TH:i') }}">
                    <i class="fa-solid fa-calendar-days text-orange-300 absolute left-2 top-1/2 -translate-y-1/2 text-[15px]"></i>
                </div>
                <template x-if="errorFecha">
                    <div class="text-red-700 bg-red-100 border border-red-300 rounded px-2 py-1 mt-1 text-xs flex items-center gap-1">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span x-text="errorFecha"></span>
                    </div>
                </template>
            </div>

            <!-- Lugar de Pago (solo efectivo) -->
            <template x-if="metodo_pago === 'Efectivo'">
                <div>
                    <label class="block text-xs font-bold mb-1">Lugar de Pago</label>
                    <select name="lugar_pago" x-model="lugar_pago"
                            class="w-full border border-gray-300 rounded-lg pl-7 pr-2 py-2 text-[13px] focus:ring-2 focus:ring-green-300">
                        <option value="">Seleccione...</option>
                        <option value="oficina">En la oficina</option>
                        <option value="punto_calle">Punto de calle</option>
                    </select>
                </div>
            </template>

            <!-- Nota (opcional) -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold mb-1">Nota (opcional)</label>
                <textarea name="nota" x-model="nota"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-[13px] focus:ring-2 focus:ring-green-300 resize-none"
                        rows="2" placeholder="Agrega una nota interna, si lo deseas"></textarea>
            </div>
        </div>

        <!-- Botón de acción -->
        <button type="submit"
            class="w-full mt-3 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-xl transition-all shadow flex items-center justify-center gap-2 text-[15px]"
            :disabled="errorMonto || errorReferencia || errorFecha || referenciaVerificando">
            <i class="fa fa-plus"></i> Agregar abono
        </button>
    </form>
</div>

{{-- Animación para mensajes --}}
<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px);}
    to   { opacity: 1; transform: translateY(0);}
}
.animate-fade-in { animation: fade-in 0.5s; }
</style>

<script>
function abonoForm() {
    return {
        monto: '',
        maximo: {{ $saldoPendiente }},
        errorMonto: '',
        metodo_pago: '',
        banco: '',
        referencia: '',
        fecha_pago: '{{ now()->format('Y-m-d\TH:i') }}',
        telefono: '',
        correo: '',
        cedula: '',
        lugar_pago: '',
        nota: '',
        errorReferencia: '',
        referenciaVerificando: false,
        errorFecha: '',
        fechaMaxima: new Date().toISOString().slice(0,16),

        resetCampos() {
            this.banco = '';
            this.referencia = '';
            this.telefono = '';
            this.correo = '';
            this.cedula = '';
            this.lugar_pago = '';
            this.nota = '';
        },

        validarMonto() {
            if (Number(this.monto) > this.maximo) {
                this.errorMonto = 'El monto máximo permitido es $' + this.maximo;
                this.monto = this.maximo;
            } else {
                this.errorMonto = '';
            }
        },

        async validarReferenciaUnica() {
            this.errorReferencia = '';
            if (!this.referencia || this.metodo_pago === 'Efectivo') {
                this.referenciaVerificando = false;
                return;
            }
            this.referenciaVerificando = true;
            try {
                const ref = this.referencia.trim();
                if (!ref) {
                    this.referenciaVerificando = false;
                    return;
                }
                const res = await fetch(`/test/validar-referencia?referencia=${encodeURIComponent(ref)}`);
                const json = await res.json();
                this.errorReferencia = json.existe
                    ? '⚠️ Esta referencia ya fue utilizada en otro pago o abono.'
                    : '';
            } catch {
                this.errorReferencia = 'No se pudo validar la referencia.';
            }
            this.referenciaVerificando = false;
        },

        validarFechaPago() {
            this.errorFecha = '';
            if (this.fecha_pago) {
                const hoy = new Date();
                const fechaPago = new Date(this.fecha_pago);
                hoy.setHours(0,0,0,0);
                fechaPago.setHours(0,0,0,0);
                if (fechaPago > hoy) {
                    this.errorFecha = 'No puedes seleccionar una fecha futura.';
                }
            }
        }
    }
}
</script>
@else
    <div class="flex flex-col items-center justify-center h-full py-16">
        <div class="bg-green-100 rounded-full p-6 mb-5 shadow">
            <i class="fa fa-check-circle text-green-600 text-6xl"></i>
        </div>
        <h3 class="text-green-700 text-2xl font-bold mb-2">¡Ticket pagado completamente!</h3>
        <div class="text-green-900 text-base text-center">
            No se pueden registrar más abonos porque este ticket ya está totalmente pagado.
        </div>
    </div>
@endif


<script>
function abonoForm() {
    return {
        monto: '',
        metodo_pago: '',
        banco: '',
        referencia: '',
        fecha_pago: '{{ now()->format('Y-m-d\TH:i') }}',
        telefono: '',
        correo: '',
        cedula: '',
        lugar_pago: '',
        nota: '',
        errorMonto: '',
        maximo: {{ $saldoPendiente }},
        errorReferencia: '',
        referenciaVerificando: false,
        errorFecha: '',
        fechaMaxima: new Date().toISOString().slice(0,16),

        resetCampos() {
            this.banco = '';
            this.referencia = '';
            this.telefono = '';
            this.correo = '';
            this.cedula = '';
            this.lugar_pago = '';
            this.nota = '';
        },

        validarMonto() {
            this.errorMonto = '';
            let valor = parseFloat(this.monto);
            if (isNaN(valor) || valor <= 0) {
                this.errorMonto = 'Debes ingresar un monto válido.';
            } else if (valor > this.maximo) {
                this.errorMonto = `El monto máximo permitido es $${this.maximo}`;
                this.monto = this.maximo;
            }
        },

        async validarReferenciaUnica() {
            this.errorReferencia = '';
            if (!this.referencia || this.metodo_pago === 'Efectivo') {
                this.referenciaVerificando = false;
                return;
            }
            this.referenciaVerificando = true;
            try {
                const ref = this.referencia.trim();
                if (!ref) {
                    this.referenciaVerificando = false;
                    return;
                }
                const res = await fetch(`/test/validar-referencia?referencia=${encodeURIComponent(ref)}`);
                const json = await res.json();
                this.errorReferencia = json.existe
                    ? '⚠️ Esta referencia ya fue utilizada en otro pago o abono.'
                    : '';
            } catch {
                this.errorReferencia = 'No se pudo validar la referencia.';
            }
            this.referenciaVerificando = false;
        },

        validarFechaPago() {
            this.errorFecha = '';
            if (this.fecha_pago) {
                const hoy = new Date();
                const fechaPago = new Date(this.fecha_pago);
                hoy.setHours(0,0,0,0);
                fechaPago.setHours(0,0,0,0);
                if (fechaPago > hoy) {
                    this.errorFecha = 'No puedes seleccionar una fecha futura.';
                }
            }
        }
    }
}
</script>
