// resources/js/stores/modalVentaTicket.js
import { useApi }             from '../helpers/api';
import clientStore            from './clientStore';
import validationStore        from './validationStore';
import paymentStore           from './paymentStore';
import descuentoStore         from './descuentoStore';
import { ventaCoreStore }     from './mixins/ventaCoreStore';
import { abonoModoStore }     from './mixins/abonoModoStore';
import { imprimirStore }      from './mixins/imprimirStore';

export default function modalVentaTicket() {
  return {
    // ── Sub-stores externos ──
    api: useApi(),
    ...clientStore(),
    ...validationStore(),
    ...paymentStore(),
    descuentos: descuentoStore(),

    // ── Mixins propios ──
    ...ventaCoreStore(),
    ...abonoModoStore(),
    ...imprimirStore(),

    hoyStr() {
      const d = new Date();
      const pad = n => String(n).padStart(2, '0');
      return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
    },
  };
}
