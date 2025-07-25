@extends('layouts.admin')

@section('title', 'Clientes')

@section('content')
<div x-data="clientesPage()" class="p-6 space-y-6">
    @include('admin.clientes.partials._search')
    @include('admin.clientes.partials._table')
    @include('admin.clientes.partials._modal-form')
    @include('admin.clientes.partials._modal-tickets')
</div>
@endsection

@push('scripts')
<script>
// Store Alpine para clientes + modal tickets
function clientesPage() {
    return {
        // --- Modal de Cliente ---
        modalOpen: false,
        editMode: false,
        clienteId: null,
        nombre: '', cedula: '', email: '', telefono: '', direccion: '',
        mensajeCedula: '', mensajeEmail: '', mensajeTelefono: '',
        errors: {},
        loading: false,

        // --- Modal Tickets ---
        modalTicketsOpen: false,
        ticketsLoading: false,
        tickets: [],
        clienteTickets: null,
        // --- Profesional: compatibilidad con store modular ---
        cliente: null, // <= Así puedes importar un store o abrir el modal desde otra parte y nunca tendrás ReferenceError

        openModal(cliente = null) {
            this.editMode = !!cliente;
            this.clienteId = cliente?.id || null;
            this.nombre = cliente?.nombre || '';
            this.cedula = cliente?.cedula || '';
            this.email = cliente?.email || '';
            this.telefono = cliente?.telefono || '';
            this.direccion = cliente?.direccion || '';
            this.errors = {};
            this.mensajeCedula = '';
            this.mensajeEmail = '';
            this.mensajeTelefono = '';
            this.modalOpen = true;
        },
        closeModal() {
            this.modalOpen = false;
        },

        buscarCedula() {
            if (!this.cedula) return;
            fetch('/admin/clientes/validar-campo', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ campo: 'cedula', valor: this.cedula }),
            })
            .then(r => r.json()).then(res => {
                this.mensajeCedula = res.message;
                if (res.exists && res.cliente) {
                    this.nombre = res.cliente.nombre || '';
                    this.email = res.cliente.email || '';
                    this.telefono = res.cliente.telefono || '';
                    this.direccion = res.cliente.direccion || '';
                }
            });
        },
        validarEmail() {
            if (!this.email) return;
            fetch('/admin/clientes/validar-campo', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ campo: 'email', valor: this.email, cedula: this.cedula }),
            })
            .then(r => r.json()).then(res => {
                this.mensajeEmail = res.conflicto ? res.message : '';
            });
        },
        validarTelefono() {
            if (!this.telefono) return;
            fetch('/admin/clientes/validar-campo', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ campo: 'telefono', valor: this.telefono, cedula: this.cedula }),
            })
            .then(r => r.json()).then(res => {
                this.mensajeTelefono = res.conflicto ? res.message : '';
            });
        },
        submit() {
            this.loading = true;
            this.errors = {};
            fetch(this.editMode ? `/admin/clientes/${this.clienteId}` : '/admin/clientes', {
                method: this.editMode ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    nombre: this.nombre,
                    cedula: this.cedula,
                    email: this.email,
                    telefono: this.telefono,
                    direccion: this.direccion,
                })
            })
            .then(async r => {
                this.loading = false;
                if (r.status === 422) {
                    this.errors = await r.json();
                } else if (r.ok) {
                    window.location.reload(); // Refresca la lista al guardar
                }
            });
        },

        // --------- Tickets de Cliente (AJAX Modal) ----------
        abrirModalTickets(cliente) {
            this.ticketsLoading = true;
            this.tickets = [];
            this.clienteTickets = null;
            // --- Limpia variable store para máxima compatibilidad ---
            this.cliente = null;

            this.modalTicketsOpen = true;
            fetch(`/admin/clientes/${cliente.id}/tickets-ajax`)
                .then(r => {
                    if (!r.ok) throw new Error('No se pudo cargar la información');
                    return r.json();
                })
                .then(res => {
                    this.tickets = res.tickets;
                    this.clienteTickets = res.cliente;
                    // --- Opcional: también rellena la variable store si la usas desde fuera
                    this.cliente = res.cliente;
                })
                .catch(e => {
                    this.tickets = [];
                    this.clienteTickets = null;
                    this.cliente = null;
                    alert(e.message || 'Error cargando tickets del cliente');
                })
                .finally(() => {
                    this.ticketsLoading = false;
                });
        },

        cerrarModalTickets() {
            this.modalTicketsOpen = false;
            this.tickets = [];
            this.clienteTickets = null;
            this.cliente = null;
        },
    }
}
</script>
@endpush
