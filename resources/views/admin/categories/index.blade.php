<x-app-layout>
    <x-slot name="header">
        <x-pages.header title="Categories List" action="{{ route('admin.categories.create') }}" buttonText="Create"
            icon="plus" />
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <main class="max-w-7xl mx-auto pb-2">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                            {{-- Filters --}}
                            <x-filters :action="route('admin.categories.index')" :baseExcept="['page']">
                                <div class="flex items-center gap-2">
                                    <x-filters.per-page />

                                    <x-filters.select-field name="sort" :options="[
                                        'name' => 'Name',
                                        'created_at' => 'Created at',
                                    ]" placeholder="Sort by field"
                                        :autoSubmit="true" />

                                    @if (request('sort'))
                                        <x-filters.select-field name="direction" :options="['asc' => 'Asc', 'desc' => 'Desc']"
                                            placeholder="Direction" :autoSubmit="true" />
                                    @endif

                                </div>
                                <div class="flex items-center gap-2 divide-x-[1px] divide-gray-100">
                                    <x-filters.search />
                                    <x-filters.reset-all>Reset filters</x-filters.reset-all>
                                </div>
                            </x-filters>
                        </div>

                        <div class="mt-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:mt-8 flex flex-col">
                            <div class="-my-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                                <div
                                    class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b">

                                    {{-- Table --}}
                                    <x-table>
                                        <x-table.thead>
                                            <x-table.th column="Name" field="name" />
                                            <x-table.th column="Description" />
                                            <x-table.th column="Created At" field="created_at" />
                                            <x-table.th />
                                        </x-table.thead>
                                        <tbody>
                                            @forelse ($categories as $category)
                                                <x-table.tr>
                                                    <x-table.td value="{{ $category->name }}" />
                                                    <x-table.td value="{{ $category->description }}" />
                                                    <x-table.td value="{{ $category->created_at->diffForHumans() }}" />
                                                    <x-table.td class="text-right flex items-center justify-end gap-1">
                                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                                            class="flex justify-center group">
                                                            <button
                                                                class="rounded-md text-blue-600 hover:text-blue-500 p-2 group-hover:bg-blue-50">
                                                                <x-icons.pencil-square />
                                                            </button>
                                                        </a>
                                                        <x-delete-button :action="route('admin.categories.destroy', $category)">
                                                            <x-icons.trash />
                                                        </x-delete-button>

                                                    </x-table.td>
                                                </x-table.tr>
                                            @empty
                                                <x-table.empty colspan="4" message="No categories found"
                                                    icon="category" />
                                            @endforelse
                                        </tbody>
                                    </x-table>

                                    {{-- Pagination --}}
                                    <x-table.pagination :paginator="$categories" />

                                </div>
                            </div>
                        </div>
                    </main>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
