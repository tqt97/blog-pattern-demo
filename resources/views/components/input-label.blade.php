@props([
    'value' => null,
    'required' => false,
    'hint' => null,
    'inline' => true,
])

<div class="flex flex-col">
    <label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700 dark:text-gray-300']) }}>
        <span>
            {{ $value ?? $slot }}

            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </span>

        @if ($hint && $inline)
            <span class="ml-1 text-xs text-gray-500">
                ({{ $hint }})
            </span>
        @endif
    </label>

    @if ($hint && !$inline)
        <span class="text-xs text-gray-500 mt-1">
            {{ $hint }}
        </span>
    @endif
</div>
