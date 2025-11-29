@props([
    'action' => '#',
    'confirm' => 'Are you sure?',
])

<form action="{{ $action }}" method="POST" class="inline-block group"
    onsubmit="return confirm('{{ $confirm }}')">
    @csrf
    @method('DELETE')

    <button type="submit" class="rounded-md text-red-600 hover:text-red-500 p-2 group-hover:bg-red-50">
        {{ $slot ?? '' }}
    </button>
</form>
