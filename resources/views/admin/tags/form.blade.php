@php
    $isEdit = isset($tag) && $tag?->exists;
    $title = $isEdit ? 'Update' : 'Create';
    $buttonText = $isEdit ? 'Update' : 'Create';
@endphp

<x-app-layout>
    <x-slot name="header">
        <x-pages.header title="{{ $title }}" action="{{ route('admin.tags.index') }}" buttonText="Back"
            icon="arrow-left" />
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <main class="max-w-7xl mx-auto py-2">
                        <form method="POST"
                            action="{{ $isEdit ? route('admin.tags.update', $tag) : route('admin.tags.store') }}">
                            @csrf
                            @if ($isEdit)
                                @method('PUT')
                            @endif

                            <div class="mb-4">
                                <x-input-label for="name" :value="__('Name')" required
                                    hint="Tối đa 255 ký tự, không chứa ký tự đặc biệt" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                    :value="old('name', $tag?->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="slug" :value="__('Slug')" />
                                <x-text-input id="slug" class="block mt-1 w-full" type="text" name="slug"
                                    :value="old('slug', $tag?->slug)" />
                                <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">

                                <x-primary-button class="ms-3">
                                    {{ __($buttonText) }}
                                </x-primary-button>
                            </div>
                        </form>
                    </main>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
