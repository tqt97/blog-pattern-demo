@php
    use App\Enums\PostStatus;

    $isEdit = isset($post) && $post?->exists;
    $title = $isEdit ? 'Update Post' : 'Create Post';
    $buttonText = $isEdit ? 'Update' : 'Create';

    $statusOptions = PostStatus::options(); // ['draft' => 'Draft', ...]
    $currentStatus = old('status', $post?->status instanceof PostStatus ? $post->status->value : $post?->status);
@endphp

<x-app-layout>
    <x-slot name="header">
        <x-pages.header title="{{ $title }}" action="{{ route('admin.posts.index') }}" buttonText="Back"
            icon="arrow-left" />
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <main class="max-w-7xl mx-auto py-2">
                        <form method="POST"
                            action="{{ $isEdit ? route('admin.posts.update', $post) : route('admin.posts.store') }}">
                            @csrf
                            @if ($isEdit)
                                @method('PUT')
                            @endif

                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                {{-- CỘT TRÁI: THÔNG TIN CHÍNH --}}
                                <div class="lg:col-span-2 space-y-4">
                                    {{-- Title --}}
                                    <div>
                                        <x-input-label for="title" :value="__('Title')" required />
                                        <x-text-input id="title" class="block mt-1 w-full" type="text"
                                            name="title" :value="old('title', $post?->title)" required autofocus />
                                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                    </div>

                                    {{-- Slug --}}
                                    <div>
                                        <x-input-label for="slug" :value="__('Slug')" />
                                        <x-text-input id="slug" class="block mt-1 w-full" type="text"
                                            name="slug" :value="old('slug', $post?->slug)" />
                                        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                                    </div>

                                    {{-- Excerpt --}}
                                    <div>
                                        <x-input-label for="excerpt" :value="__('Excerpt')" />
                                        <x-textarea id="excerpt" name="excerpt" rows="3" class="mt-1"
                                            placeholder="Short summary of the post...">{{ old('excerpt', $post->excerpt ?? '') }}</x-textarea>
                                        <x-input-error :messages="$errors->get('excerpt')" class="mt-2" />
                                    </div>

                                    {{-- Content --}}
                                    <div>
                                        <x-input-label for="content" :value="__('Content')" required />
                                        <x-textarea id="content" name="content" rows="10" class="mt-1"
                                            placeholder="Write your awesome content here...">{{ old('content', $post->content ?? '') }}</x-textarea>
                                        <x-input-error :messages="$errors->get('content')" class="mt-2" />
                                    </div>
                                </div>

                                {{-- CỘT PHẢI: META, STATUS, SEO --}}
                                <div class="space-y-4">
                                    {{-- Category --}}
                                    <div>
                                        <x-input-label for="category_id" :value="__('Category')" />
                                        <select id="category_id" name="category_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900">
                                            <option value="">-- No category --</option>
                                            @foreach ($categories as $id => $name)
                                                <option value="{{ $id }}" @selected(old('category_id', $post?->category_id) == $id)>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                    </div>

                                    {{-- Status --}}
                                    <div>
                                        <x-input-label for="status" :value="__('Status')" />
                                        <select id="status" name="status"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900">
                                            @foreach ($statusOptions as $value => $label)
                                                <option value="{{ $value }}" @selected($currentStatus === $value)>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                    </div>

                                    {{-- Published at --}}
                                    <div>
                                        <x-input-label for="published_at" :value="__('Published at')" />
                                        <x-text-input id="published_at" class="block mt-1 w-full" type="datetime-local"
                                            name="published_at" :value="old(
                                                'published_at',
                                                optional($post?->published_at)->format('Y-m-d\TH:i'),
                                            )" />
                                        <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
                                    </div>

                                    {{-- Thumbnail (URL/path) --}}
                                    <x-uploader.single label="Thumbnail" fieldName="thumbnail" :initial="$post->thumbnail"
                                        uploadUrl="{{ route('upload.image', ['type' => 'posts']) }}"
                                        deleteUrl="{{ route('upload.image.destroy', ['type' => 'posts']) }}" />

                                    {{-- SEO --}}
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-md p-3 space-y-3">
                                        <h3 class="text-sm font-semibold">SEO</h3>

                                        <div>
                                            <x-input-label for="meta_title" :value="__('Meta title')" />
                                            <x-text-input id="meta_title" class="block mt-1 w-full" type="text"
                                                name="meta_title" :value="old('meta_title', $post?->meta_title)" />
                                            <x-input-error :messages="$errors->get('meta_title')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="meta_description" :value="__('Meta description')" />
                                            <x-textarea id="meta_description" name="meta_description" rows="3"
                                                class="mt-1">{{ old('meta_description', $post->meta_description ?? '') }}</x-textarea>
                                            <x-input-error :messages="$errors->get('meta_description')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6">
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
