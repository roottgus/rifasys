<div>
  <label for="{{ $name }}" class="block text-sm font-medium">{{ $label }}</label>
  <textarea
    id="{{ $name }}"
    name="{{ $name }}"
    {{ $attributes->merge(['class' => 'w-full border-gray-300 rounded p-2 focus:ring focus:ring-orange-200']) }}
  >{{ $slot }}</textarea>
  @error($name)
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
  @enderror
</div>
