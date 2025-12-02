@props(['hover' => true, 'isHighlight' => false])


<tr class="bg-white {{ $hover ? 'hover:bg-gray-50' : '' }} {{ $isHighlight ? 'row-just-updated' : '' }}">
    {{ $slot }}
</tr>
