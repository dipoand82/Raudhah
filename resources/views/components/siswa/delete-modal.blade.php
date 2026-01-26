@props(['trigger', 'action', 'message'])

{{-- 1. Tombol Trigger (INI YANG MEMBUAT TOMBOL MUNCUL DI TABEL) --}}
<x-danger-button x-data="" 
        x-on:click.prevent="$dispatch('open-modal', '{{ $trigger }}')" 
        >
    Hapus
</x-danger-button>
{{-- 2. Modal Konfirmasinya --}}
<x-modal name="{{ $trigger }}" focusable>
    <form method="post" action="{{ $action }}" class="p-6 text-left">
        @csrf
        @method('delete')
        <div class="p-1 sm:p-2 text-left"> {{-- Padding lebih kecil di HP (p-4) --}}
            <h2 class="text-lg font-bold text-gray-900">
                Konfirmasi Hapus
            </h2>
            <div class="mt-2 text-sm text-gray-600 leading-relaxed"> {{-- Gunakan <div> sebagai pembungkus utama --}}
                <p class="mt-1 text-sm text-gray-600 whitespace-normal break-words">
                    Apakah Anda yakin ingin menghapus data siswa 
                    <strong>{{ $message }}</strong>?
                    <br>
                    <span class="text-red-500 text-xs">*Data yang dihapus tidak dapat dikembalikan.</span>
                </p>
            </div>
            <div class="mt-6 flex sm:flex-row lg:w-auto justify-end gap-3 whitespace-normal break-words">
                {{-- flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto justify-end --}}
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <x-danger-button class="ml-3">
                    Ya, Hapus Permanen
                </x-danger-button>
            </div>
        </div>
    </form>
</x-modal>