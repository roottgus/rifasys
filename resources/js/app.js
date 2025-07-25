import '../css/app.css';
import Alpine from 'alpinejs';

// Exponer Alpine globalmente
window.Alpine = Alpine;

// -------------------------
// 1. Componentes Alpine locales
// -------------------------

// Modal participantes de rifa principal
Alpine.data('rifaModal', () => ({
  openModal: false,
  list: [],
  cliente: null,
  loading: false,
  error: null,

  open(rifaId) {
    this.openModal = true;
    this.loading   = true;
    this.error     = null;
    this.cliente   = null;
    this.list      = [];

    fetch(`/admin/rifas/${rifaId}/participantes`)
      .then(res => {
        if (!res.ok) throw new Error('Error consultando participantes');
        return res.json();
      })
      .then(data => {
        this.list = data;
      })
      .catch(() => {
        this.error = 'Error cargando participantes';
      })
      .finally(() => {
        this.loading = false;
      });
  },

  select(item) {
    this.cliente = item.cliente;
  },

  close() {
    this.openModal = false;
  }
}));

// Modal para premios especiales (detalle individual de cada premio)
window.premioModal = function(premioId) {
  return {
    openModal: false,
    list: [],
    loading: false,
    error: null,
    numeroGanador: '',
    ganador: null,
    errorGanador: '',

    open() {
      this.openModal = true;
      this.loading   = true;
      this.error     = null;
      this.list      = [];

      fetch(`/admin/premios/${premioId}/participantes`)
        .then(res => {
          if (!res.ok) throw new Error('Error consultando participantes');
          return res.json();
        })
        .then(data => {
          this.list = data;
        })
        .catch(() => {
          this.error = 'Error cargando participantes';
        })
        .finally(() => {
          this.loading = false;
        });
    },

    close() {
      this.openModal = false;
    },

    confirmarGanador() {
      if (!this.numeroGanador) {
        this.errorGanador = 'Indica un número ganador';
        this.ganador = null;
        return;
      }
      this.errorGanador = '';
      this.ganador = null;

      fetch(`/admin/premios/${premioId}/confirmar-ganador`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ numero: this.numeroGanador }),
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          this.ganador = data.ganador;
        } else {
          this.errorGanador = data.message || 'No se pudo confirmar ganador';
        }
      })
      .catch(() => {
        this.errorGanador = 'Error confirmando ganador';
      });
    }
  };
};

// -------------------------
// 2. Stores globales Alpine
// -------------------------

import './stores/participantes';
import registerSalePage from './stores/salePage';
import modalVentaTicketStore from './stores/modalVentaTicket';
import premiosModal from './stores/premiosModal';
import modalTicketsClienteStore from './stores/modalTicketsCliente';
import { ticketDetailStore } from './stores/ticketDetailStore';

// NUEVO: Importa el store de loterías
import gestionLoterias from './stores/gestionLoterias';
window.gestionLoterias = gestionLoterias;

// Hacer premiosModal visible globalmente para Alpine
window.premiosModal = premiosModal;

// -------------------------
// 3. Inicializa Alpine y registra stores
// -------------------------

document.addEventListener('alpine:init', () => {
  // 1) Registro explícito de "salePage"
  Alpine.data('salePage', registerSalePage);

  // Registra data() locales y page-level stores
  registerSalePage();
  Alpine.data('modalVentaTicket', modalVentaTicketStore);
  Alpine.data('modalTicketsCliente', modalTicketsClienteStore);
  Alpine.data('ticketDetail', ticketDetailStore);
  Alpine.store('modalVentaTicket', modalVentaTicketStore());
});

// -------------------------
// 4. Arranca Alpine y SweetAlert2
// -------------------------

document.addEventListener('DOMContentLoaded', () => {
  Alpine.start();

  // Mensajes SweetAlert2 para .btn-eliminar
  import('./components/swal.js').then(({ confirmDelete }) => {
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
      btn.addEventListener('click', e => {
        e.preventDefault();
        confirmDelete().then(result => {
          if (result.isConfirmed) {
            btn.closest('form').submit();
          }
        });
      });
    });
  });
});
