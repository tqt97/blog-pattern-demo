@props(['paginator', 'align' => 'end'])
@if ($paginator->hasPages() > 0)
    <div class="mt-6 pt-4 border-t border-dotted border-gray-300">
        {{ $paginator->withQueryString()->links() }}
    </div>
@endif
