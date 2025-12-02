                        <div
                            class="flex justify-between gap-3 sm:flex-row sm:items-center sm:justify-between sm:-mx-6 sm:px-6 lg:-mx-8 lg:mt-8">
                            {{-- Bulk actions --}}
                            <div class="flex items-center gap-2">
                                <select x-model="action"
                                    class="rounded-md border-gray-100 text-sm shadow-sm focus:border-gray-900 focus:ring-gray-900">
                                    <option value="">Bulk actions</option>
                                    <option value="delete">Delete</option>
                                    <option value="publish">Publish</option>
                                    <option value="unpublish">Unpublish</option>
                                </select>

                                <x-primary-button type="button" class="flex items-center gap-1"
                                    x-bind:disabled="selectedIds.length === 0 || !action" @click="submit()">
                                    <span>Apply</span>
                                </x-primary-button>

                                <span class="text-xs text-gray-500" x-show="selectedIds.length > 0"
                                    x-text="`${selectedIds.length} selected`"></span>
                            </div>
                            {{-- Filters --}}
                            <x-filters :action="route('admin.posts.index')" :baseExcept="['page']">

                                <div class="flex items-center gap-2 divide-x-[1px] divide-gray-100">
                                    <x-filters.search />
                                    <x-filters.reset-all>Reset</x-filters.reset-all>
                                </div>
                            </x-filters>

                        </div>
                        <div class="flex flex-wrap items-center gap-2 mt-4 sm:-mx-6 sm:px-6 lg:-mx-8">
                            <x-filters :action="route('admin.posts.index')" :baseExcept="['page']">
                                <x-filters.per-page />

                                <x-filters.select-field name="user_id" :options="$users" placeholder="Select author"
                                    :autoSubmit="true" />
                                <x-filters.select-field name="category_id" :options="$categories"
                                    placeholder="Select category" :autoSubmit="true" />
                                <x-filters.select-field name="tag_id" :options="$tags" placeholder="Select tag"
                                    :autoSubmit="true" />

                                <x-filters.select-field name="sort" :options="[
                                    'name' => 'Name',
                                    'created_at' => 'Created at',
                                ]" placeholder="Sort by field"
                                    :autoSubmit="true" />

                                @if (request('sort'))
                                    <x-filters.select-field name="direction" :options="['asc' => 'Asc', 'desc' => 'Desc']" placeholder="Direction"
                                        :autoSubmit="true" />
                                @endif
                            </x-filters>
                        </div>
