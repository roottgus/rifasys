// resources/js/stores/participantes.js

import { useApi, confirmWinner, createModalHandlers } from '../helpers/api';
import Alpine from 'alpinejs';

const api = useApi();

Alpine.store('participantes', {
  api,
  open: false,
  tipo: 'especial',    // 'especial' o 'principal'
  list: [],
  cliente: null,
  loading: false,
  error: null,

  // Fetch para premios especiales
  async fetchEspecial(id) {
    this.tipo = 'especial';
    this.error = null;
    this.loading = true;
    this.cliente = null;
    try {
      this.list = await this.api.call(`/admin/premios/${id}/participantes`);
      this.open = true;
      document.body.style.overflow = 'hidden';
    } catch (e) {
      this.error = e.message;
    } finally {
      this.loading = false;
    }
  },

  // Fetch para rifa principal (participantes solventes)
  async fetchPrincipal(rifaId) {
    this.tipo = 'principal';
    this.error = null;
    this.loading = true;
    this.cliente = null;
    try {
      this.list = await this.api.call(`/admin/rifas/${rifaId}/participantes`);
      this.open = true;
      document.body.style.overflow = 'hidden';
    } catch (e) {
      this.error = e.message;
    } finally {
      this.loading = false;
    }
  },

  // Selecciona un cliente
  select(item) {
    this.cliente = item.cliente;
  },

  // Cierra el modal y limpia estado
  close() {
    this.open = false;
    this.list = [];
    this.cliente = null;
    this.error = null;
    this.loading = false;
    // ¡Importante! Restaura el scroll
    document.body.style.overflow = '';
  },

  // Métodos para ganador principal y especial (opcional si los usas)
  ...createModalHandlers('principal'),
  confirmPrincipal(id) {
    return confirmWinner(
      'rifas',
      id,
      'principalNumero',
      'principalData',
      'principalError',
      'principalLoading',
      'principalOpen'
    ).call(this);
  },

  ...createModalHandlers('especial'),
  confirmEspecial(id) {
    return confirmWinner(
      'premios',
      id,
      'especialNumero',
      'especialData',
      'especialError',
      'especialLoading',
      'especialOpen'
    ).call(this);
  },
});
