<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Gráfica de Ventas Diarias --}}
    <div class="bg-white shadow-lg rounded-2xl p-6 flex flex-col">
        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fas fa-chart-line text-orange-500"></i>
            Ventas Diarias (últimos 7 días)
        </h3>
        <canvas id="ventasDiariasChart" height="110"></canvas>
    </div>
    {{-- Gráfica de Abonos por Método --}}
    <div class="bg-white shadow-lg rounded-2xl p-6 flex flex-col">
        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fas fa-coins text-yellow-500"></i>
            Abonos por Método
        </h3>
        <canvas id="abonosMetodoChart" height="110"></canvas>
    </div>
</div>
