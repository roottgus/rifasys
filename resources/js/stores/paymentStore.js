// resources/js/stores/paymentStore.js

export default function paymentStore() {
  return {
    // ── Métodos de pago cargados desde el backend ──
    metodosPagoActivos: window.metodosPagoActivos || [],
    metodoPago: '',
    pagoDatos: {
      fecha: '',
      referencia: '',
      titular_reporte: '',
      monto_reporte: '',
      banco_reporte: '',
      oficina_o_punto: '',
      comentarios: ''
    },

    // ── Flags para validación de referencia ──
    errorReferencia: '',
    referenciaVerificando: false,

    /**
     * Configuración de campos que el administrador debe rellenar
     * según el método escogido.
     */
    reportConfigs: {
      'tran_bancaria_nacional': [
        { key: 'fecha',           label: 'Fecha de Pago',     type: 'date' },
        { key: 'banco_reporte',   label: 'Banco (Cliente)',   type: 'text' },
        { key: 'referencia',      label: 'Referencia',        type: 'text' },
        { key: 'titular_reporte', label: 'Titular (Cliente)', type: 'text' },
        { key: 'monto_reporte',   label: 'Monto Pagado',      type: 'number' }
      ],
      'tran_bancaria_internacional': [
        { key: 'fecha',         label: 'Fecha de Pago',    type: 'date' },
        { key: 'referencia',    label: 'Referencia',       type: 'text' },
        { key: 'monto_reporte', label: 'Monto Pagado',     type: 'number' },
        { key: 'banco_reporte', label: 'Banco (Cliente)',  type: 'text' }
      ],
      'pago_movil': [
        { key: 'fecha',         label: 'Fecha de Pago',    type: 'date' },
        { key: 'referencia',    label: 'Referencia',       type: 'text' },
        { key: 'monto_reporte', label: 'Monto Pagado',     type: 'number' }
      ],
      'zelle': [
        { key: 'fecha',           label: 'Fecha de Pago',        type: 'date' },
        { key: 'referencia',      label: 'Referencia (Email)',   type: 'text' },
        { key: 'titular_reporte', label: 'Titular (Cliente)',    type: 'text' },
        { key: 'monto_reporte',   label: 'Monto Pagado',         type: 'number' }
      ],
      'pago_efectivo': [
        { key: 'fecha',         label: 'Fecha de Pago',    type: 'date' },
        { key: 'monto_reporte', label: 'Monto Pagado',     type: 'number' },
        {
          key: 'oficina_o_punto',
          label: 'Lugar de Pago',
          type: 'select',
          options: [
            { value: 'oficina',     label: 'En la oficina' },
            { value: 'punto_calle', label: 'Punto de calle' }
          ]
        }
      ]
    },

    // 🚩 Función para obtener los campos activos del método seleccionado
    reportFields() {
      return this.reportConfigs[this.metodoPago] || [];
    },

    /**
     * Invocado al seleccionar un método de pago.
     */
    seleccionarMetodoPago(mpKey) {
      this.metodoPago = mpKey;
      // Reiniciar datos y errores
      this.pagoDatos = {
        fecha: '',
        referencia: '',
        titular_reporte: '',
        monto_reporte: '',
        banco_reporte: '',
        oficina_o_punto: '',
        comentarios: ''
      };
      this.errorReferencia = '';
      this.referenciaVerificando = false;

    },

    /**
     * Llama al backend para validar unicidad de la referencia.
     * Usa la ruta GET /admin/abonos/validar-referencia
     */
    async validarReferenciaUnica() {
      const ref = this.pagoDatos.referencia?.trim();
      if (!ref) {
        this.errorReferencia = '';
        return;
      }
      this.errorReferencia = '';
      this.referenciaVerificando = true;
      try {
        const res = await fetch(
          `/admin/abonos/validar-referencia?referencia=${encodeURIComponent(ref)}`
        );
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();
        if (data.existe) {
          this.errorReferencia = 'Esta referencia ya fue usada en otro pago.';
        } else {
          this.errorReferencia = '';
        }
      } catch (e) {
        console.error('Error validando referencia:', e);
        this.errorReferencia = 'No se pudo validar la referencia.';
      } finally {
        this.referenciaVerificando = false;
      }
    },

    /**
     * Devuelve los detalles de la empresa (name, description, icon, fields)
     * para el método actualmente seleccionado.
     */
    obtenerDetallesMetodo() {
      const mp = this.metodosPagoActivos.find(m => m.key === this.metodoPago);
      if (!mp) return null;
      return {
        name:        mp.name,
        descripcion: mp.descripcion || '',
        fields:      mp.fields || [],
        icon:        mp.icon || '',
        info:        mp.info || ''
      };
    }
  };
}
