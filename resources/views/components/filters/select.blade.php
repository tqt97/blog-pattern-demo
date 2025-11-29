@props([
    'name',
    'options' => [], // mảng value => label
    'value' => null, // giá trị hiện tại
    'placeholder' => null, // option đầu tiên (value rỗng)
    'autoSubmit' => false, // onchange submit
])

<select name="{{ $name }}"
    {{ $attributes->merge([
        'class' => 'rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500',
    ]) }}
    @if ($autoSubmit) onchange="this.form.submit()" @endif>
    @if ($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif

    @foreach ($options as $optionValue => $label)
        <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>
            {{ $label }}
        </option>
    @endforeach
</select>
