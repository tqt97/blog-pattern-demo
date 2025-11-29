@props([
    'disabled' => false,
    'rows' => 3,
])

<textarea @disabled($disabled) rows="{{ $rows }}"
    {{ $attributes->merge([
        'class' =>
            'w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 ' .
            'focus:border-gray-500 dark:focus:border-gray-600 focus:ring-gray-500 ' .
            'dark:focus:ring-gray-600 rounded-md shadow-sm',
    ]) }}>{{ $slot }}</textarea>
