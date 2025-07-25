{{-- resources/views/partials/_ultima_venta_nav.blade.php --}}
<div
  x-data="{ showVenta: false, ticket: null, tipo: '', anim: false }"
  x-init="
    window.addEventListener('ultima-venta-ticket', e => {
      ticket = e.detail.ticket;
      tipo = e.detail.tipo;
      anim = false; showVenta = true;
      setTimeout(() => anim = true, 10);
      setTimeout(() => { anim = false; showVenta = false; }, 17000);
    });
  "
  class="absolute left-1/2 -translate-x-1/2 flex items-center z-30"
  x-show="showVenta"
  x-cloak
>
  <div
    :class="{
      'bg-gray-400 border-gray-300 ring-gray-500/60 text-gray-900': tipo === 'Venta Total',
      'bg-red-400 border-red-300 ring-red-500/60 text-red-900': tipo === 'Apartado',
      'bg-purple-400 border-purple-300 ring-purple-500/60 text-purple-900': tipo === 'Abono'
      
    }"
    class="flex items-center gap-2 font-semibold px-4 py-2 rounded-full border-2 shadow-lg transition-all duration-300 animate-pulse ring-2 scale-100 opacity-100"
    :class="{ 'scale-100 opacity-100 shadow-xl': anim, 'scale-90 opacity-0': !anim }"
  >
    <i class="fa-solid fa-bolt text-white animate-spin"></i>
    <span class="text-lg font-mono tracking-widest drop-shadow">
      Ticket <span x-text="ticket"></span>
    </span>
    <span
      class="text-xs bg-white px-2 py-1 rounded-full ml-2 font-bold shadow"
      :class="{
        'text-gray-600': tipo === 'Venta Total',
        'text-red-600': tipo === 'Apartado',
        'text-purple-700': tipo === 'Abono'
        
      }"
    >
      <span x-text="tipo"></span>
    </span>
  </div>
</div>
