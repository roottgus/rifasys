// resources/js/stores/descuentoStore.js
export default function descuentoStore() {
  return {
    // ── Estado reactivo inicial ──
    descuento: 0,               // % de descuento aplicado
    precioConDescuento: 0,      // precio unitario luego de aplicar %
    subtotal: 0,                // subtotal antes de descuento
    montoDescuento: 0,          // monto descontado
    totalAPagar: 0,             // total final con descuento
    motivoDescuento: '',        // texto promocional
    cargandoDescuento: false,   // indicador de carga

    /**
     * Llama al backend y actualiza todos los valores.
     *
     * @param {Array} pickedTickets — Tickets seleccionados
     * @param {Object|null} cliente  — Cliente actual
     */
    async calcularDescuento(pickedTickets, cliente) {
  
  this.cargandoDescuento = true;

      // Resetear todos los valores
      this.descuento          = 0;
      this.precioConDescuento = 0;
      this.subtotal           = 0;
      this.montoDescuento     = 0;
      this.totalAPagar        = 0;
      this.motivoDescuento    = '';

      // Si no hay tickets seleccionados, salimos
      if (!pickedTickets.length) {
        this.cargandoDescuento = false;
        return;
      }

      // Extraemos el rifaId del primer ticket
      const rifaId = pickedTickets[0].rifa_id;
      if (!rifaId) {
        this.cargandoDescuento = false;
        return;
      }

      // Preparamos el payload, siempre incluyendo cliente_id (puede ser null)
      const body = {
        rifa_id:   rifaId,
        cantidad:  pickedTickets.length,
        cliente_id: cliente?.id ?? null
      };
      
      try {
        const res = await fetch(window.Rutas.obtenerDescuento, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify(body)
        });

        if (!res.ok) {
          throw new Error('Error obteniendo descuento');
        }

        const data = await res.json();

        // Asignamos resultados al store
        this.descuento          = Number(data.descuento      ?? data.porcentaje) || 0;
        this.subtotal           = Number(data.subtotal)                           || 0;
        this.montoDescuento     = Number(data.monto_descuento)                   || 0;
        this.totalAPagar        = Number(data.total_final)                       || 0;
        this.precioConDescuento = this.totalAPagar / pickedTickets.length;
        this.motivoDescuento    = data.motivo                                 || '';
      } catch (e) {
        console.error('descuentoStore error:', e);
      } finally {
        this.cargandoDescuento = false;
      }
    },

    /** Retorna el total con dos decimales */
    getTotalAPagar() {
      return Number(this.totalAPagar).toFixed(2);
    }
  };
}
