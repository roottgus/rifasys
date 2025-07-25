<template x-if="getRifa()">
    <div class="sticky-badge animate-pulse">
        <i class="fas fa-bullseye text-2xl"></i>
        Vendiendo en: 
        <span x-text="getRifa().nombre" class="font-bold"></span>
        <span
            :class="(getRifa().fecha_sorteo && new Date(getRifa().fecha_sorteo) < new Date() ? 'bg-red-600 text-white blink' : 'bg-green-600 text-white animate-pulse') + ' ml-2 px-3 py-1 rounded-full text-xs font-bold shadow border'"
            x-text="(getRifa().fecha_sorteo && new Date(getRifa().fecha_sorteo) < new Date()) ? 'FINALIZADA' : 'ACTIVA'">
        </span>
    </div>
</template>
