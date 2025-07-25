import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';
jsPDF.autoTable = autoTable;
import { getClienteNombre, getClienteDireccion, getClienteTelefono } from './tickets';

/**
 * Exporta los tickets a PDF según filtro y formato profesional, con watermark en todas las páginas.
 */
export async function exportarTicketsPDF({
  rifa,
  tickets,
  filtro,
  padLen = 2,
  logoUrl = '/images/logo.png'
}) {
  const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'A4' });

  // --- Cargar logo base64 (para encabezado y watermark)
  let imgBase64 = null;
  try {
    const img = await fetch(logoUrl).then(res => res.blob()).then(blob => {
      return new Promise(resolve => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.readAsDataURL(blob);
      });
    });
    imgBase64 = img;
    // Logo pequeño en encabezado
    doc.addImage(imgBase64, 'PNG', 40, 30, 90, 40);
  } catch { /* Si falla, omite logo */ }

  doc.setFontSize(18);
  doc.text(`Reporte de Tickets: ${rifa.nombre || '--'}`, 150, 55);
  doc.setFontSize(10);
  doc.text(`Fecha de generación: ${new Date().toLocaleString('es-VE')}`, 150, 70);

  // --- Función para watermark
  function drawWatermarkOnPage(docInstance) {
    if (!imgBase64) return;
    // Marca de agua centrada, translúcida
    // Opcional: Si tienes problemas con setGState puedes omitirlo, el PNG transparente funciona bien
    const pageWidth = docInstance.internal.pageSize.width;
    const pageHeight = docInstance.internal.pageSize.height;
    try {
      docInstance.setGState && docInstance.setGState(new docInstance.GState({ opacity: 0.09 }));
    } catch {}
    docInstance.addImage(
      imgBase64,
      'PNG',
      pageWidth / 2 - 160,
      pageHeight / 2 - 70,
      320,
      140,
      '',
      'NONE',
      0
    );
    try {
      docInstance.setGState && docInstance.setGState(new docInstance.GState({ opacity: 1 }));
    } catch {}
  }

  // --- GRID VISUAL para disponibles
  if (filtro === 'disponible') {
    const disponibles = tickets.filter(t => t.estado === 'disponible').map(t => String(t.numero).padStart(padLen, '0'));
    const itemsPorFila = 20;
    const cellW = 34, cellH = 26;
    const startX = 50, startY = 110;
    let x = startX, y = startY;
    let count = 0;

    doc.setFontSize(12);
    doc.text("Números Disponibles", startX, startY - 18);

    // Dibuja watermark en la primera página
    drawWatermarkOnPage(doc);

    disponibles.forEach((num, idx) => {
      doc.roundedRect(x, y, cellW, cellH, 6, 6, 'S');
      doc.text(num, x + cellW / 2, y + cellH / 2 + 4, { align: "center", baseline: "middle" });
      x += cellW;
      count++;
      if (count % itemsPorFila === 0) {
        x = startX;
        y += cellH;
        if (y > doc.internal.pageSize.height - 50) {
          doc.addPage();
          y = startY;
          drawWatermarkOnPage(doc); // watermark en cada página nueva
        }
      }
    });

    // Pie de página
    doc.setFontSize(9);
    doc.text("Este documento es solo para uso administrativo y confidencial. Sistema de Rifas © " + new Date().getFullYear(), 40, doc.internal.pageSize.height - 30);
    doc.save(`reporte-tickets-${rifa.nombre || 'rifa'}-disponibles.pdf`);
    return;
  }

  // --- TABLA PROFESIONAL para los demás filtros ---
  let columns, rows;
  if (filtro === 'vendido') {
    columns = ['Número', 'Cliente', 'Dirección', 'Teléfono'];
    rows = tickets.filter(t => t.estado === 'vendido')
      .map(t => [
        String(t.numero).padStart(padLen, '0'),
        getClienteNombre(t),
        getClienteDireccion(t),
        getClienteTelefono(t),
      ]);
  } else if (filtro === 'abonado') {
    columns = ['Número', 'Cliente', 'Dirección', 'Teléfono', 'Abono'];
    rows = tickets.filter(t => (t.abono || 0) > 0)
      .map(t => [
        String(t.numero).padStart(padLen, '0'),
        getClienteNombre(t),
        getClienteDireccion(t),
        getClienteTelefono(t),
        t.abono ? `Bs. ${parseFloat(t.abono).toFixed(2)}` : '-'
      ]);
  } else if (filtro === 'reservado') {
    columns = ['Número', 'Cliente', 'Dirección', 'Teléfono'];
    rows = tickets.filter(t => t.estado === 'reservado')
      .map(t => [
        String(t.numero).padStart(padLen, '0'),
        getClienteNombre(t),
        getClienteDireccion(t),
        getClienteTelefono(t)
      ]);
  } else {
    columns = ['Número', 'Estado', 'Cliente', 'Dirección', 'Teléfono'];
    rows = tickets.map(t => [
      String(t.numero).padStart(padLen, '0'),
      t.estado,
      getClienteNombre(t),
      getClienteDireccion(t),
      getClienteTelefono(t)
    ]);
  }

  // Watermark en todas las páginas de la tabla usando didDrawPage de autoTable
  autoTable(doc, {
    head: [columns],
    body: rows,
    startY: 90,
    headStyles: { fillColor: [55, 65, 81] },
    styles: { fontSize: 10, cellPadding: 4 },
    didDrawPage: function (data) {
      drawWatermarkOnPage(doc);
      // Pie de página profesional
      doc.setFontSize(9);
      doc.text(
        "Este documento es solo para uso administrativo y confidencial. Sistema de Rifas © " + new Date().getFullYear(),
        40,
        doc.internal.pageSize.height - 30
      );
    }
  });

  doc.save(`reporte-tickets-${rifa.nombre || 'rifa'}.pdf`);
}
