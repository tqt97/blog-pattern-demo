<x-app-layout>
    <x-slot name="header">
        <x-pages.header title="Tags List" action="{{ route('admin.tags.create') }}" buttonText="Create" icon="plus" />
    </x-slot>

    <div class="max-w-7xl mx-auto pb-2">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            {{-- Filters --}}
            <x-filters :action="route('admin.tags.index')" :baseExcept="['page']" class="w-full flex items-center gap-1 justify-between">
                <div class="flex items-center gap-2">
                    <x-filters.per-page />

                    <x-filters.select-field name="sort" :options="[
                        'name' => 'Name',
                        'created_at' => 'Created at',
                    ]" placeholder="Sort by field"
                        :autoSubmit="true" />

                    @if (request('sort'))
                        <x-filters.select-field name="direction" :options="['asc' => 'Asc', 'desc' => 'Desc']" placeholder="Direction"
                            :autoSubmit="true" />
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
                <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b">

                    {{-- Table --}}
                    <x-table>
                        <x-table.thead>
                            <x-table.th column="Name" field="name" />
                            <x-table.th column="Created At" field="created_at" />
                            <x-table.th />
                        </x-table.thead>
                        <tbody>
                            @forelse ($tags as $tag)
                                <x-table.tr>
                                    <x-table.td value="{{ $tag->name }}" />
                                    <x-table.td value="{{ $tag->created_at->diffForHumans() }}" />
                                    <x-table.td class="text-right flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.tags.edit', $tag) }}"
                                            class="flex justify-center group">
                                            <button
                                                class="rounded-md text-blue-600 hover:text-blue-500 p-2 group-hover:bg-blue-50">
                                                <x-icons.pencil-square />
                                            </button>
                                        </a>
                                        <x-delete-button :action="route('admin.tags.destroy', $tag)">
                                            <x-icons.trash />
                                        </x-delete-button>

                                    </x-table.td>
                                </x-table.tr>
                            @empty
                                <x-table.empty colspan="3" message="No tags found" icon="tag" />
                            @endforelse
                        </tbody>
                    </x-table>

                    {{-- Pagination --}}
                    <x-table.pagination :paginator="$tags" />

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
