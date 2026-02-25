<table>
    <thead>
        <tr>
            <th rowspan="2" style="font-weight:bold; text-align:center; background-color:#D9D9D9; color:#333333;">NO</th>
            <th rowspan="2" style="font-weight:bold; text-align:center; background-color:#D9D9D9; color:#333333;">NAMA</th>

            {{-- Non-SPP headers (rowspan 2) --}}
            @foreach($masterTagihans as $mt)
                @php $isSPP = stripos($mt->nama_tagihan, 'spp') !== false; @endphp
                @if(!$isSPP)
                    <th rowspan="2" style="font-weight:bold; text-align:center; background-color:#D9D9D9; color:#333333;">
                        {{ strtoupper($mt->nama_tagihan) }}
                    </th>
                @endif
            @endforeach

            {{-- SPP header — kuning --}}
            @foreach($masterTagihans as $mt)
                @php $isSPP = stripos($mt->nama_tagihan, 'spp') !== false; @endphp
                @if($isSPP)
                    <th colspan="{{ count($bulanList) }}" style="font-weight:bold; text-align:center; background-color:#FFF2CC; color:#333333;">
                        SPP
                    </th>
                @endif
            @endforeach

            {{-- Total — biru --}}
            <th rowspan="2" style="font-weight:bold; text-align:center; background-color:#4472C4; color:#ffffff;">TOTAL</th>
        </tr>
        <tr>
            @foreach($masterTagihans as $mt)
                @php $isSPP = stripos($mt->nama_tagihan, 'spp') !== false; @endphp
                @if($isSPP)
                    @foreach($bulanList as $bulan)
                        <th style="font-weight:bold; text-align:center; background-color:#FFF2CC; color:#333333;">
                            {{ strtoupper($bulan) }}
                        </th>
                    @endforeach
                @endif
            @endforeach
        </tr>
    </thead>

    <tbody>
        @php
            $grandTotal = 0;
            $colTotals  = [];
            foreach($masterTagihans as $mt) {
                $isSPP = stripos($mt->nama_tagihan, 'spp') !== false;
                if($isSPP) {
                    foreach($bulanList as $bulan) $colTotals["spp_{$mt->id}_{$bulan}"] = 0;
                } else {
                    $colTotals["nonspp_{$mt->id}"] = 0;
                }
            }
        @endphp

        @foreach($siswas as $index => $riwayat)
            @php $siswaId = $riwayat->id; $totalBaris = 0; @endphp
            <tr>
                <td style="text-align:center; font-size:9pt;">{{ $index + 1 }}</td>
                <td style="text-align:left; font-weight:bold; font-size:9pt;">
                    {{ strtoupper($riwayat->siswa->nama_lengkap ?? '-') }}
                </td>

                {{-- Non-SPP --}}
                @foreach($masterTagihans as $mt)
                    @php $isSPP = stripos($mt->nama_tagihan, 'spp') !== false; @endphp
                    @if(!$isSPP)
                        @php
                            $tagihan  = collect($tagihans[$siswaId][$mt->id] ?? [])->first();
                            $terbayar = $tagihan->terbayar ?? 0;
                            $totalBaris += $terbayar;
                            $colTotals["nonspp_{$mt->id}"] += $terbayar;
                        @endphp
                        <td style="text-align:right; font-size:9pt;">
                            {{ $terbayar > 0 ? number_format($terbayar, 0, ',', '.') : '-' }}
                        </td>
                    @endif
                @endforeach

                {{-- SPP per bulan --}}
                @foreach($masterTagihans as $mt)
                    @php $isSPP = stripos($mt->nama_tagihan, 'spp') !== false; @endphp
                    @if($isSPP)
                        @foreach($bulanList as $bulan)
                            @php
                                $tagihan  = $tagihans[$siswaId][$mt->id][$bulan] ?? null;
                                $terbayar = $tagihan->terbayar ?? 0;
                                $totalBaris += $terbayar;
                                $colTotals["spp_{$mt->id}_{$bulan}"] += $terbayar;
                            @endphp
                            <td style="text-align:right; font-size:9pt; background-color:#FFFEF5;">
                                {{ $terbayar > 0 ? number_format($terbayar, 0, ',', '.') : '-' }}
                            </td>
                        @endforeach
                    @endif
                @endforeach

                @php $grandTotal += $totalBaris; @endphp
                <td style="text-align:right; font-weight:bold; font-size:9pt; background-color:#DCE6F1; color:#1F3864;">
                    {{ $totalBaris > 0 ? number_format($totalBaris, 0, ',', '.') : '-' }}
                </td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td colspan="2" style="font-weight:bold; text-align:center; background-color:#FFF2CC; color:#333333; font-size:9pt;">
                TOTAL
            </td>
            {{-- Total Non-SPP --}}
            @foreach($masterTagihans as $mt)
                @php $isSPP = stripos($mt->nama_tagihan, 'spp') !== false; @endphp
                @if(!$isSPP)
                    <td style="text-align:right; font-weight:bold; background-color:#FFF2CC; color:#333333; font-size:9pt;">
                        {{ number_format($colTotals["nonspp_{$mt->id}"] ?? 0, 0, ',', '.') }}
                    </td>
                @endif
            @endforeach
            {{-- Total SPP per bulan --}}
            @foreach($masterTagihans as $mt)
                @php $isSPP = stripos($mt->nama_tagihan, 'spp') !== false; @endphp
                @if($isSPP)
                    @foreach($bulanList as $bulan)
                        <td style="text-align:right; font-weight:bold; background-color:#FFF2CC; color:#333333; font-size:9pt;">
                            {{ number_format($colTotals["spp_{$mt->id}_{$bulan}"] ?? 0, 0, ',', '.') }}
                        </td>
                    @endforeach
                @endif
            @endforeach
            {{-- Grand Total --}}
            <td style="text-align:right; font-weight:bold; background-color:#4472C4; color:#ffffff; font-size:9pt;">
                {{ number_format($grandTotal, 0, ',', '.') }}
            </td>
        </tr>
    </tfoot>
</table>