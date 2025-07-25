<div>
    <div class="flex items-center gap-3 mb-3">
        <span class="text-3xl" x-html="selectedMethod.icon"></span>
        <h2 class="text-xl font-semibold text-indigo-900" x-text="selectedMethod.name"></h2>
        <span x-show="selectedMethod.alias" class="text-xs font-bold ml-1 px-2 py-1 rounded bg-indigo-50 text-indigo-700" x-text="selectedMethod.alias"></span>
        <span class="ml-2" x-show="selectedMethod.enabled">
            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded inline-flex items-center gap-1">
                <i class="fas fa-check-circle"></i> Activo
            </span>
        </span>
        <span class="ml-2" x-show="!selectedMethod.enabled">
            <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded inline-flex items-center gap-1">
                <i class="fas fa-ban"></i> Inactivo
            </span>
        </span>
    </div>
    <div class="mb-6 text-gray-500">
        <span x-text="selectedMethod.desc"></span>
    </div>

    <div class="space-y-4">
        {{-- Alias/nombre personalizado con ayuda --}}
        <div>
            <label class="flex items-center gap-1 text-sm font-semibold text-gray-700 mb-1">
    Alias
    <span class="text-xs text-gray-400 font-normal">(opcional)</span>
    <span class="ml-1 text-gray-400 cursor-pointer" title="Solo útil si tienes varias cuentas de este tipo. Ejemplo: 'Banco Mercantil principal' o 'Cuenta secundaria'.">
        <i class="fas fa-info-circle"></i>
    </span>
</label>

            <input
                :name="'methods['+selectedIdx+'][alias]'"
                type="text"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 transition"
                placeholder="Ejemplo: Cuenta secundaria (opcional)"
                x-model="selectedMethod.alias"
            >
            <input type="hidden" :name="'methods['+selectedIdx+'][id]'" x-model="selectedMethod.id">
            <input type="hidden"
    :name="'methods['+selectedIdx+'][key]'"
    :value="selectedMethod.key"
    x-model="selectedMethod.key"
>
<span x-show="!selectedMethod.key" class="text-xs text-red-500">
    [ADVERTENCIA: este método no tiene KEY. No se podrá guardar.]
</span>
            <input type="hidden" :name="'methods['+selectedIdx+'][name]'" x-model="selectedMethod.name">
        </div>
        <template x-for="(field, fIdx) in selectedMethod.fields" :key="fIdx">
            <div>
                <label :for="'details-'+selectedMethod.key+'-'+field.key" class="block text-sm font-semibold text-gray-700 mb-1" x-text="field.label"></label>
                <input 
                    :id="'details-'+selectedMethod.key+'-'+field.key"
                    :name="'methods['+selectedIdx+'][details]['+field.key+']'"
                    type="text"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 transition"
                    :placeholder="field.placeholder"
                    x-model="field.value"
                >
            </div>
        </template>
    </div>
    <div class="mt-8 flex justify-between">
        <button type="button"
    class="px-4 py-2 bg-red-500 hover:bg-red-700 text-white rounded-xl font-semibold shadow transition mt-2"
    @click="askDeleteVariant(selectedIdx)">
    Eliminar Variante
</button>



        <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow font-bold">
            Guardar configuración
        </button>
    </div>
</div>
