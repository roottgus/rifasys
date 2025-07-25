<div class="flex flex-col items-center justify-center gap-3 my-4">

    <!-- QR o Placeholder -->
    <div class="flex flex-col items-center gap-2 w-full">
        <template x-if="picked && picked.qr_code">
            <img
                :src="picked.qr_code"
                alt="Código QR de Verificación"
                class="w-32 h-32 sm:w-40 sm:h-40 rounded-xl shadow-md border border-gray-200 bg-white"
                draggable="false"
            />
        </template>
        <template x-if="!picked || !picked.qr_code">
            <div class="w-32 h-32 sm:w-40 sm:h-40 flex items-center justify-center rounded-xl bg-gray-50 border border-dashed border-gray-200 text-gray-300 text-sm">
                Sin QR disponible
            </div>
        </template>
        <div class="text-xs text-gray-500 text-center mt-1">
            Escanea para verificar tu ticket
        </div>
    </div>

    <!-- Código de verificación -->
    <div class="text-xs text-gray-700 font-mono bg-gray-100 rounded px-2 py-1 mt-2 break-all text-center">
        Código de verificación:<br>
        <span x-text="picked && picked.uuid ? picked.uuid : '—'"></span>
    </div>

    
</div>
