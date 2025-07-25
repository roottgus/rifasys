{{-- CÉDULA --}}
<div class="relative group mb-3">
  <input
    type="text"
    x-model="cliente.cedula"
    @input="filtrarCedula()"
    maxlength="11"
    pattern="^[VEJG]\d{5,10}$"
    class="border rounded p-2 text-sm pr-8 transition-all duration-300 focus:shadow-lg w-full"
    placeholder="Ejemplo (V12345678)"
    :class="{
      'border-green-400 shadow-green-100': validacion.cedula === true && !validacion.conflicto.cedula,
      'border-red-400 shadow-red-100': validacion.cedula === false || validacion.conflicto.cedula
    }"
    required
  >
  {{-- Íconos --}}
  <template x-if="validacion.loading.cedula">
    <span class="absolute right-2 top-3 text-gray-400 animate-spin">
      <i class="fas fa-spinner"></i>
    </span>
  </template>
  <template x-if="validacion.cedula === true && !validacion.conflicto.cedula">
    <span class="absolute right-2 top-3 text-green-500 transition-all group-hover:scale-125">
      <i class="fas fa-check-circle"></i>
    </span>
  </template>
  <template x-if="validacion.cedula === false || validacion.conflicto.cedula">
    <span class="absolute right-2 top-3 text-red-500 transition-all group-hover:scale-125">
      <i class="fas fa-times-circle"></i>
    </span>
  </template>

  {{-- SOLO mensaje rojo conflicto (prioridad máxima) --}}
  <template x-if="validacion.conflicto.cedula">
    <div class="mb-2 text-red-900 bg-red-100 border border-red-300 rounded px-3 py-2 flex items-center gap-2 mt-2">
      <i class="fas fa-times-circle"></i>
      <span x-text="validacion.mensaje.cedula"></span>
    </div>
  </template>
  {{-- Mensaje pequeño SOLO si NO hay conflicto Y NO es mensaje de “registrado” --}}
  <template x-if="!validacion.conflicto.cedula && !(validacion.cedula === true && validacion.mensaje.cedula && validacion.mensaje.cedula.includes('registrado'))">
    <small class="block text-xs mt-1 transition-all"
          :class="{
            'text-green-500': validacion.cedula === true,
            'text-red-500': validacion.cedula === false,
            'text-gray-400': validacion.cedula === null
          }"
          x-text="validacion.mensaje.cedula"></small>
  </template>
</div>

{{-- NOMBRE --}}
<div class="mb-3">
  <input
    type="text"
    x-model="cliente.nombre"
    placeholder="Nombre completo"
    class="border rounded p-2 text-sm w-full"
    required
  >
</div>

{{-- EMAIL --}}
<div class="relative group mb-3">
  <input
    type="email"
    x-model="cliente.email"
    @input.debounce.600ms="validarCampo('email')"
    class="border rounded p-2 text-sm pr-8 w-full transition-all duration-300 focus:shadow-lg"
    placeholder="Correo electrónico"
    :class="{
      'border-green-400 shadow-green-100': validacion.email === true && !validacion.conflicto.email,
      'border-red-400 shadow-red-100': validacion.email === false || validacion.conflicto.email
    }"
    required
  >
  <template x-if="validacion.loading.email">
    <span class="absolute right-2 top-3 text-gray-400 animate-spin">
      <i class="fas fa-spinner"></i>
    </span>
  </template>
  <template x-if="validacion.email === true && !validacion.conflicto.email">
    <span class="absolute right-2 top-3 text-green-500 transition-all group-hover:scale-125">
      <i class="fas fa-check-circle"></i>
    </span>
  </template>
  <template x-if="validacion.email === false || validacion.conflicto.email">
    <span class="absolute right-2 top-3 text-red-500 transition-all group-hover:scale-125">
      <i class="fas fa-times-circle"></i>
    </span>
  </template>
  {{-- Mensaje rojo conflicto --}}
  <template x-if="validacion.conflicto.email">
    <div class="mb-2 text-red-900 bg-red-100 border border-red-300 rounded px-3 py-2 flex items-center gap-2 mt-2">
      <i class="fas fa-times-circle"></i>
      <span x-text="validacion.mensaje.email"></span>
    </div>
  </template>
  {{-- Mensaje pequeño solo si no hay conflicto --}}
  <template x-if="!validacion.conflicto.email">
    <small class="block text-xs mt-1 transition-all"
          :class="{
            'text-green-500': validacion.email === true,
            'text-red-500': validacion.email === false,
            'text-gray-400': validacion.email === null
          }"
          x-text="validacion.mensaje.email"></small>
  </template>
</div>

{{-- TELÉFONO --}}
<div class="relative group mb-3">
  <input
    type="text"
    x-model="cliente.telefono"
    @input.debounce.600ms="validarCampo('telefono')"
    class="border rounded p-2 text-sm pr-8 w-full transition-all duration-300 focus:shadow-lg"
    placeholder="Teléfono"
    :class="{
      'border-green-400 shadow-green-100': validacion.telefono === true && !validacion.conflicto.telefono,
      'border-red-400 shadow-red-100': validacion.telefono === false || validacion.conflicto.telefono
    }"
    required
  >
  <template x-if="validacion.loading.telefono">
    <span class="absolute right-2 top-3 text-gray-400 animate-spin">
      <i class="fas fa-spinner"></i>
    </span>
  </template>
  <template x-if="validacion.telefono === true && !validacion.conflicto.telefono">
    <span class="absolute right-2 top-3 text-green-500 transition-all group-hover:scale-125">
      <i class="fas fa-check-circle"></i>
    </span>
  </template>
  <template x-if="validacion.telefono === false || validacion.conflicto.telefono">
    <span class="absolute right-2 top-3 text-red-500 transition-all group-hover:scale-125">
      <i class="fas fa-times-circle"></i>
    </span>
  </template>
  {{-- Mensaje rojo conflicto --}}
  <template x-if="validacion.conflicto.telefono">
    <div class="mb-2 text-red-900 bg-red-100 border border-red-300 rounded px-3 py-2 flex items-center gap-2 mt-2">
      <i class="fas fa-times-circle"></i>
      <span x-text="validacion.mensaje.telefono"></span>
    </div>
  </template>
  {{-- Mensaje pequeño solo si no hay conflicto --}}
  <template x-if="!validacion.conflicto.telefono">
    <small class="block text-xs mt-1 transition-all"
          :class="{
            'text-green-500': validacion.telefono === true,
            'text-red-500': validacion.telefono === false,
            'text-gray-400': validacion.telefono === null
          }"
          x-text="validacion.mensaje.telefono"></small>
  </template>
</div>

{{-- DIRECCIÓN --}}
<div>
  <input
    type="text"
    x-model="cliente.direccion"
    placeholder="Dirección"
    class="border rounded p-2 text-sm w-full"
    required
  >
</div>
