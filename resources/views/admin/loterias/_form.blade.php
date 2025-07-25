{{-- resources/views/admin/loterias/_form.blade.php --}}
@props(['loteria'])

<div class="space-y-4">
  <div>
    <label for="nombre" class="block text-sm font-medium text-gray-700">
      Nombre de la Lotería
    </label>
    <input
      type="text"
      name="nombre"
      id="nombre"
      value="{{ old('nombre', $loteria->nombre ?? '') }}"
      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
      required
    />
    @error('nombre')
      <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
  </div>
</div>
