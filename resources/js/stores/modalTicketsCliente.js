// resources/js/stores/modalTicketsCliente.js

export default function modalTicketsClienteStore() {
    return {
        // Estado para el modal (store modular)
        modalOpen: false,
        cliente: null,
        tickets: [],
        loading: false,
        error: null,
        padLength: 3, // Valor por defecto, se sobreescribe al cargar tickets

        // ---- Para máxima compatibilidad con Alpine local ----
        clienteTickets: null,
        ticketsLoading: false,

        // Abre el modal y carga tickets por AJAX
        open(clienteId) {
            this.modalOpen = true;
            this.loading = true;
            this.ticketsLoading = true;
            this.error = null;
            this.tickets = [];
            this.cliente = null;
            this.clienteTickets = null;
            this.padLength = 3;

            fetch(`/admin/clientes/${clienteId}/tickets-ajax`)
                .then(res => {
                    if (!res.ok) throw new Error('No se pudo cargar la información');
                    return res.json();
                })
                .then(data => {
                    this.cliente = data.cliente;
                    this.clienteTickets = data.cliente; // Compatibilidad
                    this.tickets = data.tickets;
                    this.padLength = data.pad_length || 3;
                })
                .catch(e => {
                    this.error = e.message || 'Error desconocido';
                })
                .finally(() => {
                    this.loading = false;
                    this.ticketsLoading = false;
                });
        },
        // Cierra el modal y limpia estado
        close() {
            this.modalOpen = false;
            this.tickets = [];
            this.cliente = null;
            this.clienteTickets = null;
            this.error = null;
            this.loading = false;
            this.ticketsLoading = false;
            this.padLength = 3;
        }
    }
}
