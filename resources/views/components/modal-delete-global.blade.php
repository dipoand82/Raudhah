@props([
    'trigger', 
    'action', 
    'message', 
    'title' => 'Konfirmasi', 
    'submitText' => 'Proses Sekarang', // Default text jika tidak diisi
    'buttonText' => null // Text tombol awal (opsional)
])

{{-- TOMBOL PEMICU (TRIGGER) --}}
<x-danger-button {{ $attributes }} x-data="" x-on:click.prevent="$dispatch('open-modal', '{{ $trigger }}')">
    {{-- Jika buttonText diisi, pakai itu. Jika tidak, cek slot: kalau kosong 'Hapus', kalau ada isinya 'Proses' --}}
    {{ $buttonText ?? ($slot->isEmpty() ? 'Hapus' : 'Proses') }}
</x-danger-button>

{{-- MODAL --}}
<x-modal name="{{ $trigger }}" focusable>
    <form method="post" action="{{ $action }}" class="p-6 text-left">
        @csrf
        {{-- Logika otomatis: Slot kosong = DELETE, Slot ada isi = POST --}}
        @method($slot->isEmpty() ? 'delete' : 'post') 

        <div class="p-1 sm:p-2">
            <h2 class="text-lg font-bold text-gray-900">{{ $title }}</h2>

            <div class="mt-2 text-sm text-gray-600">
                <p>Apakah Anda yakin ingin <strong> {{ $title }} {{ $message }}</strong>?</p>
                
                {{-- AREA INPUT TAMBAHAN (Dropdown, dll) --}}
                <div class="mt-3">
                    {{ $slot }}
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                
                {{-- TOMBOL EKSEKUSI --}}
                <x-danger-button class="font-semibold text-sm capitalize">
                    {{-- Menggunakan props submitText --}}
                    {{ $submitText }}
                </x-danger-button>
            </div>
        </div>
    </form>
</x-modal>