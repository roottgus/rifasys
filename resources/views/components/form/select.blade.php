<div>
  <label for="{{ $name }}" class="block text-sm font-medium">{{ $label }}</label>
  <select
    id="{{ $name }}"
    name="{{ $name }}"
    {{ $attributes->merge(['class' => 'w-full border-gray-300 rounded p-2 focus:ring focus:ring-orange-200']) }}
  >
    <option value="">-- Selecciona --</option>
    @foreach($options as $value => $labelOption)
      <option value="{{ $value }}" {{ (string) $selected === (string) $value ? 'selected' : '' }}>
        {{ $labelOption }}
      </option>
    @endforeach
  </select>
  @error($name)
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
  @enderror
</div>
