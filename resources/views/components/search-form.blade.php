@props([
    'action' => '',
    'method' => 'GET',
    'placeholder' => 'Search here...',
    'width' => 'w-1/3',
    'name' => 'search',
])

{{-- How to use --}}
{{-- <x-search-form action="{{ route('products.index') }}" name="keyword" placeholder="Find product..." /> --}}

<form class="{{ $width }} rounded-sm flex items-center gap-1" action="{{ $action }}" method="{{ strtoupper($method) }}">
     @foreach(request()->except($name, 'page') as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
    
    <div class="relative flex-1 rounded-md shadow-sm">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <x-icons.search />
        </div>
        <x-text-input id="{{ $name }}" type="search" name="{{ $name }}"
        class="block w-full pl-10 sm:text-sm sm:leading-5 border border-gray-300 rounded-md bg-gray-50"
        :value="request($name)" placeholder="{{ $placeholder }}" autocomplete="{{ $name }}" />
</div>

    {{-- Nút reset --}}
    @if(request($name))
        <a href="{{ $action }}"
            class="p-2 text-sm rounded-md bg-gray-50 border border-gray-300 hover:bg-gray-300 transition">
            Reset
        </a>
    @endif

    {{-- Nút submit --}}
    <button type="submit" class="px-3 py-2 text-sm rounded-md bg-gray-800 text-white hover:bg-gray-900 transition">
        Search
    </button>
</form>
