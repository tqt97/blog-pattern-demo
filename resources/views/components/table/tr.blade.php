@props(['hover' => true])

<tr class="bg-white {{ $hover ? 'hover:bg-gray-50' : '' }}">
    {{ $slot }}
</tr>
