@props(['value' => null])

<input 
    type="checkbox"
    {{ $attributes->merge([
        'class' => 'cursor-pointer rounded border-gray-300 text-indigo-400'
    ]) }}
    value="{{ $value }}"
>