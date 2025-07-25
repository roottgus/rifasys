{{-- resources/views/admin/tickets/_cards.blade.php --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">

  @php
    $cards = [
      ['filter'=>'all',        'value'=>'tickets && Array.isArray(tickets) ? tickets.length : 0',    'label'=>'Total emitidos',      'icon'=>'fa-list-alt',       'bg'=>'bg-teal-500'],
      ['filter'=>'disponible', 'value'=>'typeof countDisponibles === \'function\' ? countDisponibles() : 0', 'label'=>'Tickets libres',   'icon'=>'fa-check-circle',   'bg'=>'bg-green-500'],
      ['filter'=>'vendido',    'value'=>'typeof countVendidos === \'function\' ? countVendidos() : 0',    'label'=>'Tickets vendidos',  'icon'=>'fa-shopping-cart',  'bg'=>'bg-gray-400'],
      ['filter'=>'reservado',  'value'=>'typeof countApartados === \'function\' ? countApartados() : 0',   'label'=>'Tickets reservados', 'icon'=>'fa-hand-paper',     'bg'=>'bg-red-500'],
      ['filter'=>'abonado',    'value'=>'typeof countAbonados === \'function\' ? countAbonados() : 0',      'label'=>'Tickets abonados',   'icon'=>'fa-coins',          'bg'=>'bg-purple-500'],
      ['filter'=>null,         'value'=>null,                'label'=>'Ingresos totales',     'icon'=>'fa-dollar-sign',    'bg'=>'bg-gray-700'],
    ];
  @endphp

  @foreach($cards as $c)
    @if($c['filter'])
      <div
        @click="setFilter('{{ $c['filter'] }}')"
        :class="filter==='{{ $c['filter'] }}' ? 'ring-2 ring-white' : ''"
        class="relative {{ $c['bg'] }} text-white rounded-lg shadow-lg overflow-hidden cursor-pointer hover:shadow-md transition-shadow"
      >
        {{-- watermark icon --}}
        <i class="fas {{ $c['icon'] }} absolute right-4 top-4 text-6xl text-white/20"></i>

        <div class="p-4">
          <p class="text-3xl font-bold leading-none" x-text="{!! $c['value'] !!}"></p>
          <p class="mt-1">{{ $c['label'] }}</p>
        </div>

        <div class="bg-black bg-opacity-10 text-sm py-2 text-center">
          <span>Aplicar filtro <i class="fas fa-filter ml-1"></i></span>
        </div>
      </div>
   @else
  <div
    class="relative {{ $c['bg'] }} text-white rounded-lg shadow-lg overflow-hidden cursor-default"
    style="min-height: 140px;"
  >
    {{-- watermark icon --}}
    <i class="fas {{ $c['icon'] }} absolute right-4 top-4 text-6xl text-white/20"></i>

    <div class="p-4">
      <p class="text-3xl font-bold leading-none">
        $<span x-text="typeof totalAbonos === 'function' ? totalAbonos() : 0"></span>
      </p>
      <p class="mt-1">{{ $c['label'] }}</p>
    </div>

    {{-- Línea de resumen minimalista --}}
    <div class="absolute left-0 right-0 bottom-0 bg-black/10 text-[13px] py-1 px-4 flex items-center justify-between gap-2 rounded-b-lg">
      <span class="flex items-center gap-1" title="Vendidos">
        <i class="fas fa-shopping-cart text-yellow-200"></i>
        <span x-text="typeof countVendidos === 'function' ? countVendidos() : 0"></span>
      </span>
      <span class="flex items-center gap-1" title="Disponibles">
        <i class="fas fa-check-circle text-green-200"></i>
        <span x-text="typeof countDisponibles === 'function' ? countDisponibles() : 0"></span>
      </span>
      <span class="flex items-center gap-1" title="% Vendido">
        <i class="fas fa-percent text-blue-200"></i>
        <span>
          <span x-text="tickets && Array.isArray(tickets) && countVendidos && typeof countVendidos === 'function' ? (tickets.length ? Math.round(100 * countVendidos() / tickets.length) : 0) : 0"></span>%
        </span>
      </span>
    </div>

    <div class="bg-black bg-opacity-10 text-sm py-2 text-center">
      <span>Ver totales <i class="fas fa-chart-line ml-1"></i></span>
    </div>
  </div>
@endif


  @endforeach

</div>
