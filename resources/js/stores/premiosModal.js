// resources/js/stores/premiosModal.js

export default function premiosModal({
  initialHas = false,
  initialPremios = [],
  loterias = [],
  tiposLoteria = [],
  premioErrors = []
}) {
  return {
    // Estado reactivo
    hasPremios: initialHas ?? false,
    modalOpen: initialHas ?? false,
    premios: Array.isArray(initialPremios) ? initialPremios : [],
    loterias: Array.isArray(loterias) ? loterias : [],
    tiposLoteria: Array.isArray(tiposLoteria) ? tiposLoteria : [],
    premioErrors: Array.isArray(premioErrors) ? premioErrors : [],

    // Tipos dependientes del padre
    tiposLoteriaByLoteriaId(loteria_id) {
      return this.tiposLoteria.filter(tipo => tipo.loteria_id == loteria_id);
    },

    addPremio() {
      this.premios.push({
        loteria_id:       '', // ID de lotería (padre)
        tipo_loteria_id:  '', // ID de tipo (hijo)
        tipo_premio:      '',
        monto:            '',
        detalle_articulo: '',
        abono_minimo:     '',
        fecha_premio:     '',
        hora_premio:      '',
        descripcion:      '',
      });
    },

    removePremio(i) {
      if (this.premios.length > i && i >= 0) {
        this.premios.splice(i, 1);
      }
    },

    // Limpiar tipo de lotería al cambiar de lotería
    onLoteriaChange(premio) {
      if (premio) premio.tipo_loteria_id = '';
    },

    toggleModal() {
      this.modalOpen = this.hasPremios;
      if (this.modalOpen && (!this.premios || this.premios.length === 0)) {
        this.premios = [];
        this.addPremio();
      }
    },
  }
}
