@props([
    'timeout' => 3000, // auto hidden sau 3s
])

@if (session('success'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, {{ $timeout }})"
        x-show="show"
        class="px-4 py-3 mb-4 rounded bg-green-100 text-green-800 border border-green-300"
    >
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, {{ $timeout }})"
        x-show="show"
        class="px-4 py-3 mb-4 rounded bg-red-100 text-red-800 border border-red-300"
    >
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="px-4 py-3 mb-4 rounded bg-red-100 text-red-800 border border-red-300">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
