<div class="relative bg-white rounded-2xl shadow-md hover:scale-105 hover:shadow-xl transition p-0 border border-gray-200 flex flex-col min-h-[210px] group overflow-hidden">
    {{-- Cabecera visual con icono de fondo y título --}}
    <div class="relative z-10 px-4 py-4 border-b flex items-center h-14 mb-2"
         :class="{
            'bg-indigo-50 border-indigo-100': method.key === 'tran_bancaria_nacional',
            'bg-green-50 border-green-100': method.key === 'pago_efectivo',
            'bg-orange-50 border-orange-100': method.key === 'pago_movil',
            'bg-blue-50 border-blue-100': method.key === 'tran_bancaria_internacional',
            'bg-purple-50 border-purple-100': method.key === 'zelle'
         }">
        <div class="absolute inset-0 flex items-center justify-end z-0 opacity-20 pointer-events-none">
            <span class="text-[48px]" x-html="method.icon"></span>
        </div>
        <span class="relative z-10 font-bold text-[15px] leading-tight tracking-tight"
            :class="{
                'text-indigo-900': method.key === 'tran_bancaria_nacional',
                'text-green-800': method.key === 'pago_efectivo',
                'text-orange-700': method.key === 'pago_movil',
                'text-blue-900': method.key === 'tran_bancaria_internacional',
                'text-purple-900': method.key === 'zelle'
            }"
            x-text="method.name + (method.alias ? ' (' + method.alias + ')' : '')"></span>
        <span class="ml-auto relative z-10 bg-green-100 text-green-800 text-xs px-2 py-1 rounded flex items-center gap-1 font-semibold shadow"
              x-show="method.enabled">
            <i class="fas fa-check-circle"></i> Activo
        </span>
        <span class="ml-auto relative z-10 bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded flex items-center gap-1 font-semibold shadow"
              x-show="!method.enabled">
            <i class="fas fa-ban"></i> Inactivo
        </span>
    </div>
    {{-- Datos del método --}}
    <div class="relative z-10 p-4 pt-2 grid grid-cols-1 gap-y-2">
        <template x-for="field in method.fields" :key="field.key">
            <div class="flex flex-col mb-1">
                <span class="font-semibold text-gray-600 text-xs uppercase tracking-wide mb-0.5" x-text="field.label"></span>
                <span class="text-gray-900 text-sm break-words break-all whitespace-pre-line max-w-[210px] font-medium pl-1"
                      x-text="field.value || '-'"></span>
            </div>
        </template>
    </div>
</div>
