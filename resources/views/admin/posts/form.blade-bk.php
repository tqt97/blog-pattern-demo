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
                                    {{-- Thumbnail Upload --}}
                                    {{-- <div x-data="imageUploader('{{ old('thumbnail', $post->thumbnail ?? '') }}')" class="space-y-2">
                                        <x-input-label for="thumbnail" :value="__('Thumbnail')" />

                                        <!-- Image preview box -->
                                        <div class="relative w-full">
                                            <template x-if="preview">
                                                <div class="relative">
                                                    <img :src="preview"
                                                        class="w-full rounded-lg shadow border border-gray-200 dark:border-gray-700 object-cover max-h-64">

                                                    <!-- Remove Button -->
                                                    <button type="button"
                                                        class="absolute top-2 right-2 bg-red-600 text-white text-xs px-2 py-1 rounded hover:bg-red-700"
                                                        @click="remove()">
                                                        Remove
                                                    </button>
                                                </div>
                                            </template>

                                            <!-- Placeholder if no image -->
                                            <template x-if="!preview">
                                                <div class="w-full h-48 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 cursor-pointer hover:border-gray-400 hover:bg-gray-100 transition"
                                                    @click="$refs.thumbnail.click()">
                                                    <x-icons.image class="w-10 h-10 text-gray-400 mb-2" />
                                                    <span class="text-sm text-gray-500">Click to upload or drag &
                                                        drop</span>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Input hidden -->
                                        <input type="file" x-ref="thumbnail" name="thumbnail" id="thumbnail"
                                            accept="image/*" class="hidden" @change="fileChosen">

                                        <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                                    </div> --}}

                                    {{-- Thumbnail Upload Pro --}}
                                    <div x-data="imageUploader('{{ old('thumbnail', $post->thumbnail ?? '') }}')" class="space-y-2">

                                        <x-input-label for="thumbnail" :value="__('Thumbnail')" />

                                        <!-- Dropzone -->
                                        <div class="relative w-full" x-bind:class="{ 'opacity-50': loading }">
                                            <!-- Preview -->
                                            <template x-if="preview && !loading">
                                                <div class="relative">
                                                    <img :src="preview"
                                                        class="w-full rounded-lg shadow border border-gray-200 dark:border-gray-700 object-cover max-h-64">

                                                    <!-- Remove -->
                                                    <button type="button"
                                                        class="absolute top-2 right-2 bg-red-600 text-white text-xs px-2 py-1 rounded hover:bg-red-700"
                                                        @click="remove">
                                                        Remove
                                                    </button>
                                                </div>
                                            </template>

                                            <!-- Loading shimmer -->
                                            <template x-if="loading">
                                                <div
                                                    class="w-full h-48 rounded-lg bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 animate-pulse">
                                                </div>
                                            </template>

                                            <!-- Dropzone placeholder -->
                                            <template x-if="!preview && !loading">
                                                <div class="w-full h-48 flex flex-col items-center justify-center border-2 border-dashed rounded-lg cursor-pointer transition
                       bg-gray-50 hover:bg-gray-100 hover:border-gray-400"
                                                    @click="$refs.thumbnail.click()" @dragover.prevent="dragOver = true"
                                                    @dragleave.prevent="dragOver = false"
                                                    @drop.prevent="handleDrop($event)"
                                                    :class="{ 'border-gray-900 bg-gray-100': dragOver }">
                                                    <x-icons.image class="w-10 h-10 text-gray-400 mb-2" />
                                                    <span class="text-sm text-gray-500">Drop image here or click to
                                                        upload</span>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Hidden file input -->
                                        <input type="file" x-ref="thumbnail" name="thumbnail" id="thumbnail"
                                            accept="image/png,image/jpeg,image/webp" class="hidden"
                                            @change="fileChosen">

                                        <!-- Error -->
                                        <p x-show="error" class="text-red-600 text-xs" x-text="error"></p>

                                        <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                                    </div>


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
    {{-- <script>
        function imageUploader(initialUrl = null) {
            return {
                preview: initialUrl,

                fileChosen(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = e => {
                        this.preview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                remove() {
                    this.preview = null;
                    this.$refs.thumbnail.value = null;
                }
            }
        }
    </script> --}}

    <script>
        function imageUploader(initialUrl = null) {
            return {
                preview: initialUrl,
                loading: false,
                dragOver: false,
                error: null,
                maxSize: 2 * 1024 * 1024, // 2 MB

                validate(file) {
                    this.error = null;

                    // Kiểm tra loại ảnh
                    const allowed = ['image/jpeg', 'image/png', 'image/webp'];
                    if (!allowed.includes(file.type)) {
                        this.error = 'Only JPG, PNG, WEBP images are allowed.';
                        return false;
                    }

                    // Kiểm tra kích thước
                    if (file.size > this.maxSize) {
                        this.error = 'Image must be under 2MB.';
                        return false;
                    }

                    return true;
                },

                handleDrop(event) {
                    this.dragOver = false;
                    const file = event.dataTransfer.files[0];
                    if (file && this.validate(file)) {
                        this.startPreview(file);
                        this.$refs.thumbnail.files = event.dataTransfer.files;
                    }
                },

                fileChosen(event) {
                    const file = event.target.files[0];
                    if (file && this.validate(file)) {
                        this.startPreview(file);
                    }
                },

                startPreview(file) {
                    this.loading = true;

                    const reader = new FileReader();
                    reader.onload = e => {
                        setTimeout(() => {
                            this.preview = e.target.result;
                            this.loading = false;
                        }, 400); // delay để thấy shimmer
                    };
                    reader.readAsDataURL(file);
                },

                remove() {
                    this.preview = null;
                    this.$refs.thumbnail.value = null;
                    this.error = null;
                }
            }
        }
    </script>

</x-app-layout>
