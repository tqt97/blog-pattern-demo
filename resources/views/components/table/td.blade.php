@props(['value' => null, 'colspan' => null, 'class' => ''])

<td @if ($colspan) colspan="{{ $colspan }}" @endif
    class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 {{ $class }} border-b border-gray-200">
    {{ $value ?? $slot }}
</td>
