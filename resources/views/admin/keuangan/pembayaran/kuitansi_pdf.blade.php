<html>

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 10px;
        }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 10px;
        }

        .border-box {
            border: 1px solid #000;
            padding: 15px;
            width: 100%;
            max-width: 550px;
            margin: 0 auto;
        }

        .header-table {
            width: 100%;
            margin-bottom: 5px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .col-text {
            text-align: center;
        }

        .nama-sekolah {
            font-size: 13px;
            font-weight: bold;
        }

        .sub-sekolah {
            font-size: 10px;
            margin-top: 2px;
        }

        .alamat-sekolah {
            font-size: 9px;
            color: #555;
        }

        .line-separator {
            border-bottom: 2px solid #000;
            margin: 10px 0;
            width: 100%;
        }

        .info-table {
            width: 100%;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 1px 0;
        }

        .info-table .label {
            width: 85px;
        }

        .info-table .colon {
            width: 8px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .detail-table td {
            padding: 4px 2px;
            border-bottom: 1px dotted #888;
        }

        .total-section {
            width: 100%;
            margin-top: 10px;
        }

        .total-box {
            background: #f2f2f2;
            padding: 8px;
            border: 1px solid #999;
            font-weight: bold;
        }

        .terbilang {
            font-weight: normal;
            font-style: italic;
            font-size: 9px;
            margin-top: 3px;
        }

        .footer-table {
            width: 100%;
            margin-top: 15px;
        }

        .col-sign {
            text-align: center;
            width: 160px;
        }
    </style>
</head>

<body>
    <div class="border-box">

        <table class="header-table">
            <tr>
                <td width="60">
                    @php
                        $logoPath = null;
                        if (file_exists(public_path('storage/logos/logo_kuitansi.PNG'))) {
                            $logoPath = public_path('storage/logos/logo_kuitansi.PNG');
                        } elseif (file_exists(public_path('storage/logos/logo_smp_raudhah.PNG'))) {
                            $logoPath = public_path('storage/logos/logo_smp_raudhah.PNG');
                        } elseif ($profil_sekolah && $profil_sekolah->logo_path) {
                            $logoPath = public_path('storage/' . $profil_sekolah->logo_path);
                        }
                    @endphp

                    @if ($logoPath)
                        <img src="{{ $logoPath }}" style="width:80px; height:80px; object-fit:contain;">
                    @else
                        <div
                            style="width:50px; height:50px; background:#1072B8; border-radius:50%;
                    display:flex; align-items:center; justify-content:center;
                    color:white; font-size:10px; font-weight:bold;">
                            SMP</div>
                    @endif
                </td>
                <td class="col-text">
                    <div class="nama-sekolah">YAYASAN RAUDHAH EL JANNAH</div>
                    <div class="sub-sekolah">TPQ, TK IT, SD IT, SMP IT Raudhah El Jannah</div>
                    <div class="alamat-sekolah">Jl. Melayu Padang Baru - Lubuk Basung, Kab. Agam</div>
                </td>
                <td width="60"></td>
            </tr>
        </table>

        <div class="line-separator"></div>

        <table class="info-table">
            <tr>
                <td class="label">No. Kuitansi</td>
                <td class="colon">:</td>
                <td>{{ $p->kode_pembayaran }}</td>
            </tr>
            <tr>
                <td class="label">Diterima Dari</td>
                <td class="colon">:</td>
                <td><strong>{{ strtoupper($p->siswa->nama_lengkap) }}</strong></td>


            </tr>
            <tr>
                <td class="label">Untuk Pembayaran </td>
                <td class="colon">:</td>
            </tr>
        </table>

        <table class="detail-table">
            @foreach ($p->detailPembayaran as $idx => $det)
                <tr>
                    <td width="25" align="center">{{ $idx + 1 }}.</td>
                    <td>
                        {{ $det->tagihanSpp->masterTagihan->nama_tagihan ?? '-' }}
                        ({{ $det->tagihanSpp->bulan ?? 'Umum' }} {{ $det->tagihanSpp->tahun }})
                    </td>
                    <td align="right" width="120">Rp {{ number_format($det->nominal_dibayar, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>

        <div class="total-section">
            <div class="total-box">
                TOTAL: Rp {{ number_format($p->total_bayar, 0, ',', '.') }}
                <div class="terbilang">Terbilang: {{ ucwords($terbilang) }} Rupiah</div>
            </div>
        </div>

        <table class="footer-table">
            <tr>
                <td></td>
                <td class="col-sign">
                    Lubuk Basung, {{ $p->created_at->format('d/m/Y') }}<br>
                    Tanda tangan,<br><br><br><br><br>
                    <strong>( {{ strtoupper(auth()->user()->name) }} )</strong>
                </td>
            </tr>
        </table>

    </div>
</body>

</html>
