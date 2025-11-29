<x-filters.form :except="['q']" class="rounded-sm flex items-center gap-2" x-data
    >
    <div class="relative flex-1 rounded-md shadow-sm">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <x-icons.search />
        </div>
        <x-text-input id="q" type="search" name="q"
            class="block w-full pl-10 sm:text-sm sm:leading-5 border border-gray-300 rounded-md bg-gray-50"
            :value="request('q')" placeholder="Search here ..." autocomplete="search" />
    </div>

    @if (request('q'))
        <a href="{{ route(Route::currentRouteName(), request()->except('q', 'page')) }}" title="Clear search text"
            class="p-2 text-sm rounded-md bg-gray-50 border border-gray-300 hover:bg-gray-900 hover:text-gray-50 transition">
            Clear
        </a>
    @endif

    <button type="submit" class="px-3 py-2 text-sm rounded-md bg-gray-800 text-white hover:bg-gray-900 transition">
        Search
    </button>
</x-filters.form>
