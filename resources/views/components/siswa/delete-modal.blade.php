@props(['trigger', 'action', 'message'])

<x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', '{{ $trigger }}')">
    Hapus
</x-danger-button>
<x-modal name="{{ $trigger }}" focusable>
    <form method="post" action="{{ $action }}" class="p-6 text-left">
        @csrf
        @method('delete')
        <div class="p-1 sm:p-2 text-left">
            <h2 class="text-lg font-bold text-gray-900">
                Konfirmasi Hapus
            </h2>
            <div class="mt-2 text-sm text-gray-600 leading-relaxed">
                <p class="mt-1 text-sm text-gray-600 whitespace-normal break-words">
                    Apakah Anda yakin ingin menghapus data siswa
                    <strong>{{ $message }}</strong>?
                    <br>
                    <span class="text-red-500 text-xs">*Data yang dihapus tidak dapat dikembalikan.</span>
                </p>
            </div>
            <div class="mt-6 flex sm:flex-row lg:w-auto justify-end gap-3 whitespace-normal break-words">
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
