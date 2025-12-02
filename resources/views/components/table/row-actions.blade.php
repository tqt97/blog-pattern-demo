@props([
    'model',
    'baseRoute', // ví dụ: 'admin.posts'
    'trashed' => null, // null | 'only' | 'with'

    // bật/tắt từng action
    'showEdit' => true,
    'showDelete' => true,
    'showView' => false,

    // model này có soft delete không
    'usesSoftDeletes' => true,

    // override route name nếu không theo convention
    'editRoute' => null,
    'destroyRoute' => null,
    'restoreRoute' => null,
    'forceDeleteRoute' => null,
])

@php
    use Illuminate\Support\Facades\Route;

    $isTrash = $trashed === 'only';
    $pk = $model->getKey();

    $editRouteName = $editRoute ?? $baseRoute . '.edit';
    $destroyRouteName = $destroyRoute ?? $baseRoute . '.destroy';
    $restoreRouteName = $restoreRoute ?? $baseRoute . '.restore';
    $forceDeleteRouteName = $forceDeleteRoute ?? $baseRoute . '.force-delete';

    // Check route name
    $editUrl = Route::has($editRouteName) ? route($editRouteName, $model) : null;
    $destroyUrl = Route::has($destroyRouteName) ? route($destroyRouteName, $model) : null;
    $restoreUrl = Route::has($restoreRouteName) ? route($restoreRouteName, $pk) : null;
    $forceDeleteUrl = Route::has($forceDeleteRouteName) ? route($forceDeleteRouteName, $pk) : null;
@endphp

{{-- VIEW --}}
@if (!$isTrash)
    @if ($showView && $editUrl)

        <a href="{{ $editUrl }}" class="flex justify-center group">
            <button type="button"
                class="rounded-md text-slate-600 hover:text-slate-700 p-2 group-hover:bg-slate-50 text-xs">
                View
            </button>
        </a>
    @endif

    @if ($showEdit && $editUrl)
        <a href="{{ $editUrl }}" class="flex justify-center group">
            <button type="button" class="rounded-md text-blue-600 hover:text-blue-500 p-2 group-hover:bg-blue-50">
                <x-icons.pencil-square />
            </button>
        </a>
    @endif

    @if ($showDelete && $destroyUrl)
        <x-delete-button :action="$destroyUrl">
            <x-icons.trash />
        </x-delete-button>
    @endif
@else
    {{-- VIEW TRASH --}}
    @if ($usesSoftDeletes && $restoreUrl)
        <form action="{{ $restoreUrl }}" method="POST" class="inline-block"
            onsubmit="return confirm('Restore this item?');">
            @csrf
            @method('PATCH')
            <button type="submit"
                class="rounded-md text-emerald-600 hover:text-emerald-500 p-2 hover:bg-emerald-50 text-xs">
                Restore
            </button>
        </form>
    @endif

    @if ($usesSoftDeletes && $forceDeleteUrl)
        <form action="{{ $forceDeleteUrl }}" method="POST" class="inline-block"
            onsubmit="return confirm('This will permanently delete this item. Continue?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-md text-rose-600 hover:text-rose-500 p-2 hover:bg-rose-50 text-xs">
                Force delete
            </button>
        </form>
    @endif
@endif
