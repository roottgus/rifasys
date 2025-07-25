<div  
    x-show="openAddModal" 
    x-transition.opacity
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40"
    style="display: none;"
    x-init="$watch('openAddModal', value => { if (value) toast.show = false })"
>
    <div 
        @click.away="openAddModal = false; toast.show = false;"
        class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 relative animate-fade-in"
    >
        <button type="button" @click="openAddModal = false; toast.show = false;" class="absolute top-3 right-3 ...">
    <i class="fas fa-times"></i>
</button>

        <h3 class="text-xl font-bold text-indigo-800 mb-4 flex items-center gap-2">
            <i class="fas fa-plus-circle text-indigo-500"></i>
            Agregar nuevo método de pago
        </h3>
        <p class="text-gray-600 mb-6 text-base">
            Selecciona el tipo de método de pago que deseas agregar.<br>
            Puedes crear múltiples variantes (por ejemplo, varias cuentas internacionales, nacionales, etc.).
        </p>
        <div class="grid grid-cols-1 gap-3">
            <template x-for="type in uniqueKeys" :key="type">
                <button
                    type="button"
                    class="flex items-center gap-3 w-full px-4 py-3 rounded-lg border border-indigo-100 bg-indigo-50 hover:bg-indigo-100 shadow-sm text-indigo-900 font-semibold text-left transition focus:ring-2 focus:ring-indigo-300"
                    @click="addNewMethod(type)"
                >
                    <span class="text-2xl" x-html="baseConfigs[type]?.icon"></span>
                    <span class="flex-1">
                        <span class="block font-bold text-indigo-800" x-text="baseConfigs[type]?.label"></span>
                        <span class="block text-xs text-gray-500" x-text="methodDesc[type]"></span>
                    </span>
                    <i class="fas fa-chevron-right text-indigo-400"></i>
                </button>
            </template>
        </div>
        <div class="mt-5 text-xs text-gray-500 text-center">
            Los métodos que agregues aquí estarán disponibles para selección rápida en ventas y reportes.
        </div>
    </div>
</div>
