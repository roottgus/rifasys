{{-- resources/views/admin/abonos/partials/_form.blade.php --}}
@php
  // Para legacy: mantenemos metodo_pago y referencia por si hay reservas antiguas
  $oldTicket    = old('ticket_id', $abono->ticket_id ?? '');
  $oldMethod    = old('payment_method_id', $abono->payment_method_id ?? '');
  $oldRef       = old('reference_number', $abono->reference_number ?? '');
@endphp

{{-- Mostrar errores --}}
@if($errors->any())
  <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
    <ul class="list-disc pl-5">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

  {{-- Ticket --}}
  <div>
    <label class="block text-sm font-medium">Ticket</label>
    <select name="ticket_id" required class="w-full border rounded p-2">
      <option value="">-- Selecciona ticket --</option>
      @foreach($tickets as $ticket)
        <option value="{{ $ticket->id }}" @selected($oldTicket == $ticket->id)>
          #{{ $ticket->numero }} — {{ $ticket->rifa->nombre }}
        </option>
      @endforeach
    </select>
  </div>

  {{-- Monto del abono --}}
  <div>
    <label class="block text-sm font-medium">Monto (USD)</label>
    <input type="number"
           name="monto"
           value="{{ old('monto', $abono->monto ?? '') }}"
           step="0.01"
           min="0"
           required
           class="w-full border rounded p-2">
  </div>

  {{-- Método de Pago --}}
  <div>
    <label class="block text-sm font-medium">Método de Pago</label>
    <select name="payment_method_id" required class="w-full border rounded p-2">
      <option value="">-- Selecciona método --</option>
      @foreach($methods as $method)
        <option value="{{ $method->id }}" @selected($oldMethod == $method->id)>
          {{ $method->name }}
        </option>
      @endforeach
    </select>
  </div>

  {{-- Número de Referencia --}}
  <div>
    <label class="block text-sm font-medium">Número de Referencia</label>
    <input type="text"
           name="reference_number"
           value="{{ $oldRef }}"
           required
           class="w-full border rounded p-2"
           placeholder="Ej. 123ABC456">
  </div>

  {{-- Banco (opcional) --}}
  <div>
    <label class="block text-sm font-medium">Banco (opcional)</label>
    <input type="text"
           name="banco"
           value="{{ old('banco', $abono->banco ?? '') }}"
           class="w-full border rounded p-2">
  </div>

  {{-- Teléfono --}}
  <div>
    <label class="block text-sm font-medium">Teléfono (opcional)</label>
    <input type="text"
           name="telefono"
           value="{{ old('telefono', $abono->telefono ?? '') }}"
           class="w-full border rounded p-2">
  </div>

  {{-- Cédula --}}
  <div>
    <label class="block text-sm font-medium">Cédula (opcional)</label>
    <input type="text"
           name="cedula"
           value="{{ old('cedula', $abono->cedula ?? '') }}"
           class="w-full border rounded p-2">
  </div>

  {{-- Titular --}}
  <div>
    <label class="block text-sm font-medium">Titular (opcional)</label>
    <input type="text"
           name="titular"
           value="{{ old('titular', $abono->titular ?? '') }}"
           class="w-full border rounded p-2">
  </div>

</div>
