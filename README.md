<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

scroll tabel
<div class="overflow-x-auto bg-white rounded shadow">

Warna #1072B8
#0b3149

sec #313F4B

warna kedua (dari button diambil)
<x-primary-button class="w-full justify-center !bg-[#3B3E42] hover:!bg-[#2f3235] focus:!bg-[#2f3235] active:!bg-[#1f2123]">
    {{ __('Simpan Kelas') }}
</x-primary-button>

atau ada round
<button type="submit" class="w-full !bg-[#3B3E42] focus:!bg-[#2f3235] active:!bg-[#1f2123] text-white font-semibold py-2 px-4 rounded-lg transition duration-200">


notif (ada silang)

before 
<div class="mb-4 bg-green-100 text-green-700 p-4 rounded shadow-sm border border-green-200">
    {{ session('success') }}
</div>

after
<div x-data="{ show: true }" x-show="show" class="mb-4 flex items-center justify-between bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded shadow-sm">
    <span class="text-sm font-medium">{{ session('success') }}</span>
    <button @click="show = false" class="text-green-500 hover:text-green-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
</div>

Button simpan style
 class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest transition duration-150 ease-in-out !bg-[#3B3E42] hover:!bg-[#2f3235] focus:!bg-[#2f3235] active:!bg-[#1f2123] focus:outline-none focus:ring-2 focus:ring-[#3B3E42] focus:ring-offset-2"

 button select modal responsive
 class="block mt-1 w-full max-w-full border-gray-300 rounded-md"

Kontrainer sebelum select agar responsive
 <div class="relative w-full">

 responsive pada tombol
 {{-- 3. Area Tombol --}}
                <div class="mt-8 flex flex-col sm:flex-row justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')" class="justify-center">
                        Batal
                    </x-secondary-button>
                    <button type="submit" 
                        class="inline-flex justify-center ">
                        Simpan Perubahan
                    </button>
                </div>

BAGIAN "flex-col sm:flex-row"
anaknya tambah class="justify-center">

select 1
<option value="">-- Pilih Kelas --</option>


{{ $k->tingkat }} {{ $k->nama_kelas }}

<x-button> Simpan </x-button>
<x-button variant="secondary"> Batal </x-button>

Pass scrol,tabel ga kebawah
class="whitespace-nowrap"

dropdown
 <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="hover:!bg-red-800 hover:!text-white">
{{ __('Log Out') }}
</x-dropdown-link>

layar hape pading good
max-w-7xl mx-auto px-4 sm:px-6

dihapus
lg:px-8

gambar galebar kemanamana
<div class="max-w-[1440px] mx-auto px-4 mt-4">