<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen bg-gray-100 flex flex-col"> {{-- Tambahkan flex flex-col saja di sini --}}
        
        @include('layouts.sidebar')

        <div class="md:ml-64 transition-all duration-300 flex-1 flex flex-col">
            {{-- Navbar Sekarang Tidak Akan Berantakan Lagi --}}
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <div class="text-[#0A78BD]">
                            {{ $header }}
                        </div>
                    </div>
                </header>
            @endisset

            <main class="flex-1"> {{-- Menambah flex-1 agar konten mengisi sisa layar --}}
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
    {{-- <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            
            @include('layouts.sidebar')

        <!-- <div class="ml-64 transition-all duration-300"></div> -->
        <div class="ml-64 transition-all duration-300 ">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body> --}}
</html>
