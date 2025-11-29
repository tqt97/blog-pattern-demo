@props([
    'paginator',
    'align' => 'end',
])
@if ($paginator->hasPages() > 0)
    <div class="px-6 py-4">
        {{ $paginator->withQueryString()->links() }}
    </div>
@endif
