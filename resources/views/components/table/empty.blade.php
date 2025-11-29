@props(['colspan' => 1, 'message' => 'No data found', 'icon' => 'inbox'])

<x-table.tr>
    <td colspan="{{ $colspan }}" class="text-center py-6 text-gray-400 text-sm italic bg-gray-50">
        <div class="flex flex-col items-center justify-center space-y-1">
            <x-dynamic-component :component="'icons.' . $icon" class="h-5 w-5 text-gray-300" />
            <span>{{ $message }}</span>
        </div>
    </td>
</x-table.tr>
