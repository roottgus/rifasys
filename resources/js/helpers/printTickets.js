// resources/js/helpers/printTickets.js
import { getClienteNombre, getClienteDireccion, getClienteTelefono } from './tickets';

export function imprimirTickets({
  rifa,
  tickets,
  filtro,
  padLen = 2,
  logoUrl = '/images/logo.png'
}) {
  let cols, rows, ths;
  if (filtro === 'disponible') {
    cols = ['Número'];
    ths = '<th>Número</th>';
    rows = tickets.filter(t => t.estado === 'disponible').map(
      t => `<tr><td>${String(t.numero).padStart(padLen, '0')}</td></tr>`
    ).join('');
  } else if (filtro === 'vendido') {
    cols = ['Número', 'Cliente', 'Dirección', 'Teléfono'];
    ths = cols.map(c => `<th>${c}</th>`).join('');
    rows = tickets.filter(t => t.estado === 'vendido').map(
      t => `<tr>
            <td>${String(t.numero).padStart(padLen, '0')}</td>
            <td>${getClienteNombre(t)}</td>
            <td>${getClienteDireccion(t)}</td>
            <td>${getClienteTelefono(t)}</td>
          </tr>`
    ).join('');
  } else if (filtro === 'abonado') {
    cols = ['Número', 'Cliente', 'Dirección', 'Teléfono', 'Abono'];
    ths = cols.map(c => `<th>${c}</th>`).join('');
    rows = tickets.filter(t => (t.abono || 0) > 0).map(
      t => `<tr>
            <td>${String(t.numero).padStart(padLen, '0')}</td>
            <td>${getClienteNombre(t)}</td>
            <td>${getClienteDireccion(t)}</td>
            <td>${getClienteTelefono(t)}</td>
            <td>${t.abono ? `Bs. ${parseFloat(t.abono).toFixed(2)}` : '-'}</td>
          </tr>`
    ).join('');
  } else if (filtro === 'reservado') {
    cols = ['Número', 'Cliente', 'Dirección', 'Teléfono'];
    ths = cols.map(c => `<th>${c}</th>`).join('');
    rows = tickets.filter(t => t.estado === 'reservado').map(
      t => `<tr>
            <td>${String(t.numero).padStart(padLen, '0')}</td>
            <td>${getClienteNombre(t)}</td>
            <td>${getClienteDireccion(t)}</td>
            <td>${getClienteTelefono(t)}</td>
          </tr>`
    ).join('');
  } else {
    cols = ['Número', 'Estado', 'Cliente', 'Dirección', 'Teléfono'];
    ths = cols.map(c => `<th>${c}</th>`).join('');
    rows = tickets.map(
      t => `<tr>
            <td>${String(t.numero).padStart(padLen, '0')}</td>
            <td>${t.estado}</td>
            <td>${getClienteNombre(t)}</td>
            <td>${getClienteDireccion(t)}</td>
            <td>${getClienteTelefono(t)}</td>
          </tr>`
    ).join('');
  }

  let popup = window.open('', '', 'width=1200,height=700');
  if (!popup) return alert('Permite las ventanas emergentes para imprimir');
  popup.document.write(`
    <html>
    <head>
      <title>Imprimir Tickets</title>
      <style>
        body { font-family: 'Inter', Arial, sans-serif; padding: 32px; color: #222; }
        h2 { font-size: 22px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th, td { border: 1px solid #aaa; padding: 8px 4px; font-size: 14px; text-align: center; }
        th { background: #f0f0f0; }
        .legal { margin-top: 24px; font-size: 11px; color: #888; }
        .logo { max-height: 50px; margin-bottom: 10px; }
      </style>
    </head>
    <body>
      <img src="${logoUrl}" class="logo" />
      <h2>Rifa: ${rifa.nombre || '--'}</h2>
      <div>Fecha de impresión: ${new Date().toLocaleString('es-VE')}</div>
      <table>
        <thead><tr>${ths}</tr></thead>
        <tbody>${rows}</tbody>
      </table>
      <div class="legal">Este documento es solo para uso administrativo y confidencial. Sistema de Rifas © ${new Date().getFullYear()}</div>
    </body>
    </html>
  `);
  setTimeout(() => popup.print(), 400);
}
