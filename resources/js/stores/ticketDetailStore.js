// resources/js/stores/ticketDetailStore.js

import { useApi } from '../helpers/api';

export function ticketDetailStore() {
  return {
    api: useApi(),

    // Estado del detalle
    detalleModalOpen: false,
    detalleTicketData: null,
    detalleTicketLoading: false,
    detalleTicketError: false,

    /**
     * Abre el modal de detalle para un ticket específico
     * y carga sus datos desde el backend.
     * @param {number} ticketId
     */
    openDetalle(ticketId) {
      this.detalleModalOpen = true;
      this.detalleTicketLoading = true;
      this.detalleTicketError = false;
      this.detalleTicketData = null;

      this.api.call(`/admin/tickets/${ticketId}/detalle-json`)
        .then(res => {
          if (!res.ok) throw new Error(`HTTP ${res.status}`);
          return res.json();
        })
        .then(data => {
          this.detalleTicketData = data;
        })
        .catch(err => {
          console.error('Error cargando detalle de ticket:', err);
          this.detalleTicketError = 'No se pudo cargar el detalle.';
        })
        .finally(() => {
          this.detalleTicketLoading = false;
        });
    },

    /**
     * Cierra el modal de detalle y resetea el estado.
     */
    closeDetalleModal() {
      this.detalleModalOpen = false;
      this.detalleTicketData = null;
      this.detalleTicketError = false;
      this.detalleTicketLoading = false;
    },

    /**
     * Formatea un número de ticket para que siempre tenga 3 dígitos (001, 054, etc.).
     * @param {number|null|undefined} num
     * @returns {string}
     */
    padNum(num) {
      if (num == null) return '—';
      return String(num).padStart(3, '0');
    },

    /**
     * Devuelve el icono HTML correspondiente al método de pago.
     * @param {string} metodo
     * @returns {string}
     */
    iconoMetodoPago(metodo) {
      const iconos = {
        pago_movil: '<i class="fa-solid fa-mobile-alt text-orange-500 text-lg"></i>',
        zelle:     `<img src="/images/zelle.svg" alt="Zelle" class="w-7 h-7 inline" />`,
        tran_bancaria_nacional: '<i class="fa-solid fa-university text-indigo-600 text-lg"></i>',
        pago_efectivo: '<i class="fa-solid fa-money-bill-wave text-green-600 text-lg"></i>',
      };
      return iconos[metodo] || '<i class="fa-solid fa-credit-card text-gray-500 text-lg"></i>';
    },

    /**
     * Devuelve el nombre legible para un método de pago.
     * @param {string} metodo
     * @returns {string}
     */
    nombreMetodoPago(metodo) {
      const nombres = {
        pago_movil: 'Pago Móvil',
        zelle: 'Zelle',
        tran_bancaria_nacional: 'Transferencia Nacional',
        pago_efectivo: 'Efectivo',
      };
      return nombres[metodo] || metodo;
    },
  };
}
