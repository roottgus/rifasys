// resources/js/stores/mixins/ventaCoreStore.js
export function ventaCoreStore() {
  return {
    // ── Estado ──
    pickedTickets: [],
    currentSubtotal: 0,
currentTotal: 0,

    modalOpen: false,
    paso: 1,
    picked: null,
    padLen: 3,
    accion: 'vender',
    operaciones: [
      { value: 'vender', label: 'Venta Total', icon: 'fas fa-cash-register' },
      { value: 'apartado', label: 'Apartado', icon: 'fas fa-hourglass-half' },
      { value: 'abono', label: 'Abono Inicial', icon: 'fas fa-coins' }
    ],
    errorFecha: '',
    cliente: { id: null, nombre: '', cedula: '', email: '', telefono: '', direccion: '' },
    montoAbono: '',
    ventaExitosa: false,
    mensajeExito: '',
    accionRealizada: '',
    errorMensaje: '',
    abonoModo: null,
    ticketParaAbono: null,
    abonoEnCurso: false,

    init() {
      window.addEventListener('cliente-cargado', event => this.setCliente(event.detail));
    },

    resetAbono() {
      this.abonoEnCurso = false;
      this.montoAbono = '';
      this.abonoModo = null;
      this.ticketParaAbono = null;
    },

    setCliente(cliente) {
      this.cliente = {
        id: cliente.id || null,
        nombre: cliente.nombre || '',
        cedula: cliente.cedula || '',
        email: cliente.email || '',
        telefono: cliente.telefono || '',
        direccion: cliente.direccion || ''
      };
      this.descuentos.calcularDescuento(this.pickedTickets, this.cliente);
    },

    validarFechaPago() {
      this.errorFecha = '';
    },

    openVentaSeleccionados(ticketOrArray) {
      
      this.open(ticketOrArray);
    },

    // ── Getter de subtotal con logs exhaustivos ──
    get subtotal() {
      
      const sum = this.pickedTickets
        .map((t, idx) => {
          
          return t.precio_ticket;
        })
        .reduce((acc, p) => acc + p, 0);
      
      return sum;
    },

    get subtotalStr() {
      return this.subtotal.toFixed(2);
    },

    get montoDescuento() {
      const total = parseFloat(this.descuentos?.totalAPagar ?? 0) || 0;
      return Math.max(this.subtotal - total, 0);
    },

    get motivoDescuento() {
  return this.descuentos?.motivoDescuento || '';
},


    get totalAPagar() {
      // 1) Lo que viene del store de descuentos
      const backendTotal = parseFloat(this.descuentos?.totalAPagar ?? 0) || 0;
      // 2) Fallback: el subtotal que ya calculaste
      const fallback = this.currentSubtotal;
      
      // 3) Elegimos backendTotal si es > 0, si no usamos fallback
      const total = backendTotal > 0 ? backendTotal : fallback;
      return total.toFixed(2);
    },

    // Para compatibilidad
    getTotalAPagar() {
      return this.totalAPagar;
    },

    enrichTicketRifa(ticket) {
      if (!ticket.rifa && ticket.rifa_id && window.rifasData) {
        const rifa = window.rifasData.find(r => r.id == ticket.rifa_id);
        if (rifa) {
          ticket.rifa = rifa;
          ticket.precio_ticket = ticket.precio_ticket ?? rifa.precio;
        }
      }
      ticket.precio_ticket = parseFloat(ticket.precio_ticket) || 0;
      ticket.numero_ticket = ticket.numero_ticket || ticket.numero || '';
      return ticket;
    },

    addTicket(ticket) {
      ticket = this.enrichTicketRifa(ticket);
      if (!this.pickedTickets.some(t => t.id === ticket.id)) {
        this.pickedTickets.push(ticket);
        this.descuentos.calcularDescuento(this.pickedTickets, this.cliente);
      }
    },

    removeTicket(ticket) {
      this.pickedTickets = this.pickedTickets.filter(t => t.id !== ticket.id);
      this.descuentos.calcularDescuento(this.pickedTickets, this.cliente);
    },

    toggleTicket(ticket) {
      this.pickedTickets.some(t => t.id === ticket.id) ? this.removeTicket(ticket) : this.addTicket(ticket);
    },

    clearTickets() {
      this.pickedTickets = [];
      this.descuentos.calcularDescuento(this.pickedTickets, this.cliente);
    },

    open(ticketOrArray) {
      const arr = Array.isArray(ticketOrArray) ? ticketOrArray : [ticketOrArray];
      this.pickedTickets = arr.map(this.enrichTicketRifa);
      // ── NUEVO ──
  this.currentSubtotal = this.pickedTickets.reduce((sum, t) => sum + t.precio_ticket, 0);
  // ──────────
  // como no hay descuento aún, el total = subtotal
  this.currentTotal = this.currentSubtotal;
      this.picked = this.pickedTickets[0];
      this.modalOpen = true;
      this.paso = 1;
      this.accion = 'vender';
      this.resetCliente?.();
      this.ventaExitosa = false;
      this.mensajeExito = '';
      this.errorMensaje = '';
      this.resetAbono();
      this.descuentos.calcularDescuento(this.pickedTickets, this.cliente);
      this.iniciarAbono?.();
      document.body.style.overflow = 'hidden';
      
    },

    closeModal() {
      this.modalOpen = false;
      this.pickedTickets = [];
      this.picked = null;
      this.ventaExitosa = false;
      this.mensajeExito = '';
      this.errorMensaje = '';
      this.resetAbono();
      Object.assign(this.descuentos, {
        descuento: 0,
        precioConDescuento: 0,
        subtotal: 0,
        montoDescuento: 0,
        totalAPagar: 0,
        motivoDescuento: '',
        cargandoDescuento: false
      });
      this.iniciarAbono?.();
      document.body.style.overflow = '';
    },

    clickVentaTotal() {
      
     this.accion = 'vender';
      this.errorMensaje = '';
      this.resetAbono();
      if (!this.camposRequeridosCompletos()) {
        this.errorMensaje = 'Debes completar y validar todos los campos obligatorios antes de continuar.';
        return;
      }
      this.currentSubtotal = this.pickedTickets.reduce((sum, t) => sum + t.precio_ticket, 0);
  // si la API de descuentos ya cargó un total > 0, úsalo; si no, subtotal
  const backendTotal = parseFloat(this.descuentos?.totalAPagar ?? 0) || 0;
  this.currentTotal = backendTotal > 0 ? backendTotal : this.currentSubtotal;
  this.paso = 2;
},

    clickApartado() {
      this.accion = 'apartado';
      this.errorMensaje = '';
      this.resetAbono();
      if (!this.camposRequeridosCompletos()) {
        this.errorMensaje = 'Debes completar y validar todos los campos obligatorios antes de continuar.';
        return;
      }
      this.procesarVenta();
    },

    clickAbonoInicial() {
      if (this.abonoEnCurso) {
        if (this.pickedTickets.length > 1 && !this.abonoModo) {
          this.errorMensaje = 'Debes elegir si el abono es GLOBAL o POR TICKET.';
          return;
        }
        if (this.pickedTickets.length === 1) {
          this.abonoModo = 'ticket';
          this.ticketParaAbono = this.pickedTickets[0].id;
        }
        if (this.abonoModo === 'ticket' && !this.ticketParaAbono) {
          this.errorMensaje = 'Selecciona el ticket al que aplicarás el abono.';
          return;
        }
        if (!this.montoAbono || Number(this.montoAbono) <= 0) {
          this.errorMensaje = 'Debes ingresar un monto de abono válido.';
          return;
        }
        if (!this.camposRequeridosCompletos()) {
          this.errorMensaje = 'Debes completar y validar todos los campos obligatorios antes de continuar.';
          return;
        }
        this.accion = 'abono';
        this.errorMensaje = '';
        this.abonoEnCurso = false;
        this.paso = 2;
        return;
      }
      this.accion = 'abono';
      this.errorMensaje = '';
      this.abonoEnCurso = true;
    },
    // ── Procesar venta (igual que antes) ──
    async procesarVenta() {
      // Apartado (sin método de pago)
      if (this.accion === 'apartado') {
        const ticket_ids = Array.isArray(this.picked)
          ? this.picked.map(t => t.id)
          : [this.picked.id];
        const payload = {
          ticket_ids,
          accion: this.accion,
          cliente: this.cliente
        };
        try {
          const res = await this.api.call('/admin/tickets/procesar-venta', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
          });
          if (!res.ok) throw new Error(`HTTP ${res.status}`);
          const data = await res.json();
          if (data.success) {
            this.picked = data.ticket;
            this.ventaExitosa = true;
            this.accionRealizada = this.accion;
            this.mensajeExito = '¡Apartado realizado exitosamente!';
            this.paso = 3;
            window.dispatchEvent(new Event('tickets:reload'));
          } else {
            this.errorPago = data.mensaje || 'Error al procesar apartado';
          }
        } catch (e) {
          console.error('Error apartado:', e);
          this.errorPago = 'Error de red o servidor al apartar';
        }
        return;
      }

      // Validaciones SOLO en paso 2
      if (this.paso === 2) {
        this.validarFechaPago();
        if (this.errorFecha) {
          this.errorPago = this.errorFecha;
          return;
        }
        if (this.errorReferencia || this.referenciaVerificando) {
          this.errorPago = this.errorReferencia || 'Esperando validación de referencia...';
          return;
        }
        // Validación método pago
        const detallesMetodo = this.obtenerDetallesMetodo?.() || {};
        if (!detallesMetodo.fields || !Array.isArray(detallesMetodo.fields)) {
          this.errorPago = 'Debes seleccionar un método de pago válido.';
          return;
        }
      }

      // Validación avanzada cliente
      if (
        this.validacion?.cedula === false ||
        this.validacion?.email === false ||
        this.validacion?.telefono === false ||
        this.validacion?.conflicto?.cedula ||
        this.validacion?.conflicto?.email ||
        this.validacion?.conflicto?.telefono
      ) {
        this.errorMensaje = 'Revisa los campos destacados, corrige antes de continuar.';
        return;
      }

      // Payload y envío
      const ticket_ids = Array.isArray(this.picked)
        ? this.picked.map(t => t.id)
        : [this.picked.id];
      const detallesMetodo = this.obtenerDetallesMetodo?.() || {};
      const cuentaDestino = {};
      if (this.paso === 2 && detallesMetodo.fields && Array.isArray(detallesMetodo.fields)) {
        detallesMetodo.fields.forEach(f => (cuentaDestino[f.key] = f.value));
      }
      const payload = {
        ticket_ids,
        accion: this.accion,
        cliente: this.cliente,
        monto_abono: this.montoAbono,
        abono_global: this.accion === 'abono' ? this.abonoModo === 'global' : undefined,
        ticket_id:
          this.accion === 'abono' && this.abonoModo === 'ticket'
            ? this.ticketParaAbono
            : undefined,
        metodo_pago: this.metodoPago,
        pago_datos: this.pagoDatos,
        cuenta_admin_destino: cuentaDestino,
        total_pago: this.getTotalAPagar()
      };

      try {
        const res = await this.api.call('/admin/tickets/procesar-venta', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify(payload)
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();
        if (data.success) {
          this.picked = data.ticket;
          this.ventaExitosa = true;
          this.accionRealizada = this.accion;
          this.mensajeExito =
            this.accion === 'vender'
              ? '¡Venta realizada exitosamente!'
              : this.accion === 'abono'
                ? '¡Abono inicial registrado correctamente!'
                : data.mensaje || 'Operación realizada correctamente';
          this.paso = 3;
          window.dispatchEvent(new Event('tickets:reload'));
        } else {
          if (data.campo && ['cedula', 'email', 'telefono'].includes(data.campo)) {
            this.validacion[data.campo] = false;
            this.validacion.mensaje[data.campo] = data.mensaje;
            this.validacion.conflicto[data.campo] = true;
          } else {
            this.errorPago = data.mensaje || 'Error al procesar venta';
          }
        }
      } catch (e) {
        console.error('Error procesarVenta():', e);
        this.errorPago = 'Error de red o servidor';
      }
    },
  };
}
