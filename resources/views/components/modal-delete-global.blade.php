@props([
    'trigger',
    'action',
    'message',
    'title' => 'Konfirmasi',
    'submitText' => 'Proses Sekarang',
    'buttonText' => null,
    'type' => 'danger',
])

{{-- 1. TOMBOL PEMICU (Yang ada di tabel) --}}
@if($type === 'warning')
    {{-- Pakai button biasa agar bisa warna kuning --}}
    <button {{ $attributes->merge(['class' => 'p-1 text-yellow-600 hover:text-yellow-700 transition']) }}
            x-data="" x-on:click.prevent="$dispatch('open-modal', '{{ $trigger }}')">
        {{ $buttonText ?? $slot }}
    </button>
@else
    {{-- Tetap pakai danger-button untuk hapus data --}}
    <x-danger-button {{ $attributes }} x-data="" x-on:click.prevent="$dispatch('open-modal', '{{ $trigger }}')">
        {{ $buttonText ?? ($slot->isEmpty() ? 'Hapus' : 'Proses') }}
    </x-danger-button>
@endif

{{-- 2. STRUKTUR MODAL --}}
<x-modal name="{{ $trigger }}" focusable>
    <form method="post" action="{{ $action }}" class="p-6 text-left">
        @csrf
        {{-- Jika warning biasanya edit data (post/patch), jika danger biasanya delete --}}
        @method($type === 'warning' ? 'post' : 'delete')

        <div class="p-1 sm:p-2">
            <h2 class="text-lg font-bold text-gray-900">{{ $title }}</h2>

            <div class="mt-2 text-sm text-gray-600">
                <p>Apakah Anda yakin ingin <strong> {{ $title }} {{ $message }}</strong>?</p>
                <div class="mt-3">
                    {{ $slot }}
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>

                {{-- 3. TOMBOL EKSEKUSI DI DALAM MODAL --}}
                @if($type === 'warning')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white capitalize tracking-widest hover:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                        {{ $submitText }}
                    </button>
                @else
                    <x-danger-button class="font-semibold text-sm capitalize">
                        {{ $submitText }}
                    </x-danger-button>
                @endif
            </div>
        </div>
    </form>
</x-modal>
