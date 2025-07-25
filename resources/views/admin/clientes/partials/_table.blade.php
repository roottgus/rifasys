<div class="bg-white shadow-xl rounded-2xl overflow-hidden">
    <table class="w-full min-w-[700px]">
        <thead class="bg-gradient-to-r from-primary-50 to-blue-50">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-bold text-gray-600">#</th>
                <th class="px-4 py-3 text-left text-sm font-bold text-gray-600">Nombre</th>
                <th class="px-4 py-3 text-left text-sm font-bold text-gray-600">Cédula</th>
                <th class="px-4 py-3 text-left text-sm font-bold text-gray-600">Email</th>
                <th class="px-4 py-3 text-left text-sm font-bold text-gray-600">Teléfono</th>
                <th class="px-4 py-3 text-left text-sm font-bold text-gray-600">Registrado</th>
                <th class="px-4 py-3 text-center text-sm font-bold text-gray-600">Tickets</th>
                <th class="px-4 py-3 text-right text-sm font-bold text-gray-600">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($clientes as $cliente)
                <tr class="hover:bg-primary-50/40 transition">
                    <td class="px-4 py-3 text-gray-400 font-semibold">{{ $cliente->id }}</td>
                    <td class="px-4 py-3 font-bold text-gray-800">{{ $cliente->nombre }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $cliente->cedula }}</td>
                    <td class="px-4 py-3 text-blue-600">{{ $cliente->email }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $cliente->telefono }}</td>
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $cliente->created_at?->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3 text-center">
                        <button
    @click="abrirModalTickets({ id: {{ $cliente->id }} })"
    class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 text-xs font-semibold"
    title="Ver tickets de {{ $cliente->nombre }}">
    <i class="fas fa-ticket-alt"></i>
    {{ $cliente->tickets_count ?? 0 }}
</button>

                        {{-- Si prefieres el link clásico, descomenta: --}}
                        {{-- <a href="{{ route('admin.tickets.index', ['cliente_id' => $cliente->id]) }}" ...> --}}
                    </td>
                    <td class="px-4 py-3 text-right space-x-1">
                        <button @click="openModal({{ $cliente->toJson() }})"
                                class="inline-flex items-center gap-1 px-3 py-1 bg-primary-50 text-primary-700 rounded-full hover:bg-primary-100 transition"
                                title="Editar"><i class="fas fa-edit"></i></button>
                        <form action="{{ route('admin.clientes.destroy', $cliente) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('¿Eliminar este cliente?')"
                                    class="inline-flex items-center gap-1 px-3 py-1 bg-red-50 text-red-700 rounded-full hover:bg-red-100 transition"
                                    title="Eliminar"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-5 py-8 text-center text-gray-400">No hay clientes registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4 px-4 pb-3">
        {{ $clientes->withQueryString()->links() }}
    </div>
</div>
