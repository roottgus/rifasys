// resources/js/stores/gestionLoterias.js

export default function gestionLoterias() {
  return {
    openLoteriaModal: false,
    openTipoModal: false,
    loterias: window.loteriasData || {},
    tiposLoteria: window.tiposLoteriaData || [],
    loteriaNombre: '',
    loteriaErrors: [],
    loteriaSuccess: '',
    tipoNombre: '',
    tipoLoteriaId: '',
    tipoErrors: [],
    tipoSuccess: '',
    searchTipo: '',

    get loteriasArray() {
      return Object.values(this.loterias).sort((a, b) => a.nombre.localeCompare(b.nombre));
    },
    get filteredTipos() {
      const q = (this.searchTipo || '').toLowerCase();
      return !q
        ? this.tiposLoteria
        : this.tiposLoteria.filter(t => t.nombre.toLowerCase().includes(q) || this.getLoteriaNombre(t.loteria_id).toLowerCase().includes(q));
    },
    getLoteriaNombre(id) {
      return this.loterias[id]?.nombre || '—';
    },
    deleteLoteriaModal: false,
    loteriaToDelete: null,
    openDeleteLoteriaModal(loteria) { this.loteriaToDelete = loteria; this.deleteLoteriaModal = true; },
    confirmDeleteLoteria() {
      if (!this.loteriaToDelete) return;
      fetch(`/admin/loterias/${this.loteriaToDelete.id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' },
      }).then(async res => {
        if (res.ok) {
          delete this.loterias[this.loteriaToDelete.id];
          this.deleteLoteriaModal = false;
          this.loteriaToDelete = null;
          this.loteriaSuccess = '¡Lotería eliminada con éxito!';
          setTimeout(() => this.loteriaSuccess = '', 1800);
          this.reloadTiposLoteria();
        } else {
          this.loteriaSuccess = '';
          this.deleteLoteriaModal = false;
        }
      });
    },
    deleteTipoLoteriaModal: false,
    tipoLoteriaToDelete: null,
    openDeleteTipoLoteriaModal(tipo) { this.tipoLoteriaToDelete = tipo; this.deleteTipoLoteriaModal = true; },
    confirmDeleteTipoLoteria() {
      if (!this.tipoLoteriaToDelete) return;
      fetch(`/admin/tipos-loteria/${this.tipoLoteriaToDelete.id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' },
      }).then(async res => {
        if (res.ok) {
          this.tiposLoteria = this.tiposLoteria.filter(t => t.id !== this.tipoLoteriaToDelete.id);
          this.deleteTipoLoteriaModal = false;
          this.tipoLoteriaToDelete = null;
          this.tipoSuccess = '¡Tipo de lotería eliminado con éxito!';
          setTimeout(() => this.tipoSuccess = '', 1800);
        } else {
          this.tipoSuccess = '';
          this.deleteTipoLoteriaModal = false;
        }
      });
    },
    submitLoteria() {
      this.loteriaErrors = [];
      fetch('/admin/loterias', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: JSON.stringify({ nombre: this.loteriaNombre })
      })
      .then(async res => {
        if (res.status === 422) {
          let data = await res.json();
          this.loteriaErrors = Object.values(data.errors).flat();
          return;
        }
        let data = await res.json();
        if (data.success) {
          this.loterias[data.loteria.id] = data.loteria;
          this.loteriaNombre = '';
          this.loteriaSuccess = '¡La lotería ha sido creada con éxito!';
          setTimeout(() => {
            this.loteriaSuccess = '';
            this.openLoteriaModal = false;
          }, 2000);
        }
      })
      .catch(() => {
        this.loteriaErrors = ['Error inesperado, intenta de nuevo.'];
      });
    },
    submitTipoLoteria() {
      this.tipoErrors = [];
      fetch('/admin/tipos-loteria', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          nombre: this.tipoNombre,
          loteria_id: this.tipoLoteriaId
        })
      })
      .then(async res => {
        if (res.status === 422) {
          let data = await res.json();
          this.tipoErrors = Object.values(data.errors).flat();
          return;
        }
        let data = await res.json();
        if (data.success) {
          this.tipoNombre = '';
          this.tipoLoteriaId = '';
          this.tipoSuccess = '¡El tipo de lotería ha sido creado con éxito!';
          this.reloadTiposLoteria();
          setTimeout(() => {
            this.tipoSuccess = '';
            this.openTipoModal = false;
          }, 2000);
        }
      })
      .catch(() => {
        this.tipoErrors = ['Error inesperado, intenta de nuevo.'];
      });
    },
    reloadTiposLoteria() {
      fetch('/admin/tipos-loteria?ajax=1', {
        headers: { 'Accept': 'application/json' }
      })
      .then(r => r.json())
      .then(tipos => { this.tiposLoteria = tipos; });
    }
  }
}
