@if(session()->has('import_errors'))
    <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm animate-pulse-once">
        <div class="flex items-center gap-2 text-red-800 mb-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-bold text-sm">Beberapa baris gagal diimport:</span>
        </div>
        
        <div class="max-h-48 overflow-y-auto pr-2 custom-scrollbar">
            <ul class="space-y-2">
                @foreach(session()->get('import_errors') as $failure)
                    <li class="text-[11px] text-red-700 bg-white/60 p-3 rounded-lg border border-red-100 shadow-sm">
                        <div class="flex justify-between items-start mb-1">
                            <span class="font-bold uppercase tracking-wider text-[10px] bg-red-100 px-2 py-0.5 rounded">
                                Baris Ke-{{ $failure->row() }}
                            </span>
                        </div>
                        <ul class="list-disc list-inside">
                            @foreach($failure->errors() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        {{-- Opsional: Menampilkan data mana yang menyebabkan error --}}
                        <div class="mt-2 pt-1 border-t border-red-50 text-[10px] text-gray-400 italic">
                            Data Excel: {{ implode(', ', array_filter($failure->values())) }}
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        
        <div class="mt-3 text-[10px] text-red-500 italic">
            *Silakan perbaiki data di file Excel Anda pada baris di atas dan coba upload kembali.
        </div>
    </div>
@endif