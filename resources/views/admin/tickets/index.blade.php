@extends('layouts.admin')

@section('title', 'Listado de Tickets')

@section('content')
<div class="p-6 space-y-8">

    {{-- Flash-messages --}}
    @if(session('success'))
      <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    {{-- Tarjetas de selección de rifa --}}
    <div class="flex flex-wrap gap-4 mb-8" id="rifaSelector">
        @foreach($rifas as $rifa)
            <div 
                class="rifa-card cursor-pointer px-6 py-4 rounded-xl shadow 
                    bg-white border-2 border-gray-200 hover:border-primary transition-all
                    text-gray-700 font-bold text-lg flex flex-col items-center"
                data-rifa-id="{{ $rifa->id }}"
                style="min-width: 220px;"
            >
                <span class="mb-1 text-primary text-2xl">{{ $rifa->nombre }}</span>
                <span class="text-xs text-gray-400 font-normal"># de tickets: {{ $rifa->cantidad_numeros }}</span>
                <span class="text-xs text-gray-500 font-normal mt-1">Sorteo: {{ \Carbon\Carbon::parse($rifa->fecha_sorteo)->format('d M Y') }}</span>
            </div>
        @endforeach
    </div>

    {{-- Resumen de la rifa seleccionada --}}
    <div 
        x-data="resumenRifa()" 
        x-init="initResumen({{ $rifas->first()->id ?? 'null' }})"
        id="rifaResumenWrapper"
    >
        @include('admin.tickets._rifa_resumen')
    </div>

    {{-- Barra de búsqueda y filtros --}}
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-4">
        <form id="buscadorTickets" class="flex gap-2 w-full md:w-1/2" autocomplete="off">
            <input
                type="text"
                name="q"
                id="searchInput"
                value=""
                placeholder="Buscar por nombre, cédula o número de ticket"
                class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-primary outline-none"
                autocomplete="off"
            >
            <input type="hidden" name="estado" id="estadoInput" value="">
            <input type="hidden" name="rifa_id" id="rifaInput" value="{{ $rifas->first()->id ?? '' }}">
            <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg font-bold shadow">
                Buscar
            </button>
        </form>
        <div class="flex gap-2 mt-2 md:mt-0">
            <script>
                window.cambiarEstado = function (nuevoEstado) {
                    const estadoInput = document.getElementById('estadoInput');
                    const form = document.getElementById('buscadorTickets');
                    estadoInput.value = nuevoEstado;
                    form.dispatchEvent(new Event('submit'));
                };
            </script>
            @foreach(['todos' => 'Todos', 'vendido' => 'Vendidos', 'abonado' => 'Abonados', 'apartado' => 'Apartados', 'reservado' => 'Reservados'] as $value => $label)
                <button
                    type="button"
                    class="px-4 py-2 rounded-lg font-bold shadow
                    {{ request('estado', 'todos') === $value ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700 hover:bg-primary/10 hover:text-primary' }}"
                    onclick="cambiarEstado('{{ $value === 'todos' ? '' : $value }}')"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Contenedor AJAX para las tarjetas de tickets --}}
    <div id="ajaxTicketsContent">
        <div class="text-center text-gray-400 py-12">
            <svg class="animate-spin h-7 w-7 mx-auto mb-2 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            Cargando tickets...
        </div>
    </div>

    {{-- MODAL DE DETALLE DE TICKET (SOLO EL NUEVO) --}}
    @include('admin.tickets.partials._modal_ticket_detalle_listado')

</div>
@endsection

@section('scripts')

<script>
// -- RESUMEN DE RIFA
function resumenRifa() {
    return {
        nombre: '',
        fecha: '',
        vendidos: 0,
        reservados: 0,
        abonados: 0,
        apartados: 0,          // Agregado
        total: 0,
        total_recaudado: 0,    // Agregado
        porcentaje: 0,
        rifaId: null,
        loading: false,
        async cargar(rifa_id) {
            if(!rifa_id) return;
            this.loading = true;
            try {
                let res = await fetch('/admin/rifas/' + rifa_id + '/resumen');
                let data = await res.json();
                this.nombre = data.nombre ?? '—';
                this.fecha = data.fecha ?? '—';
                this.vendidos = data.vendidos ?? 0;
                this.reservados = data.reservados ?? 0;
                this.abonados = data.abonados ?? 0;
                this.apartados = data.apartados ?? 0;               
                this.total = data.total ?? 0;
                this.total_recaudado = data.total_recaudado ?? 0;   
                this.porcentaje = data.progreso ?? 0;
            } catch(e) {
                this.nombre = '—';
                this.fecha = '—';
                this.vendidos = 0;
                this.reservados = 0;
                this.abonados = 0;
                this.apartados = 0;                 
                this.total = 0;
                this.total_recaudado = 0;           
                this.porcentaje = 0;
            }
            this.loading = false;
        },
        initResumen(rifa_id) {
            this.rifaId = rifa_id;
            this.cargar(rifa_id);
        }
    };
}


document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('buscadorTickets');
    const searchInput = document.getElementById('searchInput');
    const estadoInput = document.getElementById('estadoInput');
    const ajaxContent = document.getElementById('ajaxTicketsContent');
    const rifaInput = document.getElementById('rifaInput');
    const rifaCards = document.querySelectorAll('.rifa-card');

    if (!rifaInput.value && rifaCards.length > 0) {
        rifaInput.value = rifaCards[0].dataset.rifaId;
        rifaCards[0].classList.add('border-primary', 'ring-2');
    }

    if (window.Alpine && !Alpine.$data) {
        Alpine.$data = (el) => el.__x ? el.__x.$data : null;
    }

    rifaCards.forEach(card => {
        card.addEventListener('click', function() {
            rifaCards.forEach(c => c.classList.remove('border-primary', 'ring-2', 'bg-primary/10', 'shadow-lg'));
            this.classList.add('border-primary', 'ring-2', 'bg-primary/10', 'shadow-lg');

            rifaInput.value = this.dataset.rifaId;
            searchInput.value = '';
            estadoInput.value = '';
            let resumen = document.getElementById('rifaResumenWrapper');
            if (window.Alpine && Alpine.$data(resumen)) {
                Alpine.$data(resumen).initResumen(this.dataset.rifaId);
            }
            form.dispatchEvent(new Event('submit'));
        });
    });

    const urlParams = new URLSearchParams(window.location.search);
    searchInput.value = urlParams.get('q') ?? '';
    estadoInput.value = urlParams.get('estado') ?? '';
    if(urlParams.get('rifa_id')) {
        rifaInput.value = urlParams.get('rifa_id');
        rifaCards.forEach(card => {
            if(card.dataset.rifaId == rifaInput.value) {
                card.classList.add('border-primary', 'ring-2');
            } else {
                card.classList.remove('border-primary', 'ring-2');
            }
        });
    }

    function fetchTickets() {
        const formData = new FormData(form);
        let params = new URLSearchParams(formData).toString();
        ajaxContent.innerHTML = `<div class="text-center text-gray-400 py-12">
            <svg class="animate-spin h-7 w-7 mx-auto mb-2 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            Cargando tickets...
        </div>`;
        fetch('{{ route('admin.tickets.ajax') }}?' + params, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            ajaxContent.innerHTML = html;
        })
        .catch(e => {
            ajaxContent.innerHTML = '<div class="text-red-500 text-center py-8">Error al cargar tickets</div>';
        });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        fetchTickets();
        const params = new URLSearchParams(new FormData(form)).toString();
        window.history.replaceState({}, '', params ? `?${params}` : window.location.pathname);
    });

    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchTickets();
            const params = new URLSearchParams(new FormData(form)).toString();
            window.history.replaceState({}, '', params ? `?${params}` : window.location.pathname);
        }, 350);
    });

    fetchTickets();
});
</script>
@endsection
