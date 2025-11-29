@props(['title', 'action', 'icon' => 'arrow-left', 'buttonText'])

<div class="flex justify-between items-center gap-2">
    <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight">
        {{ __($title) }}
    </h2>
    <a href="{{ $action }}" class="block text-gray-50 hover:text-gray-100">
        <x-primary-button class="flex items-center gap-1 border-2 capitalize">
            <x-dynamic-component :component="'icons.' . $icon" size="4" />{{ $buttonText }}
        </x-primary-button>
    </a>
</div>
