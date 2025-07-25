@extends('layouts.admin')

@section('title', 'Reglas de Descuento')

@section('content')
    <h1 class="text-2xl font-extrabold mb-6">Reglas de Descuento por Rifa</h1>

    {{-- Explicación del funcionamiento de las reglas de descuento --}}
    <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg shadow-sm text-gray-800 flex items-start gap-3">
        <span class="mt-0.5 text-yellow-500">
            <i class="fas fa-info-circle fa-lg"></i>
        </span>
        <div>
            <strong class="block text-yellow-800 mb-1">¿Cómo funcionan los descuentos por cantidad?</strong>
            <ul class="list-disc ml-5 text-sm space-y-1">
                <li>Aquí puedes definir reglas automáticas de descuento para cada rifa.</li>
                <li>Por ejemplo: "Si un cliente compra <b>5 tickets</b> o más de la rifa X, recibe <b>20% de descuento</b>".</li>
                <li>Puedes crear varias reglas para la misma rifa, con diferentes cantidades mínimas y porcentajes de descuento.</li>
                <li>El sistema aplicará siempre el <b>mayor descuento</b> posible según la cantidad de tickets que compre el cliente (considerando también tickets comprados anteriormente para esa rifa).</li>
                <li>El descuento se calcula y muestra automáticamente en la venta, ¡sin que tengas que hacer nada!</li>
            </ul>
            <span class="block text-xs text-gray-500 mt-2">
                Ejemplo: si tienes reglas para 5 tickets (20%) y 10 tickets (30%), al vender 10 o más tickets a un mismo cliente, se aplicará el 30% de descuento.
            </span>
        </div>
    </div>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-900 rounded shadow flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <a href="{{ route('admin.descuentos.create') }}"
        class="bg-primary text-white px-4 py-2 rounded mb-4 inline-block hover:bg-primary/80">
        <i class="fas fa-plus-circle"></i> Nueva Regla de Descuento
    </a>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-2xl shadow border">
            <thead class="bg-primary/10">
                <tr>
                    <th class="px-5 py-3 text-left text-sm font-bold text-primary">Rifa</th>
                    <th class="px-5 py-3 text-left text-sm font-bold text-primary">Cantidad mínima</th>
                    <th class="px-5 py-3 text-left text-sm font-bold text-primary">% Descuento</th>
                    <th class="px-5 py-3 text-center text-sm font-bold text-primary">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($descuentos as $descuento)
                    <tr class="border-b hover:bg-primary/5 transition">
                        <td class="px-5 py-3">{{ $descuento->rifa->nombre }}</td>
                        <td class="px-5 py-3">{{ $descuento->cantidad_minima }}</td>
                        <td class="px-5 py-3 font-bold text-green-700">{{ $descuento->porcentaje }}%</td>
                        <td class="px-5 py-3 text-center space-x-1">
                            <a href="{{ route('admin.descuentos.edit', $descuento) }}"
                                class="inline-flex items-center gap-1 px-3 py-1 bg-blue-50 text-blue-700 rounded-full hover:bg-blue-100 transition"
                                title="Editar">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <form action="{{ route('admin.descuentos.destroy', $descuento) }}" method="POST"
                                class="inline delete-descuento-form">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    class="btn-eliminar inline-flex items-center gap-1 px-3 py-1 bg-red-50 text-red-700 rounded-full hover:bg-red-100 transition"
                                    title="Eliminar">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-gray-400">No hay reglas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.btn-eliminar').forEach(function(btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Confirmar eliminación',
                text: "¿Seguro que deseas eliminar esta regla? Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-2 rounded shadow',
                    cancelButton: 'bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold px-6 py-2 rounded shadow',
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.closest('form').submit();
                }
            });
        });
    });
</script>
@endpush
