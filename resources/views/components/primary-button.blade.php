{{-- <button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#1072B8] border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-[#0d5a91] focus:bg-[#0d5a91] active:bg-[#09426b] focus:outline-none focus:ring-2 focus:ring-[#1072B8] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button> --}}
<button {{ $attributes->merge([
    'type' => 'submit', 
    'class' => 'inline-flex items-center justify-center gap-3  bg-[#1072B8] hover:bg-[#0d5a91] text-white px-4 py-2 rounded-lg font-semibold text-sm transition shadow-sm whitespace-nowrap capitalize border border-transparent focus:outline-none focus:ring-2 focus:ring-[#1072B8] focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed'
]) }}>
    {{ $slot }}
</button>
{{-- <button {{ $attributes->merge([
    'type' => 'submit', 
    'class' => 'inline-flex items-center justify-center gap-3 px-4 py-3 bg-[#1072B8] hover:bg-[#0A78BD] text-white rounded-lg font-medium text-base transition-colors shadow-md whitespace-nowrap border border-transparent focus:outline-none focus:ring-2 focus:ring-[#1072B8] focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed'
]) }}>
    {{ $slot }}
</button> --}}