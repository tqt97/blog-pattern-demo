{{-- resources/views/components/upload/multiple.blade.php --}}

@props([
    'name', // tên field, ví dụ: banners
    'uploadUrl', // route upload
    'deleteUrl' => null, // route delete (nếu null thì không gọi delete)
    'initial' => [], // mảng ảnh ban đầu (URL hoặc [{ path, relative }])
    'maxFiles' => 10,
    'label' => null,
    'hint' => null,
])

<div class="space-y-2">
    @if ($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
            {{ $label }}
        </label>
    @endif

    @if ($hint)
        <p class="text-xs text-gray-500 dark:text-gray-400">
            {{ $hint }}
        </p>
    @endif

    <div x-data="multipleUploader({
        uploadUrl: '{{ $uploadUrl }}',
        deleteUrl: '{{ $deleteUrl }}',
        initial: @json($initial),
        fieldName: '{{ $name }}',
        maxFiles: {{ $maxFiles }}
    })" x-init="init()" class="space-y-3">
        {{-- Dropzone --}}
        <div class="w-full border-2 border-dashed rounded-xl p-4 bg-gray-50 dark:bg-gray-800 cursor-pointer
                   hover:bg-gray-100 dark:hover:bg-gray-700 flex flex-col items-center justify-center text-center"
            @click="$refs.fileInput.click()" @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
            @drop.prevent="handleDrop($event)" :class="{ 'border-gray-900 bg-gray-100 dark:bg-gray-700': dragging }">
            <x-icons.image class="w-8 h-8 text-gray-400 dark:text-gray-500" />
            <p class="text-xs text-gray-600 dark:text-gray-300 mt-2">
                Click to browse or drag & drop images here
            </p>
            <p class="text-[11px] text-gray-400 dark:text-gray-500">
                Max {{ $maxFiles }} images · JPG/PNG/WEBP · &lt; 4MB each
            </p>

            <input type="file" multiple class="hidden" x-ref="fileInput" @change="handleChange">
        </div>
        <p class="text-[11px] text-gray-400">
            Selected: <span x-text="items.length"></span>
        </p>

        {{-- Error --}}
        <p x-show="error" x-text="error" class="text-xs text-red-500"></p>

        {{-- List ảnh --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <template x-for="item in items" :key="item.id">
                <div class="relative border rounded-lg overflow-hidden bg-white dark:bg-gray-900 shadow-sm">
                    <template x-if="item.preview">
                        <img :src="item.preview" class="w-full h-32 object-cover">
                    </template>

                    <template x-if="!item.preview">
                        <div
                            class="w-full h-32 flex items-center justify-center text-xs text-gray-400
                                    bg-gray-100 dark:bg-gray-800">
                            No preview
                        </div>
                    </template>

                    {{-- Overlay loading --}}
                    <div x-show="item.loading"
                        class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center text-xs text-white">
                        <div class="w-20 h-1.5 bg-gray-700 rounded-full overflow-hidden mb-1">
                            <div class="h-full bg-emerald-400" :style="`width: ${item.progress}%;`"></div>
                        </div>
                        <span x-text="item.progress + '%'"></span>
                    </div>

                    {{-- Controls reorder --}}
                    <div class="absolute top-1 left-1 flex gap-1">
                        <button type="button" @click="moveUp(item.id)"
                            class="bg-white/80 hover:bg-white text-[10px] px-1 rounded shadow-sm">
                            ↑
                        </button>
                        <button type="button" @click="moveDown(item.id)"
                            class="bg-white/80 hover:bg-white text-[10px] px-1 rounded shadow-sm">
                            ↓
                        </button>
                    </div>

                    {{-- Remove --}}
                    <button type="button" @click="remove(item.id)"
                        class="absolute top-1 right-1 bg-red-600 text-white text-[10px] px-2 py-0.5 rounded shadow">
                        Remove
                    </button>

                    {{-- Error riêng cho từng ảnh --}}
                    <p x-show="item.error" x-text="item.error"
                        class="absolute bottom-1 left-1 right-1 text-[10px] text-red-500 bg-white/80 px-1 rounded">
                    </p>
                    {{-- Hidden input để submit lên server --}}
                    {{-- <input type="hidden" :name="`{{ $name }}[]`" :value="item.pathRelative || item.path"> --}}
                    <template x-if="item.path || item.pathRelative">
                        <input type="hidden" :name="`{{ $name }}[]`" :value="item.pathRelative || item.path">
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>


{{-- <x-uploader.multiple name="banners" :upload-url="route('upload.image', ['type' => 'banners'])" :delete-url="route('upload.image.destroy', ['type' => 'banners'])" :initial="[]" :max-files="10"
    label="Banner images" hint="These images will be displayed in the homepage slider." /> --}}
