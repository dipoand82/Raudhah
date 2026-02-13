<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $profil_sekolah->nama_sekolah ?? 'SMP IT Raudhah')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Nunito:wght@600&display=swap" rel="stylesheet">
</head>
<body class="bg-white text-gray-800 font-sans antialiased scroll-smooth">

    @include('partials.navbar-landing') {{-- Kita panggil navbar di sini --}}

    <main>
        @yield('content') {{-- Di sini isi welcome.blade akan muncul --}}
    </main>

    @include('partials.footer-landing') {{-- Kita panggil footer di sini --}}

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 1000,
                once: false,
                mirror: true,
                offset: 100,
            });
        });
    </script>
</body>
</html>
