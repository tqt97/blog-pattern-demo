@props([
    'action' => '#', // route bulk
    'trashed' => null, // null | 'only' | 'with'
    'normalActions' => [], // actions for normal view
    'trashActions' => [], // actions for  trashed only view
])

@php
    $isTrash = $trashed === 'only';

    // Fallback:
    $normal = $normalActions ?: [['value' => 'delete', 'label' => 'Delete']];

    $trash = $trashActions ?: [
        ['value' => 'restore', 'label' => 'Restore'],
        ['value' => 'force_delete', 'label' => 'Force delete'],
    ];

    $currentActions = $isTrash ? $trash : $normal;
@endphp

<div class="flex flex-wrap items-center gap-1">
    {{-- Hidden bulk form (xử lý submit trong Alpine) --}}
    <form x-ref="bulkForm" method="POST" action="{{ $action }}" class="hidden">
        @csrf
        <input type="hidden" name="action" x-model="action">
        <input type="hidden" name="ids" :value="selectedIds.join(',')">
    </form>

    {{-- Bulk action select --}}
    <select x-model="action"
        class="rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-gray-900 focus:ring-gray-900">
        <option value="">Bulk actions</option>

        @foreach ($currentActions as $item)
            <option value="{{ $item['value'] }}">
                {{ $item['label'] }}
            </option>
        @endforeach
    </select>

    {{-- Apply --}}
    <x-primary-button type="button" class="flex items-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed"
        x-bind:disabled="selectedIds.length === 0 || !action" @click="submit()">
        <span>Apply</span>
    </x-primary-button>

    {{-- count selected --}}
    <span x-show="selectedIds.length > 0" x-text="`${selectedIds.length} selected`"
        class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 border border-slate-200">
    </span>
</div>
