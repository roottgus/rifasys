<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <h1 class="text-3xl font-extrabold text-gray-800 flex items-center gap-2">
        <i class="fas fa-users"></i> Clientes
    </h1>
    <form method="GET" class="flex items-center gap-2">
        <input name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, cédula, teléfono..."
               class="px-3 py-2 rounded-lg border shadow-sm focus:ring-primary-300 w-72">
        <button class="bg-primary-600 text-white px-3 py-2 rounded-lg hover:bg-primary-700" type="submit">
            <i class="fas fa-search"></i> Buscar
        </button>
    </form>
    <button @click="openModal()" class="ml-auto bg-primary-600 text-white px-5 py-2.5 rounded-full shadow-lg hover:bg-primary-700 transition">
        <i class="fas fa-user-plus"></i> Nuevo Cliente
    </button>
</div>
