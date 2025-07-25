// resources/js/stores/mixins/imprimirStore.js
import logoBase64 from '../constants/logoBase64';

export function imprimirStore() {
  return {
    // Convierte un SVG data-URL a PNG para QZ Tray
    async convertSvgToPngBase64(svgDataUrl) {
      return new Promise(resolve => {
        const img = new Image();
        img.crossOrigin = 'Anonymous';
        img.onload = () => {
          const scale = 0.3; // 30% del tamaño original
          const canvas = document.createElement('canvas');
          canvas.width  = img.width  * scale;
          canvas.height = img.height * scale;
          const ctx = canvas.getContext('2d');
          ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
          resolve(canvas.toDataURL('image/png'));
        };
        img.src = svgDataUrl;
      });
    },

    // Lógica completa de impresión vía QZ Tray
    async imprimirTicket() {
      if (typeof window.qz === 'undefined') {
        alert('QZ Tray no está disponible. Por favor, asegúrate de que QZ Tray esté instalado y en ejecución.');
        return;
      }

      const ticket = this.picked;
      if (!ticket) {
        alert('No hay ticket para imprimir.');
        return;
      }

      let detalle;
      try {
        const res = await this.api.call(`/admin/tickets/${ticket.id}/detalle-json`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        detalle = await res.json();
      } catch (e) {
        console.error('Error al obtener detalle:', e);
        alert('No se pudieron cargar los datos del ticket.');
        return;
      }

      // Obtener QR en base64 PNG
      let qrDataUrl = '';
      const qrImg = document.querySelector('#qr-ticket-img');
      if (qrImg?.src) qrDataUrl = qrImg.src;
      else if (detalle.url_qr) qrDataUrl = detalle.url_qr;
      else if (detalle.qr_svg) qrDataUrl = `data:image/svg+xml;base64,${btoa(detalle.qr_svg)}`;

      if (qrDataUrl.startsWith('data:image/svg+xml;base64,')) {
        qrDataUrl = await this.convertSvgToPngBase64(qrDataUrl);
      }
      const qrBase64 = qrDataUrl.split(',', 2)[1] || '';

      // Preparar datos
      const logo        = logoBase64;
      const nombreRifa  = detalle.rifa?.nombre    || '–';
      const loteria     = detalle.rifa?.loteria   || '–';
      const tipoLoteria = detalle.rifa?.tipo      || '–';
      let fechaSorteo = '–';
      if (detalle.rifa?.fecha_sorteo) {
        const [d, t] = detalle.rifa.fecha_sorteo.split('T');
        const [Y, M, D] = d.split('-');
        const hhmm = t.split('.')[0].substr(0,5);
        fechaSorteo = `${D}/${M}/${Y}` + (hhmm !== '00:00' ? ` ${hhmm}` : '');
      }
      const horaSorteo = detalle.rifa?.hora_sorteo?.substr(0,5) || '–';
      const premios   = Array.isArray(detalle.premios) && detalle.premios.length
                        ? detalle.premios.map(p => p.descripcion || p).join(', ')
                        : '–';
      const numero      = detalle.numero_formateado        || '--';
      const precioTicket= typeof detalle.precio_ticket==='number'
                          ? detalle.precio_ticket.toFixed(2)
                          : detalle.precio_ticket || '--';
      const montoPagado = typeof detalle.total_abonado==='number'
                          ? detalle.total_abonado.toFixed(2)
                          : detalle.total_abonado || '0.00';
      const rawEstado  = (detalle.estado||'').toLowerCase();
      const estado     = rawEstado==='reservado'
                          ? 'APARTADO'
                          : (rawEstado.toUpperCase()||'SIN ESTADO');
      const clienteNombre = detalle.cliente?.nombre || '—';
      let fechaVenta = '–';
      if (detalle.updated_at) {
        const rawV = detalle.updated_at.split('.')[0];
        const [dV,tV] = rawV.includes('T')? rawV.split('T') : rawV.split(' ');
        const [yV,mV,dayV] = dV.split('-');
        const [hV,minV] = tV.split(':');
        fechaVenta = `${dayV}/${mV}/${yV} ${hV}:${minV}`;
      }
      const codigo = detalle.codigo_qr||detalle.uuid||'—';
      function colorEstado(e) {
        switch(e) {
          case 'VENDIDO':  return '#198754';
          case 'APARTADO': return '#fd7e14';
          case 'ABONADO':  return '#0dcaf0';
          default:         return '#6c757d';
        }
      }

      // Montar HTML (estilos idénticos al original)
      const html = `
<!doctype html>
<html><head><meta charset="utf-8"><style>
@page{margin:0}body{font-family:'Courier New',monospace;text-align:center;margin:0;padding:0;margin-top:-3px;}
.logo{width:120px;margin:0 auto;}h1{font-size:18px;margin:1px 0}.rifa{font-size:15px;font-weight:bold;}
.estado{font-size:13px;font-weight:bold;color:${colorEstado(estado)};border:3px solid ${colorEstado(estado)};padding:2px 6px;border-radius:4px;margin:4px 0;}
.numero{font-size:22px;font-weight:bold;margin:4px 0}.datos{font-size:13px;margin:2px 0}.monto,.pagado{font-size:14px;font-weight:bold;}
.pagado{color:#007bff}.qr img{width:110px;height:110px}.footer{font-size:13px;margin-top:0;padding-bottom:0;}
</style></head><body>
${logo?`<img class="logo" src="data:image/png;base64,${logo}" alt="Logo empresa"/>`:''}
<h1>TICKET DE RIFA</h1>
<div class="rifa">${nombreRifa}</div>
<div class="estado">${estado}</div>
<div class="line"></div>
<p class="numero">Nro: ${numero}</p>
<p class="datos"><strong>Cliente:</strong> ${clienteNombre}</p>
<p class="datos"><strong>Fecha venta:</strong> ${fechaVenta}</p>
<p class="datos"><strong>Fecha sorteo:</strong> ${fechaSorteo}</p>
<p class="datos"><strong>Hora sorteo:</strong> ${horaSorteo}</p>
<p class="datos"><strong>Lotería:</strong> ${loteria} – ${tipoLoteria}</p>
<div class="monto">Precio ticket: $${precioTicket}</div>
<div class="pagado">Monto pagado: $${montoPagado}</div>
<p class="datos"><strong>Premios especiales:</strong></p>
<p class="datos" style="word-wrap:break-word;">${premios}</p>
<div class="line"></div>
<div class="qr"><img src="data:image/png;base64,${qrBase64}" alt="QR"/></div>
<div class="line"></div>
<p class="datos"><strong>Código verif.:</strong></p>
<p class="datos" style="word-break:break-all;">${codigo}</p>
<div class="line"></div>
<div class="footer"><p>¡Gracias por confiar en <b>Rifasys</b>!</p><p>@rifasys</p></div>
</body></html>`;

      try {
        if (!window.qz.websocket.isActive()) await window.qz.websocket.connect();
        const cfg = window.qz.configs.create('POS-80');
        await window.qz.print(cfg, [{ type:'html', format:'plain', data:html }]);
        alert('¡Ticket enviado a impresión!');
      } catch (err) {
        alert('Error al imprimir: ' + (err.message||err.toString()));
      } finally {
        if (window.qz.websocket.isActive()) window.qz.websocket.disconnect();
      }
    }
  };
}
