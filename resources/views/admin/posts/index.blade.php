@php
    $trashed = request('trashed');

    $postNormalBulkActions = [
        ['value' => 'delete', 'label' => 'Delete'],
        ['value' => 'publish', 'label' => 'Publish'],
        ['value' => 'unpublish', 'label' => 'Unpublish'],
    ];

    $postTrashBulkActions = [
        ['value' => 'restore', 'label' => 'Restore'],
        ['value' => 'force_delete', 'label' => 'Force delete'],
    ];

    $highlightId = (int) session('highlight_id');
@endphp



<x-app-layout>
    <x-slot name="header">
        <x-pages.header title="Posts List" action="{{ route('admin.posts.create') }}" buttonText="Create" icon="plus" />
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                {{-- ĐẶT x-data Ở ĐÂY, BAO CẢ FORM + MAIN --}}
                <div class="p-6 text-gray-900 dark:text-gray-100" x-data="bulkActions({
                    confirmMap: {
                        delete: 'Delete selected posts?',
                        force_delete: 'This will permanently delete selected posts. Continue?',
                        // publish / unpublish nếu muốn confirm thì thêm
                    }
                })">

                    {{-- FORM BULK ẨN (trong scope x-data) --}}
                    <form x-ref="bulkForm" method="POST" action="{{ route('admin.posts.bulk') }}" class="hidden">
                        @csrf
                        <input type="hidden" name="action" x-model="action">
                        <input type="hidden" name="ids" :value="selectedIds.join(',')">
                    </form>

                    <main class="max-w-7xl mx-auto pb-2">
                        {{-- TOOLBAR CARD --}}
                        <div class="mt-4 sm:-mx-6 sm:px-6 lg:-mx-8">
                            <div
                                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm px-4 py-3 sm:py-4 sm:px-6 space-y-4">

                                {{-- ========== HÀNG 1: BULK + SEARCH ========== --}}
                                <div class="flex flex-col gap-1 lg:flex-row lg:items-center lg:justify-between">

                                    {{-- LEFT: Bulk actions --}}

                                    <x-bulk-actions action="{{ route('admin.posts.bulk') }}" :trashed="$trashed"
                                        :normalActions="$postNormalBulkActions" :trashActions="$postTrashBulkActions" />

                                    {{-- RIGHT: Search (sát lề phải) --}}
                                    <div class="w-full lg:flex-1 lg:max-w-md lg:ml-auto justify-end">
                                        <x-filters :action="route('admin.posts.index')" :baseExcept="['page']">
                                            <div class="flex1">
                                                <x-filters.search class="w-full" />
                                            </div>
                                        </x-filters>
                                    </div>
                                </div>

                                {{-- ========== HÀNG 2: FILTER BAR + RESET (CUỐI) ========== --}}
                                <x-filters :action="route('admin.posts.index')" :baseExcept="['page']" class="flex items-center">
                                    <div
                                        class="flex flex-wrap items-center gap-1 pt-4 border-t border-dashed border-slate-200">

                                        {{-- Per page --}}
                                        <x-filters.per-page />

                                        <x-filters.select-field name="trashed" :options="[
                                            // '' => 'All',
                                            'with' => 'With trashed',
                                            'only' => 'Only trashed',
                                        ]" placeholder="All"
                                            :autoSubmit="true" />

                                        {{-- Advanced filters --}}
                                        <x-filters.select-field name="user_id" :options="$users"
                                            placeholder="Select author" :autoSubmit="true" />

                                        <x-filters.select-field name="category_id" :options="$categories"
                                            placeholder="Select category" :autoSubmit="true" />

                                        <x-filters.select-field name="tag_id" :options="$tags"
                                            placeholder="Select tag" :autoSubmit="true" />
                                        <x-filters.select-field name="status" :options="$status"
                                            placeholder="Select status" :autoSubmit="true" />

                                        <x-filters.select-field name="sort" :options="[
                                            'title' => 'Title',
                                            'status' => 'Status',
                                            'published_at' => 'Published at',
                                            'created_at' => 'Created at',
                                        ]" placeholder="Sort by"
                                            :autoSubmit="true" />

                                        @if (request('sort'))
                                            <x-filters.select-field name="direction" :options="['asc' => 'Asc', 'desc' => 'Desc']"
                                                placeholder="Direction" :autoSubmit="true" />
                                        @endif

                                        {{-- RIGHT: Reset cuối cùng --}}
                                        <div class="ml-auto">
                                            {{-- <x-filters.reset-all
                                                class="inline-flex items-center rounded-lg border border-slate-200 px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">
                                                Reset
                                            </x-filters.reset-all> --}}
                                            <x-filters.reset-all>
                                                <x-icons.refresh />
                                            </x-filters.reset-all>
                                        </div>
                                    </div>
                                </x-filters>
                            </div>
                        </div>



                        {{-- TABLE --}}
                        <div class="mt-6 sm:-mx-6 sm:px-6 lg:-mx-8 lg:mt-8 flex flex-col">
                            <div class="-my-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                                <div
                                    class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b">

                                    <x-table>
                                        <x-table.thead>
                                            <x-table.th>
                                                <input type="checkbox" x-model="selectAll" @change="toggleAll()"
                                                    class="rounded border-gray-50 text-gray-900 shadow-sm focus:ring-gray-900">
                                            </x-table.th>
                                            <x-table.th column="Post" field="title" />
                                            <x-table.th column="Status" field="status" />
                                            <x-table.th column="Published At" field="published_at" />
                                            <x-table.th column="Created At" field="created_at" />
                                            <x-table.th />
                                        </x-table.thead>

                                        <tbody>
                                            @forelse ($posts as $post)
                                                <x-table.tr :isHighlight="$highlightId === $post->id">
                                                    <x-table.td class="px-4 py-3">
                                                        <input type="checkbox" value="{{ $post->id }}"
                                                            x-model="selectedIds"
                                                            class="rounded border-gray-300 text-gray-900 shadow-sm focus:ring-gray-900">
                                                    </x-table.td>

                                                    <x-table.td>
                                                        <div class="flex items-center gap-2">
                                                            <img src="{{ $post->thumbnail_url }}"
                                                                alt="{{ $post->title }}" class="size-12 rounded-md">
                                                            <div class="flex flex-col">
                                                                <span class="font-semibold text-md">
                                                                    <a href="{{ route('posts.show', $post) }}"
                                                                        class="flex items-center gap-1 hover:text-blue-500"
                                                                        title="{{ $post->title }}" target="_blank">
                                                                        {{ $post->short_title }}
                                                                        <x-icons.arrow-top-right-on-square
                                                                            size="3" />
                                                                    </a>
                                                                </span>
                                                                <div class="flex items-center gap-2">
                                                                    <span class="flex items-center gap-1 text-xs">
                                                                        <x-icons.single-user size="3" />
                                                                        {{ $post->user_name }}
                                                                    </span>
                                                                    <span
                                                                        class="flex items-center gap-1 text-xs text-gray-500 pl-3">
                                                                        <x-icons.category size="3" />
                                                                        {{ $post->category_name }}
                                                                    </span>
                                                                </div>
                                                                <span class="text-xs flex items-center gap-1">
                                                                    <x-icons.tag size="3" />
                                                                    {{ $post->tags_list }}

                                                                </span>
                                                            </div>
                                                        </div>
                                                    </x-table.td>

                                                    <x-table.td class="text-xs">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $post->status_badge_class }}">
                                                            {{ $post->status_label }}
                                                        </span>

                                                    </x-table.td>
                                                    <x-table.td value="{{ $post->published_at }}" />
                                                    <x-table.td value="{{ $post->created_at->diffForHumans() }}" />

                                                    <x-table.td>

                                                        <div class="text-right flex items-center justify-end gap-1">

                                                            <x-table.row-actions :model="$post"
                                                                baseRoute="admin.posts" :trashed="request('trashed')"
                                                                :usesSoftDeletes="true" :showView="false" :showEdit="true"
                                                                :showDelete="true" />
                                                        </div>
                                                    </x-table.td>
                                                </x-table.tr>
                                            @empty
                                                <x-table.empty colspan="6" message="No posts found" icon="file" />
                                            @endforelse
                                        </tbody>
                                    </x-table>
                                </div>
                            </div>

                            <x-table.pagination :paginator="$posts" />
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function bulkActions(config = {}) {
                return {
                    action: '',
                    selectAll: false,
                    selectedIds: [],

                    confirmMap: config.confirmMap || {
                        delete: 'Are you sure you want to delete selected items?',
                        force_delete: 'This will permanently delete selected items. Continue?',
                    },

                    toggleAll() {
                        if (this.selectAll) {
                            this.selectedIds = @json($posts->pluck('id'));
                        } else {
                            this.selectedIds = [];
                        }
                    },

                    submit() {
                        if (!this.action || this.selectedIds.length === 0) {
                            return;
                        }

                        const msg = this.confirmMap[this.action] ?? null;
                        if (msg && !confirm(msg)) {
                            return;
                        }

                        this.$refs.bulkForm.submit();
                    }
                }
            }
        </script>
    @endpush

</x-app-layout>
