@props([
    'column' => '',
    'field' => null,   // field in DB to sort
    'align' => 'left',
])

@php
    $isSortable = !is_null($field);
    $currentSort = request('sort');
    $currentDirection = request('direction', 'asc');
    $isCurrent = $currentSort === $field;

    $nextDirection = $isCurrent && $currentDirection === 'asc' ? 'desc' : 'asc';

    $class = "px-6 py-3 text-left text-sm font-semibold text-gray-100 uppercase1 tracking-wider"
@endphp

<th scope="col" {{ $attributes->merge(['class' => $class]) }}>
    @if ($isSortable)
        <a href="{{ request()->fullUrlWithQuery(['sort' => $field, 'direction' => $nextDirection]) }}"
           class="inline-flex items-center space-x-1 group">
            <span>{{ $column ?: $slot }}</span>

            {{-- show icon sort --}}
            @if ($isCurrent)
                @if ($currentDirection === 'asc')
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-gray-100" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 10l5-5 5 5H5z" clip-rule="evenodd" />
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-gray-100" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 10l5 5 5-5H5z" clip-rule="evenodd" />
                    </svg>
                @endif
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-300 group-hover:text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 10l5-5 5 5H5z" clip-rule="evenodd" />
                </svg>
            @endif
        </a>
    @else
        {{ $column ?: $slot }}
    @endif
</th>
