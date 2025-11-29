@props([
    'timeout' => 3000, // 3s auto hide
])

<div x-data="{ show: true }" x-init="setTimeout(() => show = false, {{ $timeout }})" x-show="show"
    x-transition:enter="transform ease-out duration-300 transition" x-transition:enter-start="translate-y-2 opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transform ease-in duration-300 transition"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-2"
    {{ $attributes->merge(['class' => 'flex items-start justify-between gap-1 w-80 p-3 rounded-lg shadow-lg border']) }}>

    <div class="flex items-center gap-1">
        {{-- Icon --}}
        <div>
            {{ $icon ?? '' }}
        </div>

        {{-- Message --}}
        <div class="text-sm font-medium ms-.5 border-s border-default ps-1.5">
            {{ $slot }}
        </div>
    </div>
    <div @click="show = false" class="cursor-pointer" class="flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="size-5 text-gray-300 hover:text-gray-500">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </div>
</div>
