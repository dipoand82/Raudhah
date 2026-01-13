<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-blue-600 to-blue-400 rounded-lg shadow-lg">
            <h2 class="font-bold text-2xl text-white leading-tight py-4 px-6">
                {{ __('Edit User') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8">

                    {{-- Form Start: Perhatikan route menggunakan admin.users.update dan mengirim ID --}}
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- PENTING: Untuk update data --}}

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Nama Lengkap --}}
                            <div class="col-span-1 md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="name" id="name" 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200 shadow-sm @error('name') border-red-500 @enderror"
                                    value="{{ old('name', $user->name) }}" required> {{-- Value mengambil data lama --}}
                                
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-span-1 md:col-span-2">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" name="email" id="email" 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200 shadow-sm @error('email') border-red-500 @enderror"
                                    value="{{ old('email', $user->email) }}" required>
                                
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Divider / Pemisah --}}
                            <div class="col-span-1 md:col-span-2 border-t border-gray-200 my-2 pt-4">
                                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Ubah Password (Opsional)</h3>
                                <p class="text-xs text-gray-400 mb-4">Biarkan kosong jika tidak ingin mengubah password user ini.</p>
                            </div>

                            {{-- Password --}}
                            <div class="col-span-1">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                                <input type="password" name="password" id="password" 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200 shadow-sm @error('password') border-red-500 @enderror"
                                    placeholder="Isi hanya jika ingin ganti">
                                
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Konfirmasi Password --}}
                            <div class="col-span-1">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200 shadow-sm"
                                    placeholder="Ulangi password baru">
                            </div>

                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-end mt-8 space-x-4">
                            {{-- Tombol Batal --}}
                            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded-lg hover:bg-gray-100 transition duration-200">
                                Batal
                            </a>

                            {{-- Tombol Update --}}
                            <button type="submit" class="bg-gradient-to-r from-green-500 to-green-700 hover:from-green-600 hover:to-green-800 text-white font-bold py-2 px-6 rounded-lg shadow-lg transform transition hover:scale-105 duration-300 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Perbarui Data
                            </button>
                        </div>

                    </form>
                    {{-- Form End --}}

                </div>
            </div>
        </div>
    </div>
</x-app-layout>