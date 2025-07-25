import Alpine from 'alpinejs';
import { estadosExportar, filtroNombre } from '../helpers/tickets';
import { exportarTicketsPDF } from '../helpers/exportPDF';
import { imprimirTickets } from '../helpers/printTickets';
import descuentoStore from '../stores/descuentoStore';

// Normaliza ticket (por si el backend te devuelve anidado)
function normalizaTicket(rawTicket) {
  if (rawTicket && typeof rawTicket.qr_code !== "undefined") {
    return rawTicket;
  }
  if (rawTicket && rawTicket.ticket) {
    return rawTicket.ticket;
  }
  return null;
}

export default function registerSalePage() {
  Alpine.data('salePage', () => ({

    rifas: window.rifasData || [],
    selectedRifa: null,
    tickets: [],
    padLen: 2,
    loading: false,
    error: null,

    filter: 'all',
    exportMenu: false,
    estadosExportar,
    filtroNombre,

    // --- Modal state ---
    modalOpen: false,
    picked: null,
    cliente: '',
    montoAbono: '',
    accion: 'vender', // 'vender', 'apartado', 'abono'
    ventaExitosa: false,
    mensajeExito: '',
    premiosModalOpen: false,
    gridLimit: 1000,

    // Búsqueda profesional
    searchGlobal: '',

    showToast: false,

    // --- Selección múltiple ---
    selectedTickets: [],

    // --- Store de descuentos ---
    descuentos: descuentoStore(),

    // --- Getters para UI de descuento ---
    get subtotal()        { return this.descuentos.subtotal ?? 0; },
    get descuento()       { return this.descuentos.descuento ?? 0; },
    get montoDescuento()  { return this.descuentos.montoDescuento ?? 0; },
    get motivoDescuento() { return this.descuentos.motivoDescuento ?? ''; },
    get totalAPagar()     { return this.descuentos.getTotalAPagar(); },

    cargarMas() { this.gridLimit += 1000; },

    get ticketPdfUrl() {
      return this.picked ? `/admin/tickets/${this.picked.id}/pdf` : '#';
    },

    get linkVerificacion() {
      return this.picked
        ? `${window.location.origin}/tickets/verificar/${this.picked.uuid}`
        : '';
    },

    // Total a pagar selección múltiple (sin descuento)
    get totalSeleccionados() {
      if (!this.selectedTickets.length) return 0;
      return this.selectedTickets.reduce((acc, t) => acc + Number(t.precio_ticket || 0), 0);
    },


    // Toggle selección
    toggleTicketSel(ticket) {
      if (ticket.estado !== 'disponible') return;
      const idx = this.selectedTickets.findIndex(t => t.id === ticket.id);
      if (idx >= 0) this.selectedTickets.splice(idx, 1);
      else this.selectedTickets.push(ticket);
    },
    clearSelectedTickets() {
      this.selectedTickets = [];
    },

    // Venta múltiple
venderSeleccionados() {
  if (!this.selectedTickets.length) return;
  this.picked = [...this.selectedTickets];
  this.descuentos.calcularDescuento(this.picked, this.cliente);
  window.dispatchEvent(new CustomEvent('open-venta-ticket', {
    detail: this.picked
  }));
},

// Venta individual
abrirVentaTicketIndividual(ticket) {
  this.picked = [ticket];
  this.descuentos.calcularDescuento(this.picked, this.cliente);
  window.dispatchEvent(new CustomEvent('open-venta-ticket', {
    detail: this.picked
  }));
},


    async exportarPDF(filtro = this.filter) {
      await exportarTicketsPDF({
        rifa: this.getRifa(),
        tickets: this.filteredTickets,
        filtro,
        padLen: this.padLen,
        logoUrl: window.logoEmpresaUrl || '/images/logo.png'
      });
      this.exportMenu = false;
    },

    imprimirGrid(filtro = this.filter) {
      imprimirTickets({
        rifa: this.getRifa(),
        tickets: this.filteredTickets,
        filtro,
        padLen: this.padLen,
        logoUrl: window.logoEmpresaUrl || '/images/logo.png'
      });
      this.exportMenu = false;
    },

     // --- Flujo de inicialización ---
    init() {
      this.selectedRifa = this.rifas[0]?.id || null;
      if (this.selectedRifa) this.loadTickets();
      window.addEventListener('tickets:reload', () => this.loadTickets());
      window.addEventListener('open-venta-ticket', (e) => {
        this.picked = e.detail;
        this.descuentos.calcularDescuento(this.picked, this.cliente);
        this.modalOpen = true;
        document.body.style.overflow = 'hidden';
      });
    },
    // ------------- FIN DE FLUJO MODERNO --------------

     async onChangeRifa() {
      await this.loadTickets();
      this.clearSelectedTickets();
    },

    openPremiosModal()   { this.premiosModalOpen = true; document.body.style.overflow = 'hidden'; },
    closePremiosModal()  { this.premiosModalOpen = false; document.body.style.overflow = ''; },

    getRifa() {
      return this.rifas.find(r => r.id == this.selectedRifa) || null;
    },
    getRifaNombre() {
      const r = this.getRifa();
      return r?.nombre || '--';
    },
    getRifaPrecio() {
      const r = this.getRifa();
      return r && r.precio ? parseFloat(r.precio).toFixed(2) : '0.00';
    },
    getRifaFechaSorteo() {
      const r = this.getRifa();
      if (!r?.fecha_sorteo) return '--';
      const d = new Date(r.fecha_sorteo);
      return isNaN(d) ? '--'
        : d.toLocaleDateString('es-ES', { day:'2-digit', month:'short', year:'numeric' });
    },
    getRifaHoraSorteo() {
      const r = this.getRifa();
      if (!r?.hora_sorteo) return '';
      return r.hora_sorteo.slice(0,5);
    },

    getRifaLoteria() {
      const r = this.getRifa();
      if (!r) return '--';
      if (r.loteria && typeof r.loteria === 'object' && r.loteria.nombre) return r.loteria.nombre;
      if (typeof r.loteria === 'string' && r.loteria) return r.loteria;
      return '--';
    },
    getRifaTipoLoteria() {
      const r = this.getRifa();
      if (!r) return '--';
      if (r.tipo_loteria && typeof r.tipo_loteria === 'object' && r.tipo_loteria.nombre) return r.tipo_loteria.nombre;
      if (typeof r.tipo_loteria === 'string' && r.tipo_loteria) return r.tipo_loteria;
      return '--';
    },

    formatSorteo(rifa) {
      if (!rifa) return '--';
      let fechaText = '--';
      if (rifa.fecha_sorteo) {
        const partes = rifa.fecha_sorteo.split('-');
        if (partes.length === 3) {
          const d = new Date(`${rifa.fecha_sorteo}T00:00:00`);
          fechaText = d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
        }
      }
      let horaText = '';
      if (rifa.hora_sorteo) {
        horaText = rifa.hora_sorteo.substring(0,5);
        if (horaText && horaText !== '00:00') horaText = ' · ' + horaText;
        else horaText = '';
      }
      return fechaText + horaText;
    },

    getPremiosEspeciales() {
      const r = this.getRifa();
      return r && Array.isArray(r.premios_especiales) ? r.premios_especiales : [];
    },
    countPremios() {
      return this.getPremiosEspeciales().length;
    },

    formatPremioFecha(fecha, hora) {
      let out = '';
      if (fecha) {
        const f = new Date(fecha);
        out += f.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
      }
      if (hora) {
        let h = hora;
        if (h.length > 5) {
          const match = h.match(/(\d{2}:\d{2})/);
          h = match ? match[1] : h.substring(0,5);
        } else {
          h = h.substring(0,5);
        }
        out += (out ? ' · ' : '') + h;
      }
      return out;
    },

    // --- ARREGLADO: todos retornan un número ---
    countDisponibles() {
      return this.tickets.filter(t => t.estado === 'disponible').length;
    },
    countVendidos() {
      return this.tickets.filter(t => t.estado === 'vendido').length;
    },
    countApartados() {
      return this.tickets.filter(t => t.estado === 'reservado').length;
    },
    countAbonados() {
      // Si el ticket tiene estado abonado o tiene abono, cuenta
      return this.tickets.filter(t => t.estado === 'abonado' || (t.total_abonado || 0) > 0).length;
    },
    totalAbonos() {
      return this.tickets
        .filter(t => t.estado === 'abonado')
        .reduce((acc, t) => acc + (t.total_abonado || 0), 0);
    },

    get filteredTickets() {
      let filtered = this.tickets;
      switch (this.filter) {
        case 'disponible': filtered = filtered.filter(t => t.estado === 'disponible'); break;
        case 'vendido':    filtered = filtered.filter(t => t.estado === 'vendido'); break;
        case 'reservado':  filtered = filtered.filter(t => t.estado === 'reservado'); break;
        case 'abonado':    filtered = filtered.filter(t => t.estado === 'abonado'); break;
      }
      const q = (this.searchGlobal || '').toLowerCase().trim();
      if (q) {
        filtered = filtered.filter(t => {
          const matchNum = String(t.numero).padStart(this.padLen, '0').includes(q) || String(t.numero).includes(q);
          const nombre = (t.cliente_nombre || t.cliente || '').toLowerCase();
          const matchNombre = nombre.includes(q);
          const cedula = (t.cliente_cedula || t.cedula || '').toLowerCase();
          const matchCedula = cedula.includes(q);
          return matchNum || matchNombre || matchCedula;
        });
      }
      return filtered.slice(0, this.gridLimit);
    },

    async loadTickets() {
      if (!this.selectedRifa) { this.tickets = []; return; }
      const r = this.getRifa();
      this.padLen = r?.cantidad_numeros
        ? Math.max(String(r.cantidad_numeros-1).length, 2)
        : 2;
      this.loading = true; this.error = null;
      try {
        const res = await fetch(`/admin/rifas/${this.selectedRifa}/tickets/json`);
        if (!res.ok) throw new Error('Error al cargar números');
        this.tickets = await res.json();
        this.filter = 'all'; this.gridLimit = 1000;
        this.showToast = true;
        setTimeout(() => this.showToast = false, 1700);
      } catch (e) {
        this.error = e.message;
      } finally {
        this.loading = false;
      }
    },

    // Cambiado: selección múltiple
    selectNumber(t) {
      this.toggleTicketSel(t);
    },

    // NUEVO: vender seleccionados desde botón especial
    venderSeleccionadosBtn() {
      this.venderSeleccionados();
    },

   closeModal() {
      this.modalOpen = false;
      this.ventaExitosa = false;
      this.picked = null;
      document.body.style.overflow = '';
      this.clearSelectedTickets();
      // Limpia descuentos
      this.descuentos.subtotal = 0;
      this.descuentos.descuento = 0;
      this.descuentos.montoDescuento = 0;
      this.descuentos.motivoDescuento = '';
    },

    // --- Acciones del modal (sale/apartado/abono) ---
    async confirmSale() {
      const res = await fetch(`/admin/tickets/${this.picked.id}/sell`, {
        method: 'POST',
        headers: {
          'Content-Type':'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ cliente: this.cliente }),
      });
      if (res.ok) {
        const data = await res.json();
        this.picked = normalizaTicket(data.ticket || data);
        this.ventaExitosa = true;
        this.mensajeExito = "¡Venta realizada!";
        window.dispatchEvent(new CustomEvent('tickets:ultimo-vendido', {
          detail:{ numero:this.picked.numero, operacion:'venta', monto:this.getRifaPrecio(), rifa_id:this.selectedRifa }
        }));
        window.dispatchEvent(new Event('tickets:reload'));
      }
    },

    async confirmApartado() {
      const res = await fetch(`/admin/tickets/${this.picked.id}/reserve`, {
        method: 'POST',
        headers: {
          'Content-Type':'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ cliente: this.cliente }),
      });
      if (res.ok) {
        const data = await res.json();
        this.picked = normalizaTicket(data.ticket || data);
        this.ventaExitosa = true;
        this.mensajeExito = "¡Ticket apartado!";
        window.dispatchEvent(new CustomEvent('tickets:ultimo-vendido', {
          detail:{ numero:this.picked.numero, operacion:'apartado', monto:this.getRifaPrecio(), rifa_id:this.selectedRifa }
        }));
        window.dispatchEvent(new Event('tickets:reload'));
      }
    },

    // ---- MODAL DE DETALLE DE TICKET ----
    detalleModalOpen: false,
    detalleTicketData: null,
    detalleTicketLoading: false,
    detalleTicketError: null,

    async openTicketDetail(ticket) {
      this.detalleModalOpen = true;
      this.detalleTicketLoading = true;
      this.detalleTicketError = null;
      document.body.style.overflow = 'hidden';
      try {
        const res = await fetch(`/admin/tickets/${ticket.id}/detalle-json`);
        if (!res.ok) throw new Error('Error al cargar el detalle del ticket');
        this.detalleTicketData = await res.json();
      } catch (e) {
        this.detalleTicketError = e.message;
      } finally {
        this.detalleTicketLoading = false;
      }
    },
    closeDetalleModal() {
      this.detalleModalOpen = false;
      this.detalleTicketData = null;
      this.detalleTicketError = null;
      document.body.style.overflow = '';
    },

    async confirmAbono() {
      const res = await fetch(`/admin/abonos`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
          ticket_id: this.picked.id,
          monto: this.montoAbono,
          cliente: this.cliente
        }),
      });
      if (res.ok) {
        const data = await res.json();
        this.picked = normalizaTicket(data.ticket || data);
        this.ventaExitosa = true;
        this.mensajeExito = "¡Abono registrado!";
        window.dispatchEvent(new CustomEvent('tickets:ultimo-vendido', {
          detail: {
            numero: this.picked.numero,
            operacion: 'abono',
            monto: this.montoAbono,
            rifa_id: this.selectedRifa,
          }
        }));
        window.dispatchEvent(new Event('tickets:reload'));
      }
    }
  }));
}
