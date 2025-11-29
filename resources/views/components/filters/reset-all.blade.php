@props([
    // Có giữ lại per_page khi reset không
    'keepPerPage' => true,

    // Những key KHÔNG tính là filter (luôn bỏ qua khi check)
    'ignore' => ['page', 'per_page'],
    'icon' => false,
])

@php
    // Xác định các key để bỏ qua khi xét "có filter hay không"
    $ignoreKeys = $keepPerPage ? ['page', 'per_page'] : ['page'];

    $ignoreKeys = array_unique(array_merge($ignoreKeys, $ignore));

    // Lấy tất cả query param trừ các key ignore, và bỏ value rỗng
    $activeFilters = collect(request()->except($ignoreKeys))->reject(fn($value) => $value === null || $value === '');

    // Build params cho link reset
    $params = [];

    if ($keepPerPage && request()->filled('per_page')) {
        $params['per_page'] = request('per_page');
    }
@endphp

@if ($activeFilters->isNotEmpty())
<div class="pl-2">

    <a href="{{ route(Route::currentRouteName(), $params) }}"
        {{ $attributes->merge([
            'class' =>
                'px-2 py-2 text-sm rounded-md bg-gray-200 border border-gray-100 hover:bg-gray-900 hover:text-gray-50 transition inline-flex items-center',
        ]) }}
        title="Reset all filters">
        @if ($icon)
            <x-icons.refresh />
        @endif
        <span>{{ $slot ?: 'Reset all' }}</span>
    </a>
</div>
@endif
