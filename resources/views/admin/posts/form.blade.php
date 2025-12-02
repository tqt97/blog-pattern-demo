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

    <div class="max-w-7xl mx-auto py-2">
        <form method="POST" action="{{ $isEdit ? route('admin.posts.update', $post) : route('admin.posts.store') }}">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif
            <input type="hidden" name="redirect" value="{{ url()->previous() }}">

            <div x-data="{ tab: 'post' }">
                {{-- Tabs --}}
                <div class="border-b border-gray-200 dark:border-gray-700 mb-4 flex gap-4">
                    <button type="button" @click="tab = 'post'"
                        class="px-3 py-2 text-sm font-medium border-b-2 -mb-[1px]"
                        :class="tab === 'post'
                            ?
                            'border-gray-900 text-gray-900 dark:border-gray-100 dark:text-gray-100' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                        Post
                    </button>

                    <button type="button" @click="tab = 'seo'"
                        class="px-3 py-2 text-sm font-medium border-b-2 -mb-[1px]"
                        :class="tab === 'seo'
                            ?
                            'border-gray-900 text-gray-900 dark:border-gray-100 dark:text-gray-100' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                        SEO
                    </button>
                </div>

                <div class="border border-gray-200 dark:border-gray-700 rounded-md p-4 space-y-4">
                    {{-- TAB: POST --}}
                    <div x-show="tab === 'post'" x-cloak>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            {{-- CỘT TRÁI: NỘI DUNG CHÍNH --}}
                            <div class="lg:col-span-2 space-y-4">
                                {{-- Title --}}
                                <div>
                                    <x-input-label for="title" :value="__('Title')" required />
                                    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title"
                                        :value="old('title', $post?->title)" required autofocus />
                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                </div>

                                {{-- Slug --}}
                                <div>
                                    <x-input-label for="slug" :value="__('Slug')" />
                                    <x-text-input id="slug" class="block mt-1 w-full" type="text" name="slug"
                                        :value="old('slug', $post?->slug)" />
                                    <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                                </div>

                                {{-- Excerpt --}}
                                <div>
                                    <x-input-label for="excerpt" :value="__('Excerpt')" required />
                                    <x-textarea id="excerpt" name="excerpt" rows="3" class="mt-1"
                                        placeholder="Short summary of the post...">{{ old('excerpt', $post->excerpt ?? '') }}</x-textarea>
                                    <x-input-error :messages="$errors->get('excerpt')" class="mt-2" />
                                </div>

                                {{-- Content (cho dài hơn chút) --}}
                                <div>
                                    <x-input-label for="content" :value="__('Content')" required />
                                    <x-textarea id="content" name="content" rows="11" class="mt-1"
                                        placeholder="Write your awesome content here...">{{ old('content', $post->content ?? '') }}</x-textarea>
                                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                                </div>
                            </div>

                            {{-- CỘT PHẢI: THUMBNAIL + META --}}
                            <div class="space-y-4">
                                {{-- Thumbnail lên đầu --}}
                                <div>
                                    <x-uploader.single label="Thumbnail" fieldName="thumbnail" :initial="$post->thumbnail ?? null"
                                        :initial-url="$post->thumbnail_url ?? null" uploadUrl="{{ route('upload.image', ['type' => 'posts']) }}"
                                        deleteUrl="{{ route('upload.image.destroy', ['type' => 'posts']) }}" />
                                    {{-- <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" /> --}}

                                    <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                                </div>

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

                                {{-- Tags (multi-select) --}}
                                @php
                                    $oldTags = collect(
                                        old('tag_ids', isset($post) ? $post->tags->pluck('id')->toArray() : []),
                                    );
                                @endphp
                                <div>
                                    <x-input-label for="tag_ids" value="Tags" />
                                    <select name="tag_ids[]" id="tag_ids" multiple
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 min-h-[80px]">
                                        @foreach ($tags as $id => $name)
                                            <option value="{{ $id }}" @selected($oldTags->contains($id))>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                        Hold <kbd>Ctrl</kbd> / <kbd>Cmd</kbd> to select multiple.
                                    </p>
                                    <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                                </div>

                                {{-- Published at + Status trên cùng 1 hàng --}}
                                {{-- <div class="grid grid-cols-1 md:grid-cols-2 gap-3"> --}}
                                <div>
                                    <x-input-label for="published_at" :value="__('Published at')" />
                                    <x-text-input id="published_at" class="block mt-1 w-full" type="datetime-local"
                                        name="published_at" :value="old(
                                            'published_at',
                                            optional($post?->published_at)->format('Y-m-d\TH:i'),
                                        )" />
                                    <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
                                </div>

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
                                {{-- </div> --}}


                            </div>
                        </div>
                    </div>

                    {{-- TAB: SEO --}}
                    <div x-show="tab === 'seo'" x-cloak>
                        <div class="space-y-3">
                            <h3 class="text-sm font-semibold mb-1">SEO</h3>

                            <div>
                                <x-input-label for="meta_title" :value="__('Meta title')" />
                                <x-text-input id="meta_title" class="block mt-1 w-full" type="text" name="meta_title"
                                    :value="old('meta_title', $post?->meta_title)" />
                                <x-input-error :messages="$errors->get('meta_title')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="meta_description" :value="__('Meta description')" />
                                <x-textarea id="meta_description" name="meta_description" rows="4"
                                    class="mt-1">{{ old('meta_description', $post->meta_description ?? '') }}</x-textarea>
                                <x-input-error :messages="$errors->get('meta_description')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end mt-6">
                <x-primary-button class="ms-3">
                    {{ __($buttonText) }}
                </x-primary-button>
            </div>
        </form>
    </div>

</x-app-layout>
