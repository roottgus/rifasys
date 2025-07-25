// resources/js/helpers/api.js

/**
 * Devuelve null si localStorage no está disponible o lanza.
 */
function safeGetItem(key) {
  try {
    if (
      typeof window !== 'undefined' &&
      window.localStorage &&
      typeof window.localStorage.getItem === 'function'
    ) {
      return localStorage.getItem(key);
    }
  } catch (e) {
    console.warn('LocalStorage inaccesible:', e);
  }
  return null;
}

/**
 * Crea un cliente API que NO parsea JSON automáticamente,
 * devuelve el objeto Response para que el llamador decida
 * cómo manejar status y parseo.
 *
 * @param {string} [base=''] prefijo para todas las URLs
 */
export function useApi(base = '') {
  return {
    async call(endpoint, options = {}) {
      let response;
      try {
        const token = safeGetItem('token');
        const headers = {
          'Accept': 'application/json',
          ...(token ? { Authorization: `Bearer ${token}` } : {}),
          ...options.headers,
        };

        response = await fetch(base + endpoint, {
          ...options,
          headers,
        });
      } catch (err) {
        console.error('Error en fetch:', err);
        throw new Error('No se pudo conectar con el servidor.');
      }
      return response;
    },
  };
}

/**
 * Envía la petición para elegir un ganador y gestiona errores HTTP y de JSON.
 *
 * @param {string} type
 * @param {number|string} id
 * @param {string} numeroProp
 * @param {string} dataProp
 * @param {string} errorProp
 * @param {string} loadingProp
 * @param {string} openProp
 */
export function confirmWinner(
  type,
  id,
  numeroProp,
  dataProp,
  errorProp,
  loadingProp,
  openProp
) {
  return async function () {
    this[errorProp] = null;
    this[loadingProp] = true;
    try {
      const res = await this.api.call(
        `/admin/${type}/${id}/winner`,
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document
              .querySelector('meta[name="csrf-token"]')
              .content,
          },
          body: JSON.stringify({ numero: this[numeroProp] }),
        }
      );

      if (!res.ok) {
        const text = await res.text();
        console.error(`Error HTTP ${res.status}:`, text);
        this[errorProp] = 'Error interno del servidor.';
      } else {
        let payload;
        try {
          payload = await res.json();
        } catch (e) {
          console.error('JSON inválido:', e);
          this[errorProp] = 'Respuesta inválida del servidor.';
          return;
        }
        this[dataProp] = payload;
        this[openProp] = true;
        document.body.style.overflow = 'hidden';
      }
    } catch (e) {
      console.error('confirmWinner fallo:', e);
      this[errorProp] = e.message;
    } finally {
      this[loadingProp] = false;
    }
  };
}

/**
 * Genera los manejadores base para un modal con estado prefix*.
 *
 * @param {string} prefix
 */
export function createModalHandlers(prefix) {
  return {
    [`${prefix}Open`]: false,
    [`${prefix}Data`]: null,
    [`${prefix}Numero`]: null,
    [`${prefix}Error`]: null,
    [`${prefix}Loading`]: false,
    close() {
      this[`${prefix}Open`] = false;
      this[`${prefix}Data`] = null;
      this[`${prefix}Numero`] = null;
      this[`${prefix}Error`] = null;
      this[`${prefix}Loading`] = false;
      document.body.style.overflow = '';
    },
  };
}
