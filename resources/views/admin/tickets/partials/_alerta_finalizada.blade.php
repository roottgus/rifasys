<template x-if="getRifa() && getRifa().fecha_sorteo && (new Date(getRifa().fecha_sorteo) < new Date())">
    <div class="w-full bg-red-100 border-2 border-red-500 rounded-xl p-6 flex items-center gap-4 shadow-lg mb-4 animate-pulse">
        <i class="fas fa-exclamation-triangle text-4xl text-red-600"></i>
        <div>
            <span class="block text-lg font-extrabold text-red-800 mb-1 blink">¡Atención!</span>
            <span class="block text-red-700 text-base font-semibold">
                Esta rifa <span class="font-bold underline" x-text="getRifa().nombre"></span> ya está FINALIZADA.<br>
                No es posible vender más tickets.
            </span>
        </div>
    </div>
</template>
