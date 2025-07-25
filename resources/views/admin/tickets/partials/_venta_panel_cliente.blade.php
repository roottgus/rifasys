<div class="relative">
    <h3 class="text-xl font-bold text-primary mb-2 flex items-center gap-2">
        <i class="fas fa-user"></i> Datos del Cliente
    </h3>
    <div class="mb-3">
        <input type="text"
            class="w-full px-3 py-2 rounded border text-sm bg-white mb-1 shadow"
            placeholder="Ingresa los datos o selecciona un cliente ya registrado."
            style="background: #eef6ff; border:none;"
            disabled
        >
    </div>
    {{-- Mensaje de bienvenida SOLO ARRIBA --}}
    <template x-if="validacion.cedula === true && !validacion.conflicto.cedula && validacion.mensaje.cedula.includes('registrado')">
        <div class="mb-3 text-green-900 bg-green-100 border border-green-300 rounded px-3 py-2 flex items-center gap-2 animate-fade-in">
            <i class="fas fa-check-circle"></i>
            <span x-text="validacion.mensaje.cedula"></span>
        </div>
    </template>
    @include('admin.tickets._modal_venta_inputs')
</div>
