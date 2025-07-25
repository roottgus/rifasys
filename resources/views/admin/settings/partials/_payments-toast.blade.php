{{-- resources/views/admin/settings/partials/_payments-toast.blade.php --}}

{{-- 1. Toast AlpineJS Dinámico (para cambios instantáneos) --}}
<div
  x-data
  x-show="toast && toast.show"
  x-transition.duration.400ms
  class="fixed top-6 right-6 z-50 flex items-center gap-3"
  style="display: none;"
>
  <div 
    :class="{
      'bg-green-600 ring-green-300': !toast.error,
      'bg-red-600 ring-red-300': toast.error
    }"
    class="flex items-center gap-3 text-white px-5 py-3 rounded-xl shadow-lg ring-2"
  >
    <i :class="toast.error ? 'fas fa-times-circle text-2xl' : 'fas fa-check-circle text-2xl'"></i>
    <div class="text-base font-semibold" x-text="toast.msg"></div>
    <button class="ml-4 text-white/80 hover:text-white" @click="toast.show = false">
      <i class="fas fa-times"></i>
    </button>
  </div>
</div>

{{-- 2. Toast por sesión Laravel (en caso de redirección clásica) --}}
@if(session('success'))
  <div 
    x-data="{ show: true }"
    x-show="show"
    x-transition.duration.500ms
    class="fixed top-6 right-6 z-50 flex items-center gap-3 bg-green-600 text-white px-5 py-3 rounded-xl shadow-lg ring-2 ring-green-300"
    style="display: none;"
    @click="show = false"
    x-init="setTimeout(() => show = false, 3500)"
  >
    <i class="fas fa-check-circle text-2xl"></i>
    <div class="text-base font-semibold">
      {{ session('success') }}
    </div>
    <button class="ml-4 text-white/80 hover:text-white" @click="show = false">
      <i class="fas fa-times"></i>
    </button>
  </div>
@endif
