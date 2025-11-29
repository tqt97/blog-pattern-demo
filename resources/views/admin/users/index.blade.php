<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <main class="max-w-7xl mx-auto py-2">
                        <div class="flex justify-end">
                            <x-search-form action="{{ route('admin.users.index') }}" placeholder="Search users..." />
                        </div>

                        <div class="mt-8 flex flex-col">
                            <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                                <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg">
                                    <x-table>
                                        <x-table.thead>
                                            <x-table.th column="First Name" />
                                            <x-table.th column="Last Name" />
                                            <x-table.th column="Email" />
                                            <x-table.th column="Last login" />
                                            <x-table.th />
                                        </x-table.thead>
                                        <tbody>
                                            @forelse ($users as $user)
                                                <x-table.tr>
                                                    <x-table.td value="{{ $user->first_name  }}" />
                                                    <x-table.td value="{{ $user->last_name  }}" />
                                                    <x-table.td value="{{ $user->email  }}" />
                                                    <x-table.td
                                                        value="{{ $user->logins()->latest()->first()?->created_at ?? '-'  }}" />
                                                    <x-table.td class="text-right">
                                                        <a href="" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                                    </x-table.td>
                                                </x-table.tr>
                                            @empty
                                                <x-table.empty colspan="5" message="No users found" icon="user" />
                                            @endforelse
                                        </tbody>
                                    </x-table>

                                    <x-table.pagination :paginator="$users" />

                                </div>
                            </div>
                        </div>
                    </main>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
