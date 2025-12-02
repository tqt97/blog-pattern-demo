@props([
    'label' => 'Thumbnail',
    'fieldName' => 'thumbnail',
    'initial' => null, // value in DB (relative path or full URL)
    'initialUrl' => null, // full URL (use accessor thumbnail_url)
    'uploadUrl',
    'deleteUrl' => null,
])


<div x-data="singleUploader({
    uploadUrl: '{{ $uploadUrl }}',
    deleteUrl: '{{ $deleteUrl }}',
    fieldName: '{{ $fieldName }}',
})" class="space-y-2">
    <x-input-label :value="$label" />

    {{-- Current thumbnail (server render) --}}
    @if ($initialUrl)
        <div class="mb-2">
            <img src="{{ $initialUrl }}" class="w-full rounded-lg border shadow max-h-64 object-cover">
            <p class="text-[11px] text-gray-500 mt-1">
                Current thumbnail. Upload a new image to replace it.
            </p>
        </div>
    @endif

    {{-- Preview --}}
    <template x-if="preview">
        <div class="relative">
            <img :src="preview" class="w-full rounded-lg border shadow max-h-64 object-cover">

            <button type="button" @click="remove"
                class="absolute top-2 right-2 bg-red-600 text-white text-xs px-2 py-1 rounded">
                Remove
            </button>

            <p x-show="error" x-text="error"
                class="absolute bottom-1 left-1 right-1 text-[10px] text-red-500 bg-white/80 px-1 rounded">
            </p>

            <div x-show="loading"
                class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center text-xs text-white">
                <div class="w-24 h-1.5 bg-gray-700 rounded-full overflow-hidden mb-1">
                    <div class="h-full bg-emerald-400" :style="`width: ${progress}%;`"></div>
                </div>
                <span x-text="progress + '%'"></span>
            </div>
        </div>
    </template>

    {{-- Shimmer --}}
    <template x-if="!preview && loading">
        <div class="h-56 rounded-xl bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 animate-pulse"></div>
    </template>

    {{-- Dropzone --}}
    <template x-if="!preview && !loading">
        <div class="w-full h-44 border-2 border-dashed rounded-xl flex flex-col justify-center items-center bg-gray-50/50 cursor-pointer hover:bg-gray-100 transition"
            @click="$refs.input.click()" @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
            @drop.prevent="handleDrop($event)" :class="{ 'border-gray-900 bg-gray-100': dragging }">
            <x-icons.image class="w-10 h-10 text-gray-400" />
            <span class="text-sm text-gray-500 mt-2">Click or drop image here</span>
        </div>
    </template>

    <input type="file" x-ref="input" class="hidden" accept="image/*" @change="handleChange">

    <input type="hidden" name="{{ $fieldName }}" value="{{ $initial ?? '' }}"
        :value="(pathRelative || path) || '{{ $initial ?? '' }}'">

    <p x-show="error" class="text-red-600 text-xs" x-text="error"></p>
</div>
