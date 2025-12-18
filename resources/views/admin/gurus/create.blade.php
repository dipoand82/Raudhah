<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tambah Guru Manual') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm rounded-lg">
                
                <form method="POST" action="{{ route('admin.gurus.store') }}">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama Lengkap Guru')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" required />
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.gurus.index') }}" class="px-4 py-2 text-gray-600">Batal</a>
                        <x-primary-button>{{ __('Simpan Guru') }}</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>