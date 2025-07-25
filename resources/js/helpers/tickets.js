// resources/js/helpers/tickets.js

/** --- CLIENTE HELPERS --- */
export function getClienteNombre(ticket) {
  return ticket.cliente_nombre || ticket.cliente || '-';
}
export function getClienteDireccion(ticket) {
  return ticket.cliente_direccion || ticket.direccion || '-';
}
export function getClienteTelefono(ticket) {
  return ticket.cliente_telefono || ticket.telefono || '-';
}

/** --- FILTRO HELPERS --- */
export const estadosExportar = [
  { value: 'all', label: 'Todos' },
  { value: 'disponible', label: 'Disponibles' },
  { value: 'vendido', label: 'Vendidos' },
  { value: 'abonado', label: 'Abonados' },
  { value: 'reservado', label: 'Reservados' },
];

export function filtroNombre(f) {
  switch (f) {
    case 'all': return 'Todos';
    case 'disponible': return 'Disponibles';
    case 'vendido': return 'Vendidos';
    case 'abonado': return 'Abonados';
    case 'reservado': return 'Reservados';
    default: return 'Todos';
  }
}
