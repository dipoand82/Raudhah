<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 10px; } 
        
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

        /* HEADER & GARIS TENGAH */
        .header-table {
            width: 100%;
            margin-bottom: 5px; /* Kurangi margin bawah karena ada garis tengah */
        }
        .header-table td { vertical-align: middle; }
        .col-text {
            text-align: center; /* Mengetengahkan tulisan */
        }
        .nama-sekolah { font-size: 13px; font-weight: bold; }
        .sub-sekolah { font-size: 10px; margin-top: 2px; }
        .alamat-sekolah { font-size: 9px; color: #555; }

        /* Garis Tengah seperti di Header */
        .line-separator {
            border-bottom: 2px solid #000;
            margin: 10px 0;
            width: 100%;
        }

        .info-table { width: 100%; margin-bottom: 10px; }
        .info-table td { padding: 1px 0; }
        .info-table .label { width: 85px; }
        .info-table .colon { width: 8px; }

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
        .terbilang { font-weight: normal; font-style: italic; font-size: 9px; margin-top: 3px; }

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

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td width="60">
                <img src="{{ public_path('storage/logos/brosur.png') }}" style="width: 50px; height: 50px;">
            </td>
            <td class="col-text">
                <div class="nama-sekolah">YAYASAN RAUDHAH EL JANNAH</div>
                <div class="sub-sekolah">TPQ, TK IT, SD IT, SMP IT Raudhah El Jannah</div>
                <div class="alamat-sekolah">Jl. Melayu Padang Baru - Lubuk Basung, Kab. Agam</div>
            </td>
            <td width="60"></td> {{-- Spacer agar teks benar-benar di tengah --}}
        </tr>
    </table>

    {{-- GARIS TENGAH (SEPARATOR) --}}
    <div class="line-separator"></div>

    {{-- INFO --}}
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
    </table>

    {{-- TABEL DETAIL --}}
    <table class="detail-table">
        @foreach($p->detailPembayaran as $idx => $det)
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

    {{-- TOTAL --}}
    <div class="total-section">
        <div class="total-box">
            TOTAL: Rp {{ number_format($p->total_bayar, 0, ',', '.') }}
            <div class="terbilang">Terbilang: {{ ucwords($terbilang) }} Rupiah</div>
        </div>
    </div>

    {{-- FOOTER --}}
    <table class="footer-table">
        <tr>
            <td></td>
            <td class="col-sign">
                Lubuk Basung, {{ $p->created_at->format('d/m/Y') }}<br>
                Penerima,<br><br><br><br><br>
                <strong>( {{ strtoupper(auth()->user()->name) }} )</strong>
            </td>
        </tr>
    </table>

</div>
</body>
</html>