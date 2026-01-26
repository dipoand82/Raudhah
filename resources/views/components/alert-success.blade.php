@props(['timeout' => 5000])

<div x-data="{ show: true }" 
     x-init="setTimeout(() => show = false, {{ $timeout }})" 
     x-show="show" 
     x-transition:leave="transition ease-in duration-500"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     {{-- 
        PERUBAHAN WARNA:
        - border-green-800 -> border-green-600 (Hijau lebih cerah/segar)
        - text-green-800   -> text-green-700 (Teks hijau tua yang lebih lembut)
     --}}
     class="mb-4 flex items-center justify-between bg-green-100 border-2 border-green-600 text-green-700 px-4 py-2.5 rounded-lg shadow-md relative">
    
    <div class="flex items-center gap-3">
        {{-- 
           PERUBAHAN ICON CEKLIS:
           - text-green-800 -> text-green-600 (Menyamakan dengan warna border)
        --}}
        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        
        {{-- Pesan teks --}}
        <span class="text-sm font-bold">{{ $slot }}</span>
    </div>

    {{-- 
       PERUBAHAN TOMBOL SILANG:
       - text-green-800 -> text-green-600 (Hijau segar)
       - hover:text-green-950 -> hover:text-green-800 (Efek hover tidak terlalu hitam)
    --}}
    <button @click="show = false" 
            class="text-green-600 hover:text-green-800 transition duration-150 flex items-center justify-center">
        <span class="text-3xl leading-none">&times;</span>
    </button>
</div>