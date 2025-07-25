// resources/js/stores/validationStore.js

export default function validationStore() {
  return {
    // Mensajes de error
    errorMensaje: '',
    errorPago: '',

    /**
     * Valida que TODOS los campos definidos en reportFields
     * estén completos (no vacíos) en pagoDatos.
     * Si falta alguno, asigna errorPago y retorna false.
     */
    validarPago(reportFields, pagoDatos) {
      this.errorPago = '';

      for (const field of this.reportFields()) {
  const val = this.pagoDatos[field.key];
  if (val === undefined || val === null || String(val).trim() === '') {
    this.errorPago = `Completa el campo “${field.label}.”`;
    return false;
  }
}
return true;

    },

    /**
     * Muestra un mensaje de error de pago (si se necesita).
     * Puede adaptarse para disparar un toast visual, etc.
     */
    showErrorPago(msg) {
      this.errorPago = msg;
      // Ejemplo: si quisieras usar un toast real,
      // setearías aquí alguna bandera para mostrarlo.
    }
  };
}
