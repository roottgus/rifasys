{{-- Paso 3: Éxito --}}
<div
  x-show="ventaExitosa"
  x-transition
  class="fixed inset-0 flex items-center justify-center z-50"
  style="background: rgba(0,0,0,0.60);"
>
  <!-- Confetti puro CSS -->
  <div class="absolute inset-0 pointer-events-none overflow-hidden">
    <template x-for="i in 25">
      <span
        class="absolute rounded-full"
        :style="`
          left: ${Math.random() * 100}vw;
          animation: confetti-fall ${Math.random() * 0.8 + 1.5}s linear forwards;
          background: hsl(${Math.random()*360},100%,60%);
          width: ${Math.random()*6+6}px;
          height: ${Math.random()*6+6}px;
          top: -10px;
        `"
      ></span>
    </template>
    <style>
      @keyframes confetti-fall {
        to {
          transform: translateY(100vh) rotate(720deg);
          opacity: 0.7;
        }
      }
    </style>
  </div>

  <!-- Tarjeta de éxito -->
  <div class="relative bg-white rounded-2xl shadow-2xl p-10 max-w-md w-full text-center border-2 border-primary/20 z-10 animate-fade-in">
    <!-- Icono dinámico animado -->
    <div class="flex justify-center mb-4">
      <template x-if="accionRealizada === 'vender' || accionRealizada === 'abono'">
        <svg class="w-20 h-20 text-green-500 animate-pop-in" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 48 48">
          <circle class="opacity-20" cx="24" cy="24" r="22" stroke="currentColor" stroke-width="5"/>
          <path x-show="ventaExitosa" stroke-linecap="round" stroke-linejoin="round"
                d="M15 24l7 7 11-13"
                class="stroke-current animate-checkmark"
          />
        </svg>
      </template>
      <template x-if="accionRealizada === 'apartado'">
        <i class="fas fa-hourglass-half text-orange-500 text-7xl animate-bounce"></i>
      </template>
      <style>
        .animate-pop-in {
          animation: pop-in 0.4s cubic-bezier(.42,1.54,.74,1.14);
        }
        @keyframes pop-in {
          0% { transform: scale(0.5); opacity: 0;}
          60% { transform: scale(1.2); opacity: 1;}
          100% { transform: scale(1); }
        }
        .animate-checkmark {
          stroke-dasharray: 48;
          stroke-dashoffset: 48;
          animation: draw-check 0.7s 0.2s cubic-bezier(.42,1.54,.74,1.14) forwards;
        }
        @keyframes draw-check {
          to { stroke-dashoffset: 0; }
        }
      </style>
    </div>
    <div
      :class="{
        'text-indigo-700': accionRealizada === 'vender',
        'text-orange-700': accionRealizada === 'apartado',
        'text-purple-700': accionRealizada === 'abono'
      }"
      class="text-2xl font-bold mb-2 flex items-center justify-center gap-2"
    >
      <span x-text="mensajeExito"></span>
    </div>
    <div class="text-gray-700 text-lg mb-2">
      Ticket
      <span class="font-mono bg-gray-100 px-2 rounded" x-text="picked ? String(picked.numero).padStart(padLen,'0') : '--'"></span><br>
      <span class="font-semibold" x-text="cliente.nombre"></span>
    </div>

    <!-- QR del ticket (asegúrate de que picked.qr_code tenga el base64, lo necesitas para impresión) -->
    <div class="flex justify-center my-2">
      <img
        id="qr-ticket-img"
        :src="picked && picked.qr_code ? picked.qr_code : ''"
        alt="QR del Ticket"
        class="mx-auto rounded border border-gray-200 shadow w-32 h-32 bg-white"
        x-show="picked && picked.qr_code"
      >
    </div>

    <!-- Botón de impresión térmica -->
    <button
      type="button"
      class="block w-full mt-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded shadow transition font-semibold"
      @click="imprimirTicket()"
    >
      <i class="fas fa-print mr-2"></i> Imprimir en Térmica
    </button>

    <button
      @click="closeModal()"
      class="mt-5 px-8 py-3 rounded-lg bg-primary text-white font-semibold shadow hover:bg-primary/90 transition text-lg w-full"
    >Cerrar</button>
  </div>
</div>
