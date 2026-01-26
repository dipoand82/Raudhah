@props(['trigger', 'action', 'message', 'title' => 'Konfirmasi'])

<x-danger-button {{ $attributes }} x-data="" x-on:click.prevent="$dispatch('open-modal', '{{ $trigger }}')">
    {{ $slot->isEmpty() ? 'Hapus' : 'Proses' }} {{-- Tulisan tombol otomatis berubah --}}
</x-danger-button>

<x-modal name="{{ $trigger }}" focusable>
    <form method="post" action="{{ $action }}" class="p-6 text-left">
        @csrf
        @method($slot->isEmpty() ? 'delete' : 'post') {{-- Jika ada slot (kelulusan), gunakan POST. Jika kosong (hapus), gunakan DELETE --}}

        <div class="p-1 sm:p-2">
            <h2 class="text-lg font-bold text-gray-900">{{ $title }}</h2>

            <div class="mt-2 text-sm text-gray-600">
                <p>Apakah Anda yakin ingin memproses <strong>{{ $message }}</strong>?</p>
                
                {{-- INI TEMPAT INPUT TAMBAHAN (Dropdown dll) --}}
                <div class="mt-3">
                    {{ $slot }}
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-danger-button>Proses Sekarang</x-danger-button>
            </div>
        </div>
    </form>
</x-modal>