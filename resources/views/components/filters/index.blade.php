@props([
    'action',
    'baseExcept' => ['page'],
])

<div {{ $attributes->merge(['class' => '']) }}>
    {{ $slot }}
</div>
