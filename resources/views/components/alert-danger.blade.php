@props(['timeout' => 5000])

<div x-data="{ show: true }" 
     x-init="setTimeout(() => show = false, {{ $timeout }})" 
     x-show="show" 
     x-transition:leave="transition ease-in duration-500"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     {{-- Desain: Background merah muda, Border merah pekat, Shadow halus --}}
     class="mb-4 flex items-center justify-between bg-red-100 border-2 border-red-500 text-red-800 px-4 py-2.5 rounded-lg shadow-md relative">
    
    <div class="flex items-center gap-3">
        {{-- Ikon Tanda Seru dalam Lingkaran --}}
        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        
        {{-- Pesan Error masuk ke sini via Slot --}}
        <span class="text-sm font-bold">{{ $slot }}</span>
    </div>

    {{-- Tombol Silang Besar --}}
    <button @click="show = false" 
            class="text-red-500 hover:text-red-850 transition duration-150 flex items-center justify-center">
        <span class="text-3xl leading-none">&times;</span>
    </button>
</div>