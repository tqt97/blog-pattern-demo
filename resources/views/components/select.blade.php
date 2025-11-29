@props([
    'disabled' => false,
    'options' => [],         // array key => label
    'value' => null,         // current value
    'placeholder' => null,   // first option
])

@php
    // Kiểm tra có slot hay không
    $hasCustomSlot = trim($slot) !== '';
@endphp

<select
    @disabled($disabled)
    {{ $attributes->merge([
        'class' =>
            'w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 ' .
            'focus:border-gray-500 dark:focus:border-gray-600 focus:ring-gray-500 ' .
            'dark:focus:ring-gray-600 rounded-md shadow-sm'
    ]) }}
>
    @if ($hasCustomSlot)
        {{ $slot }}

    @else
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $key => $label)
            <option value="{{ $key }}" @selected((string) $value === (string) $key)>
                {{ $label }}
            </option>
        @endforeach
    @endif
</select>

{{-- How to use --}}
{{-- <x-select
    name="status"
    :value="old('status', $category?->status)"
    :options="[
        'draft' => 'Draft',
        'published' => 'Published'
    ]"
    placeholder="-- Select status --"
/> --}}
{{-- <x-select name="role">
    <option value="">-- Select role --</option>
    <option value="admin">Admin</option>
    <option value="editor">Editor</option>
</x-select> --}}
{{-- End how to use --}}
