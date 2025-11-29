<x-toast-container>
    @if (session('success'))
        <x-toast class="bg-green-500/10 border-green-400 text-green-800">
            <x-slot:icon>
                <x-icons.circle-check class="w-5 h-5 text-green-600" />
            </x-slot:icon>
            {{ session('success') }}
        </x-toast>
    @endif

    @if (session('error'))
        <x-toast class="bg-red-500/10 border-red-400 text-red-800">
            <x-slot:icon>
                <x-icons.x-circle class="w-5 h-5 text-red-600" />
            </x-slot:icon>
            {{ session('error') }}
        </x-toast>
    @endif

    @if ($errors->any())
        <x-toast class="bg-red-500/10 border-red-400 text-red-800" timeout="6000">
            <x-slot:icon>
                <x-icons.alert-circle class="w-5 h-5 text-red-600" />
            </x-slot:icon>

            <ul class="list-disc ml-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-toast>
    @endif
</x-toast-container>
