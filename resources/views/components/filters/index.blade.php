@props([
    'action',
    'baseExcept' => ['page'], // luôn bỏ page
])

<div {{ $attributes->merge(['class' => 'w-full flex items-center justify-between gap-2']) }}>
    {{ $slot }}
</div>
