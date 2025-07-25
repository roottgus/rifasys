<div class="col-span-1 bg-gray-50 p-4 rounded-xl shadow min-h-[420px]">
    <h2 class="text-lg font-semibold mb-4 text-indigo-800 flex items-center gap-2">
        <i class="fas fa-list-ul text-indigo-500"></i>
        Métodos Disponibles
    </h2>
    <button type="button"
            @click="selectedIdx = null; showAll = true"
            class="flex items-center w-full px-3 py-3 mb-2 rounded-lg border border-dashed border-indigo-400 bg-indigo-50 hover:bg-indigo-100 transition gap-3 font-semibold text-indigo-800"
            :class="showAll ? 'shadow-lg ring-2 ring-indigo-400' : ''">
        <i class="fas fa-layer-group text-indigo-600"></i>
        Ver todos los métodos activos
    </button>
    {{-- Listar todos los métodos por variante --}}
    <template x-for="(method, idx) in methods" :key="method.id || method._key">
        <div class="mb-2">
            <button type="button"
                @click="selectMethod(idx); showAll = false"
                class="flex items-center w-full px-3 py-3 rounded-lg transition border border-transparent
                text-left gap-3 hover:bg-indigo-50"
                :class="selectedIdx === idx && !showAll
                        ? 'bg-indigo-100 border-indigo-400 shadow text-indigo-900'
                        : 'bg-white text-gray-700'">
                <span class="text-2xl" x-html="method.icon"></span>
                <span>
                    <span class="font-semibold" x-text="method.name"></span>
                    <span x-text="method.alias ? ` (${method.alias})` : ''" class="text-xs ml-1 text-indigo-500"></span>
                    <span x-show="!method.enabled" class="text-xs ml-2 px-2 py-1 rounded bg-gray-300 text-gray-700">Inactivo</span>
                </span>
                <span class="ml-auto flex items-center gap-2">
                    <input type="hidden" :name="'methods['+idx+'][enabled]'" value="0">
                    <input 
    type="checkbox" 
    :name="'methods['+idx+'][enabled]'"
    class="form-checkbox h-5 w-5 text-indigo-600"
    :checked="method.enabled"
    @change="toggleEnabled(idx)"
>

                </span>
            </button>
        </div>
    </template>

    {{-- Botón profesional para agregar un nuevo método de pago --}}
    <div class="mt-8">
        <button
            type="button"
            class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow flex items-center justify-center gap-2"
            @click="openAddModal = true">
            <i class="fas fa-plus-circle"></i>
            Agregar un nuevo método de pago
        </button>
    </div>
</div>
