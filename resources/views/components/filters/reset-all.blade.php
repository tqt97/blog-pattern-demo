@props([
    'keepPerPage' => true,
    'ignore' => ['page', 'per_page', 'trashed'],
    'icon' => false,
])

@php
    $ignoreKeys = $keepPerPage ? ['page', 'per_page'] : ['page'];

    $ignoreKeys = array_unique(array_merge($ignoreKeys, $ignore));

    $activeFilters = collect(request()->except($ignoreKeys))->reject(fn($value) => $value === null || $value === '');

    $params = [];

    if ($keepPerPage && request()->filled('per_page')) {
        $params['per_page'] = request('per_page');
    }
    if ($keepPerPage && request()->filled('trashed')) {
        $params['trashed'] = request('trashed');
    }
@endphp

@if ($activeFilters->isNotEmpty())
    <div class="pl-2">

        <a href="{{ route(Route::currentRouteName(), $params) }}"
            {{ $attributes->merge([
                'class' =>
                    'px-2 py-2 text-sm rounded-md bg-gray-100 border border-gray-200 hover:bg-gray-900 hover:text-gray-50 transition inline-flex items-center',
            ]) }}
            title="Reset all filters">
            @if ($icon)
                <x-icons.refresh />
            @endif
            <span>{{ $slot ?: 'Reset all' }}</span>
        </a>
    </div>
@endif
