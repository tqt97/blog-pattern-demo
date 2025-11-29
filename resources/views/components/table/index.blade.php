@props(['striped' => false])

<table class="min-w-full divide-y divide-gray-200 {{ $striped ? 'table-striped' : '' }}">
    {{ $slot }}
</table>
