@aware(['action', 'baseExcept'])

@props([
    'name', // tên field: sort, direction, status, category_id,...
    'options' => [], // mảng value => label
    'value' => null, // nếu null -> lấy từ request($name)
    'placeholder' => null,
    'autoSubmit' => false,
    'except' => null, // nếu không truyền -> mặc định = $name
])

@php
    $fieldName = $name;
    $exceptKey = $except ?? $fieldName;

    $exceptKeys = array_unique(array_merge($baseExcept ?? ['page'], [$exceptKey]));

    $params = collect(request()->except($exceptKeys))->reject(fn($v) => $v === null || $v === '');

    $currentValue = $value ?? request($fieldName);
@endphp

<form method="GET" action="{{ $action }}" x-data
    @submit.prevent="
        const form = $el;
        const field = form.querySelector('[name={{ json_encode($fieldName) }}]');

        // Nếu chọn option rỗng (placeholder) thì xoá name => không gửi key
        if (field && !field.value) {
            field.name = '';
        }

        form.submit();
      "
    {{ $attributes->merge(['class' => 'flex items-center']) }}>

    {{-- Giữ lại các filter khác --}}
    @foreach ($params as $key => $val)
        @if (is_array($val))
            @foreach ($val as $subKey => $subVal)
                @if ($subVal !== null && $subVal !== '')
                    <input type="hidden" name="{{ $key }}[{{ $subKey }}]" value="{{ $subVal }}">
                @endif
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
        @endif
    @endforeach

    <x-filters.select :name="$fieldName" :options="$options" :value="$currentValue" :placeholder="$placeholder" :autoSubmit="$autoSubmit" />
</form>
