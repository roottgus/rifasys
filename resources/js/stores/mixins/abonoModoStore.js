// resources/js/stores/mixins/abonoModoStore.js
export function abonoModoStore() {
  return {
    // 'global' | 'ticket' | null
    abonoModo: null,
    // ID del ticket seleccionado en modo 'ticket'
    ticketParaAbono: null,

    iniciarAbono() {
  
  this.abonoModo = null;
  this.ticketParaAbono = null;
},

    // Fija el modo de aplicación del abono
    elegirAbonoModo(modo) {
      this.abonoModo = modo;
      if (modo === 'ticket') {
        // Seleccionar por defecto el primer ticket disponible
        this.ticketParaAbono = this.pickedTickets[0]?.id || null;
      } else {
        this.ticketParaAbono = null;
      }
    }
  };
}
