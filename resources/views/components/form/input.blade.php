<div>
  <label for="{{ $name }}" class="block text-sm font-medium">{{ $label }}</label>
  <input
    id="{{ $name }}"
    name="{{ $name }}"
    type="{{ $type }}"
    value="{{ $value }}"
    {{ $required ? 'required' : '' }}
    {{ $attributes->merge(['class' => 'w-full border-gray-300 rounded p-2 focus:ring focus:ring-orange-200']) }}
  />
  @error($name)
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
  @enderror
</div>
