<div class="bg-white rounded-2xl shadow-xl p-6 mt-6">
    <h3 class="text-lg font-semibold text-brand mb-4" style="color: {{ $primaryColor }}">Abonos por Método</h3>
    <canvas id="abonosMetodoChart" height="100"></canvas>
</div>

@push('scripts')
<script>
  const abonosCtx = document.getElementById('abonosMetodoChart').getContext('2d');
  new Chart(abonosCtx, {
    type: 'bar',
    data: {
      labels: {!! json_encode($metodosAbono) !!},
      datasets: [{
        label: 'Abonos',
        data: {!! json_encode($abonosDatos) !!},
        backgroundColor: [
          '{{ $primaryColor }}',
          '#60A5FA',
          '#A78BFA',
          '#FBBF24',
          '#34D399'
        ],
        borderRadius: 8,
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
