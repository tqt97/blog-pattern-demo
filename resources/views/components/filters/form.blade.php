@aware(['action', 'baseExcept'])

@props([
    'except' => [], // các key cần bỏ thêm cho form này
])

@php
    $exceptKeys = array_unique(array_merge($baseExcept ?? ['page'], $except));

    // Lấy tất cả query param, trừ những thứ trong except, và bỏ value rỗng
    $params = collect(request()->except($exceptKeys))->reject(fn($value) => $value === null || $value === '');
@endphp

<form method="GET" action="{{ $action }}" {{ $attributes->merge(['class' => 'flex items-center']) }}>

    {{-- Giữ lại các filter khác --}}
    @foreach ($params as $key => $value)
        @if (is_array($value))
            @foreach ($value as $subKey => $subValue)
                @if ($subValue !== null && $subValue !== '')
                    <input type="hidden" name="{{ $key }}[{{ $subKey }}]" value="{{ $subValue }}">
                @endif
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach

    {{ $slot }}
</form>
