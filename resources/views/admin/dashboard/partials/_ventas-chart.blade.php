<div class="bg-white rounded-2xl shadow-xl p-6 mt-6">
    <h3 class="text-lg font-semibold text-brand mb-4" style="color: {{ $primaryColor }}">Ventas Diarias (últimos 7 días)</h3>
    <canvas id="ventasDiariasChart" height="100"></canvas>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ventasCtx = document.getElementById('ventasDiariasChart').getContext('2d');
  new Chart(ventasCtx, {
    type: 'line',
    data: {
      labels: {!! json_encode($ventasFechas) !!},
      datasets: [{
        label: 'Ventas',
        data: {!! json_encode($ventasDatos) !!},
        fill: true,
        backgroundColor: '{{ $primaryColor }}33',
        borderColor: '{{ $primaryColor }}',
        tension: 0.35,
        pointRadius: 5,
        pointBackgroundColor: '{{ $primaryColor }}'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      scales: {
        x: { grid: { color: '#f2f2f2' } },
        y: { beginAtZero: true, grid: { color: '#f2f2f2' } }
      }
    }
  });
</script>
@endpush
