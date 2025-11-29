@props([
    'name' => 'per_page',
    'options' => [5, 10, 15, 25, 50, 100],
    'default' => 15,
])

<x-filters.form :except="[$name]" class="flex items-center gap-2">
    <div class="flex gap-1 items-center justify-center">
        Show:
        <select name="{{ $name }}"
            class="w-[80px] rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
            onchange="this.form.submit()">
            @foreach ($options as $size)
                <option value="{{ $size }}" @selected(request($name, $default) == $size)>
                    {{ $size }}
                </option>
            @endforeach
        </select>
    </div>
</x-filters.form>
