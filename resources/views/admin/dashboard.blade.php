<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @push('styles')
        <link
            href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap"
            rel="stylesheet">
        <style>
            :root {
                --blue: #1072B8;
                --blue-dark: #0d5a91;
                --blue-light: #e8f4fd;
                --gold: #f59e0b;
                --gold-light: #fef3c7;
                --red: #ef4444;
                --red-light: #fee2e2;
                --green: #10b981;
                --green-light: #d1fae5;
            }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 16px;
                margin-bottom: 24px;
            }

            @media(max-width:1024px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media(max-width:640px) {
                .stats-grid {
                    grid-template-columns: 1fr;
                }
            }

            .stat-card {
                background: #fff;
                border-radius: 16px;
                padding: 22px;
                border: 1px solid #e5e7eb;
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                transition: box-shadow .2s, transform .2s;
            }

            .stat-card:hover {
                box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
                transform: translateY(-2px);
            }

            .stat-card.blue {
                background: var(--blue);
                color: #fff;
                border-color: var(--blue);
            }

            .stat-card.gold {
                background: var(--gold);
                color: #fff;
                border-color: var(--gold);
            }

            .stat-label {
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .06em;
                opacity: .65;
                margin-bottom: 6px;
            }

            .stat-value {
                font-size: 28px;
                font-weight: 800;
                line-height: 1;
                font-family: 'JetBrains Mono', monospace;
            }

            .stat-value.money {
                font-size: 18px;
            }

            .stat-sub {
                font-size: 11px;
                font-weight: 600;
                margin-top: 6px;
                opacity: .7;
            }

            .stat-sub.up {
                color: var(--green);
            }

            .stat-card.blue .stat-sub,
            .stat-card.gold .stat-sub {
                color: rgba(255, 255, 255, .85);
                opacity: 1;
            }

            .stat-icon {
                width: 44px;
                height: 44px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .stat-icon svg {
                width: 22px;
                height: 22px;
            }

            .icon-blue {
                background: var(--blue-light);
                color: var(--blue);
            }

            .icon-green {
                background: var(--green-light);
                color: var(--green);
            }

            .icon-white {
                background: rgba(255, 255, 255, .2);
                color: #fff;
            }

            .dash-bottom {
                display: grid;
                grid-template-columns: 1fr 380px;
                gap: 20px;
                align-items: start;
            }

            @media(max-width:1100px) {
                .dash-bottom {
                    grid-template-columns: 1fr;
                }
            }

            .dash-card {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 16px;
                overflow: hidden;
                margin-bottom: 20px;
            }

            .dash-card:last-child {
                margin-bottom: 0;
            }

            .dash-card-header {
                padding: 18px 22px;
                border-bottom: 1px solid #f3f4f6;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .dash-card-header h3 {
                font-size: 14px;
                font-weight: 800;
                color: #111827;
            }

            .dash-card-header p {
                font-size: 11px;
                color: #9ca3af;
                margin-top: 2px;
            }

            .btn-outline-sm {
                padding: 6px 14px;
                border-radius: 8px;
                font-size: 11px;
                font-weight: 700;
                background: #fff;
                color: #4b5563;
                border: 1px solid #e5e7eb;
                text-decoration: none;
                transition: background .15s;
            }

            .btn-outline-sm:hover {
                background: #f9fafb;
            }

            .dash-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
            }

            .dash-table thead tr {
                background: #f8fafc;
            }

            .dash-table th {
                padding: 10px 16px;
                text-align: left;
                font-size: 10px;
                font-weight: 700;
                color: #9ca3af;
                text-transform: uppercase;
                letter-spacing: .07em;
                border-bottom: 1px solid #e5e7eb;
            }

            .dash-table td {
                padding: 12px 16px;
                border-bottom: 1px solid #f3f4f6;
                vertical-align: middle;
            }

            .dash-table tr:last-child td {
                border-bottom: none;
            }

            .dash-table tbody tr:hover td {
                background: #f9fafb;
            }

            .student-name {
                font-weight: 700;
                font-size: 12px;
                color: #111827;
            }

            .student-nisn {
                font-size: 10px;
                color: #9ca3af;
                font-family: 'JetBrains Mono', monospace;
                margin-top: 2px;
            }

            .kelas-badge {
                display: inline-flex;
                padding: 3px 8px;
                border-radius: 6px;
                background: var(--blue-light);
                color: var(--blue);
                font-size: 10px;
                font-weight: 700;
            }

            .amount {
                font-family: 'JetBrains Mono', monospace;
                font-weight: 700;
                color: var(--blue);
                font-size: 12px;
            }

            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 3px 8px;
                border-radius: 999px;
                font-size: 9px;
                font-weight: 800;
                text-transform: uppercase;
            }

            .status-lunas {
                background: var(--green-light);
                color: #065f46;
            }

            .status-cicilan {
                background: var(--gold-light);
                color: #92400e;
            }

            .status-belum {
                background: var(--red-light);
                color: #991b1b;
            }

            .prog-section {
                padding: 18px 22px;
            }

            .prog-row {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 14px;
            }

            .prog-label {
                font-size: 11px;
                font-weight: 700;
                width: 52px;
                flex-shrink: 0;
                color: #4b5563;
            }

            .prog-bar {
                flex: 1;
                height: 8px;
                background: #f3f4f6;
                border-radius: 999px;
                overflow: hidden;
            }

            .prog-fill {
                height: 100%;
                border-radius: 999px;
                transition: width .8s ease;
            }

            .prog-fill.blue {
                background: var(--blue);
            }

            .prog-fill.green {
                background: var(--green);
            }

            .prog-fill.gold {
                background: var(--gold);
            }

            .prog-pct {
                font-size: 11px;
                font-weight: 800;
                color: #4b5563;
                width: 36px;
                text-align: right;
            }

            .quick-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                padding: 18px 22px;
            }

            .quick-btn {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 14px;
                border-radius: 12px;
                border: 1.5px solid #e5e7eb;
                background: #fff;
                cursor: pointer;
                font-size: 12px;
                font-weight: 700;
                color: #374151;
                text-align: left;
                text-decoration: none;
                transition: all .15s;
            }

            .quick-btn:hover {
                border-color: var(--blue);
                color: var(--blue);
                background: var(--blue-light);
            }

            .quick-btn svg {
                width: 20px;
                height: 20px;
                flex-shrink: 0;
            }

            .quick-btn .sub {
                font-size: 9px;
                font-weight: 500;
                color: #9ca3af;
                display: block;
                margin-top: 1px;
            }

            .quick-btn:hover .sub {
                color: var(--blue-dark);
                opacity: .7;
            }

            /* Role badge */
            .role-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 4px 12px;
                border-radius: 999px;
                font-size: 10px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .role-admin {
                background: #e8f4fd;
                color: var(--blue);
            }

            .role-guru {
                background: #d1fae5;
                color: #065f46;
            }

            @keyframes fadeUp {
                from {
                    opacity: 0;
                    transform: translateY(12px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .stat-card {
                animation: fadeUp .4s ease both;
            }

            .stat-card:nth-child(1) {
                animation-delay: .05s;
            }

            .stat-card:nth-child(2) {
                animation-delay: .10s;
            }

            .stat-card:nth-child(3) {
                animation-delay: .15s;
            }

            .stat-card:nth-child(4) {
                animation-delay: .20s;
            }

            .dash-card {
                animation: fadeUp .45s ease both;
                animation-delay: .25s;
            }
        </style>
    @endpush

    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ══ GREETING ══ --}}
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h2 class="text-xl font-extrabold text-gray-900">
                            Selamat datang, {{ Auth::user()->name }}
                        </h2>
                        @if (Auth::user()->role === 'admin' || Auth::user()->role === 'guru')
                            <span class="role-badge role-staff">Staff Sekolah</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-400 font-medium">
                        {{ now()->translatedFormat('l, d F Y') }} —
                        Periode aktif: <span
                            class="font-bold text-[#1072B8]">{{ now()->translatedFormat('F Y') }}</span>
                    </p>
                </div>
            </div>
            {{-- Container Tombol dengan Flex Wrap dan Gap --}}
            <div class="flex flex-wrap items-center gap-3 mb-6">
                @can('admin')
                    <a href="{{ route('admin.manajemen-user.index', ['tab' => 'panduan']) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-extrabold uppercase tracking-widest text-yellow-900 bg-gradient-to-r from-yellow-300 to-yellow-500 border border-yellow-300 rounded-full shadow-lg shadow-yellow-500/50 hover:shadow-xl hover:shadow-yellow-500/80 hover:-translate-y-0.5 hover:scale-105 active:scale-95 transition-all duration-300 cursor-pointer w-full sm:w-auto justify-center sm:justify-start">
                        <span class="relative flex h-2.5 w-2.5">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-yellow-100"></span>
                        </span>
                        Lihat Panduan
                    </a>

                    <a href="{{ route('admin.keuangan.tagihan.index') }}"
                        class="inline-flex items-center gap-3 px-5 py-2 text-white bg-gradient-to-r from-blue-500 to-blue-700 border border-blue-400 rounded-full shadow-lg shadow-blue-500/50 hover:shadow-xl hover:shadow-blue-500/80 hover:-translate-y-0.5 hover:scale-105 active:scale-95 transition-all duration-300 cursor-pointer group w-full sm:w-auto justify-center sm:justify-start">
                        <span class="relative flex h-2.5 w-2.5 flex-shrink-0">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-200"></span>
                        </span>
                        <span class="text-xs font-extrabold uppercase tracking-wider">Proses Bayar & Generate Tagihan</span>
                    </a>
                @endcan

                {{-- Tombol Export Laporan (Hijau Muda/Soft) --}}
                <a href="{{ route('admin.keuangan.laporan.export', request()->query()) }}"
                    class="inline-flex items-center gap-3 px-5 py-2 text-emerald-700 bg-gradient-to-r from-emerald-100 to-emerald-200 border border-emerald-300 rounded-full shadow-md shadow-emerald-500/20 hover:shadow-lg hover:shadow-emerald-500/30 hover:-translate-y-0.5 hover:scale-105 active:scale-95 transition-all duration-300 cursor-pointer group w-full sm:w-auto justify-center sm:justify-start">

                    {{-- Titik Putih Bulat Selaras --}}
                    <span class="relative flex h-2.5 w-2.5 flex-shrink-0">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5  bg-emerald-300"></span>
                    </span>

                    <span class="text-xs font-extrabold uppercase tracking-wider">Export Laporan</span>
                </a>

                {{-- Tombol Lihat Laporan (Hijau Tua/Solid) --}}
                <a href="{{ route('admin.keuangan.laporan.index') }}"
                    class="inline-flex items-center gap-3 px-5 py-2 text-white bg-gradient-to-r from-emerald-600 to-emerald-700 border border-emerald-500 rounded-full shadow-lg shadow-emerald-600/30 hover:shadow-xl hover:shadow-emerald-600/50 hover:-translate-y-0.5 hover:scale-105 active:scale-95 transition-all duration-300 cursor-pointer group w-full sm:w-auto justify-center sm:justify-start">

                    {{-- Titik Putih Bulat Selaras --}}
                    <span class="relative flex h-2.5 w-2.5 flex-shrink-0">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>

                    <span class="text-xs font-extrabold uppercase tracking-wider">Lihat Laporan</span>
                </a>
            </div>

            {{-- ══ STAT CARDS ══ --}}
            <div class="stats-grid">

                {{-- Total Siswa (semua role lihat) --}}
                <div class="stat-card">
                    <div>
                        <div class="stat-label">Total Siswa</div>
                        <div class="stat-value">{{ number_format($totalSiswa) }}</div>
                        <div class="stat-sub up">↑ Aktif Tahun Ini</div>
                    </div>
                    <div class="stat-icon icon-blue">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>



                {{-- Total Terkumpul (semua role lihat) --}}
                @can('admin')
                    <div class="stat-card blue">
                        <div>
                            <div class="stat-label">Total Terkumpul</div>
                            <div class="stat-value money">Rp {{ number_format($totalTerkumpul, 0, ',', '.') }}</div>
                            <div class="stat-sub">{{ now()->translatedFormat('F Y') }}</div>
                        </div>
                        <div class="stat-icon icon-white">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                @endcan

                {{-- Belum Bayar (semua role lihat) --}}
                <div class="stat-card gold">
                    <div>
                        <div class="stat-label">Belum Bayar</div>
                        <div class="stat-value">{{ number_format($belumBayar) }}</div>
                        <div class="stat-sub">Siswa pending</div>
                    </div>
                    <div class="stat-icon icon-white">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                {{-- Transaksi Hari Ini (semua role lihat) --}}
                <div class="stat-card">
                    <div>
                        <div class="stat-label">Transaksi Hari Ini</div>
                        <div class="stat-value">{{ number_format($transaksiHariIni) }}</div>
                        <div class="stat-sub up">
                            @if ($selisihTransaksi >= 0)
                                ↑ {{ $selisihTransaksi }} dari kemarin
                            @else
                                ↓ {{ abs($selisihTransaksi) }} dari kemarin
                            @endif
                        </div>
                    </div>
                    <div class="stat-icon icon-green">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                </div>

            </div>

            {{-- ══ BOTTOM GRID ══ --}}
            <div class="dash-bottom">

                {{-- KIRI: Tabel Transaksi Terbaru --}}
                <div>
                    <div class="dash-card">

                        <div class="dash-card-header">
                            <div>
                                <h3>Transaksi Terbaru</h3>
                                <p>Pembayaran yang baru diproses</p>
                            </div>
                            @can('admin')
                                <a href="{{ route('admin.keuangan.pembayaran.index') }}" class="btn-outline-sm">
                                    Lihat Semua
                                </a>
                            @endcan

                        </div>

                        <div class="overflow-x-auto">
                            <table class="dash-table">
                                <thead>
                                    <tr>
                                        <th>Siswa</th>
                                        <th>Kelas</th>
                                        <th>Tagihan</th>
                                        <th>Nominal</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transaksiTerbaru as $p)
                                        @php
                                            $kelas = $p->detailPembayaran->first()?->tagihanSpp?->riwayatAkademik
                                                ?->kelas;
                                            $namaTagihan = $p->detailPembayaran
                                                ->map(fn($d) => $d->tagihanSpp?->masterTagihan?->nama_tagihan)
                                                ->filter()
                                                ->unique()
                                                ->first();
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="student-name">{{ $p->siswa->nama_lengkap }}</div>
                                                <div class="student-nisn">{{ $p->siswa->nisn ?? '-' }}</div>
                                            </td>
                                            <td>
                                                @if ($kelas)
                                                    <span
                                                        class="kelas-badge">{{ $kelas->tingkat }}{{ $kelas->nama_kelas }}</span>
                                                @else
                                                    <span class="text-gray-300 text-xs">-</span>
                                                @endif
                                            </td>
                                            <td class="text-xs text-gray-600">{{ $namaTagihan ?? '-' }}</td>
                                            <td><span class="amount">Rp
                                                    {{ number_format($p->total_bayar, 0, ',', '.') }}</span></td>
                                            <td><span class="status-badge status-lunas">✓ Lunas</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5"
                                                class="px-6 py-12 text-center text-gray-400 italic text-sm">
                                                Belum ada transaksi hari ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- KANAN: Progress SPP Per Kelas --}}
                <div>
                    <div class="dash-card">
                        <div class="dash-card-header">
                            <div>
                                <h3>Progres SPP Bulan {{ now()->translatedFormat('F') }}</h3>
                                <p>Status pelunasan siswa per tingkat kelas</p>
                            </div>
                        </div>

                        <div class="prog-section" style="padding-top: 10px;">
                            @foreach ($progressPerKelas as $prog)
                                <div class="prog-row" style="margin-bottom: 20px;">
                                    {{-- Label & Info Angka --}}
                                    <div class="flex justify-between items-end mb-2">
                                        <div>
                                            <span
                                                class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas
                                                {{ $prog['tingkat'] }}</span>
                                            <div class="flex items-baseline gap-1">
                                                <span
                                                    class="text-lg font-bold text-gray-800">{{ $prog['sudah_lunas'] }}</span>
                                                <span class="text-xs text-gray-400">/ {{ $prog['jumlah_siswa'] }}
                                                    Siswa_</span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span
                                                class="text-sm font-black {{ $prog['persen'] == 100 ? 'text-green-500' : 'text-blue-600' }}">
                                                {{ $prog['persen'] }}%
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Progress Bar Container --}}
                                    <div class="prog-bar"
                                        style="width: 100%; height: 10px; background-color: #f3f4f6; border-radius: 20px; overflow: hidden; border: 1px solid #e5e7eb;">
                                        {{-- Isi Bar (Warna Dinamis) --}}
                                        <div class="prog-fill {{ $loop->iteration == 1 ? 'blue' : ($loop->iteration == 2 ? 'green' : 'gold') }}"
                                            style="width: {{ $prog['persen'] }}%; height: 100%; border-radius: 20px; transition: width 0.8s ease-out;">
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Footer Info --}}
                            <div style="margin-top: 10px; padding-top: 12px; border-top: 1px solid #f3f4f6;">
                                <p style="font-size: 10px; color: #9ca3af; line-height: 1.4;">
                                    <i class="fas fa-info-circle mr-1"></i> Data di atas menunjukkan jumlah siswa yang
                                    memiliki setidaknya satu tagihan SPP berstatus <strong>Lunas</strong> pada bulan
                                    {{ now()->translatedFormat('F Y') }}.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
