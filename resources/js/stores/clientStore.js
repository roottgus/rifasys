// resources/js/stores/clientStore.js

export default function clientStore() {
  return {
    // Datos del cliente
    cliente: {
      id:        null,
      cedula:    '',
      nombre:    '',
      email:     '',
      telefono:  '',
      direccion: ''
    },

    // Estado y mensajes de validación de campos de cliente
    validacion: {
      cedula: null,
      email: null,
      telefono: null,
      loading: {
        cedula: false,
        email: false,
        telefono: false
      },
      mensaje: {
        cedula: '',
        email: '',
        telefono: ''
      },
      conflicto: {
        cedula: false,
        email: false,
        telefono: false
      }
    },

    /**
     * Reinicia los datos del cliente y su validación.
     */
    resetCliente() {
      this.cliente = {
        id:        null,
        cedula:    '',
        nombre:    '',
        email:     '',
        telefono:  '',
        direccion: ''
      };
      this.validacion = {
        cedula: null,
        email: null,
        telefono: null,
        loading: { cedula: false, email: false, telefono: false },
        mensaje: { cedula: '', email: '', telefono: '' },
        conflicto: { cedula: false, email: false, telefono: false }
      };
    },

    /**
     * Filtra y estandariza el campo de cédula mientras el usuario lo escribe.
     * Luego llama a validarCampo('cedula') para hacer la validación en el servidor.
     */
    filtrarCedula() {
      let val = this.cliente.cedula.toUpperCase().replace(/[^VEJG0-9]/g, '');
      if (val && !val.match(/^[VEJG]/)) {
        val = 'V' + val.replace(/^[^0-9]*/, '');
      }
      val = val.replace(/^([VEJG])(\d{0,10}).*$/, '$1$2');
      this.cliente.cedula = val;

      if (!val.match(/^[VEJG]\d{5,10}$/)) {
        this.validacion.cedula = null;
        this.validacion.mensaje.cedula = 'Debe ser V, E, J o G seguido de 5-10 dígitos';
        this.validacion.conflicto.cedula = false;
        return;
      }
      this.validarCampo('cedula');
    },

    /**
     * Valida cédula/email/teléfono contra el backend y maneja los estados de validación.
     * - Si existe en BD, autocompleta datos (en el caso de cédula).
     * - Si hay conflicto, marca error.
     */
    async validarCampo(campo) {
      const valor = this.cliente[campo];
      if (!valor) {
        this.validacion[campo] = null;
        this.validacion.mensaje[campo] = '';
        this.validacion.conflicto[campo] = false;
        return;
      }

      // Validaciones de formato local
      if (campo === 'email' && !valor.match(/^[^@]+@[^@]+\.[a-zA-Z]{2,}$/)) {
        this.validacion.email = null;
        this.validacion.mensaje.email = 'Email inválido';
        this.validacion.conflicto.email = false;
        return;
      }
      if (campo === 'telefono' && !valor.match(/^[\d\+\-\s]{8,20}$/)) {
        this.validacion.telefono = null;
        this.validacion.mensaje.telefono = 'Teléfono inválido';
        this.validacion.conflicto.telefono = false;
        return;
      }

      // Empieza la petición AJAX al servidor
      this.validacion.loading[campo] = true;
      this.validacion[campo] = null;
      this.validacion.mensaje[campo] = '';
      this.validacion.conflicto[campo] = false;

      try {
        const response = await fetch('/admin/clientes/validar-campo', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          },
          body: JSON.stringify({ campo, valor, cedula: this.cliente.cedula })
        });
        const data = await response.json();


        if (data.success) {
          if (data.conflicto) {
            // Ya existe en BD, pero ligándolo a otro cliente → error
            this.validacion[campo] = false;
            this.validacion.mensaje[campo] = data.message || 'Este dato ya pertenece a otro cliente.';
            this.validacion.conflicto[campo] = true;
          } else if (data.exists && campo === 'cedula' && data.cliente) {
  // El cliente ya estaba registrado, autocompleta datos si es cédula
  this.validacion[campo] = true;
  this.validacion.mensaje[campo] = data.message || '¡Bienvenido de nuevo! Datos autocompletados.';

  // Conserva el valor de la cédula que escribió el usuario
  const valorCedula = this.cliente.cedula;

  // Autocompletar campos y asignar ID
  this.cliente = {
    id:        data.cliente.id,
    cedula:    data.cliente.cedula || valorCedula,  // <— aquí preservas el input
    nombre:    data.cliente.nombre || '',
    email:     data.cliente.email || '',
    telefono:  data.cliente.telefono || '',
    direccion: data.cliente.direccion || ''
  };

  // Ajusta estados de validación de email/telefono
  this.validacion.email    = !!this.cliente.email;
  this.validacion.telefono = !!this.cliente.telefono;
  this.validacion.conflicto.email    = false;
  this.validacion.conflicto.telefono = false;

  // Disparamos evento para recalcular descuento en el modal
  window.dispatchEvent(new CustomEvent('cliente-cargado', {
    detail: this.cliente
  }));
}
 else {
            // Valor libre para usar
            this.validacion[campo] = true;
            this.validacion.mensaje[campo] = 'Libre para usar.';
            this.validacion.conflicto[campo] = false;
            if (campo === 'cedula') {
              // Si es una cédula nueva, limpiar cualquier ID previo
              this.cliente.id = null;
            }
          }
        } else {
          // El backend devolvió éxito=false (error en la validación)
          this.validacion[campo] = false;
          this.validacion.mensaje[campo] = data.message || 'Error validando';
          this.validacion.conflicto[campo] = true;
        }
      } catch (e) {
        // Error de red
        this.validacion[campo] = false;
        this.validacion.mensaje[campo] = 'Error de red';
        this.validacion.conflicto[campo] = true;
      } finally {
        this.validacion.loading[campo] = false;
      }
    },

    /**
     * Antes de pasar al paso 2 (pago/abono), verificamos que
     * TODOS los campos obligatorios de cliente estén completos y validados.
     */
    camposRequeridosCompletos() {
      return (
        this.cliente.cedula &&
        this.validacion.cedula === true &&
        this.cliente.nombre.trim() !== '' &&
        this.cliente.email &&
        this.validacion.email === true &&
        this.cliente.telefono &&
        this.validacion.telefono === true &&
        this.cliente.direccion.trim() !== '' &&
        !this.validacion.conflicto.cedula &&
        !this.validacion.conflicto.email &&
        !this.validacion.conflicto.telefono
      );
    },
  };
}
