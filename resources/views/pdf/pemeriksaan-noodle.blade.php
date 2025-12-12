@php
    use Carbon\Carbon;
    $company   = $company   ?? 'PT. Charoen Pokphand Indonesia';
    $division  = $division  ?? 'Food Division';
    $title     = $title     ?? 'PEMERIKSAAN PEMASAKAN NOODLE';
    $doc_code  = $doc_code  ?? 'QF 07/12';
    $tanggal   = $tanggal   ?? '________________';
    $shift     = $shift     ?? '________';
    $produk    = $produk    ?? '________________';
    $cols      = (int)($cols ?? 6); // banyak kotak isian per baris parameter
    $data      = $data      ?? collect(); // Ensure $data is a collection
    $firstItem = $data->isNotEmpty() ? $data->first() : null;
    $mixingData = $firstItem ? json_decode($firstItem->mixing, true) : [];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $title }}</title>
<style>
    @page { size: A4 portrait; margin: 12mm 10mm 14mm 10mm; }
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 10px; }

    .header-top { font-size:9px; }
    .title { text-align:center; font-weight:700; font-size:13px; margin:4px 0 6px; }

    .meta { width:100%; margin-bottom:6px; }
    .meta td { padding:2px 0; }

    table.grid { width:100%; border-collapse:collapse; font-size:9px; margin-bottom:6px; }
    table.grid th, table.grid td { border:1px solid #000; padding:2px 3px; }
    table.grid td.label { width:45mm; font-weight:600; }
    table.grid td.cell { width:auto; height:14px; text-align:center; }
    .section { background:#f5f5f5; font-weight:700; text-align:left; }

    .multiheader th { text-align:center; font-weight:700; background:#f2f2f2; }

    .note { font-size:9px; margin-top:4px; }
    .catatan { font-size:9px; margin-top:6px; }
    .line { border-bottom:1px solid #000; height:14px; }

    .sign { margin-top:10mm; width:100%; }
    .sign td { text-align:center; vertical-align:bottom; }
    .sign .slot { width:50%; padding:0 10mm; }
    .sign .line-sign { border-bottom:1px solid #000; height:0; margin:12mm 0 3px; }
    .role { font-size:9px; }

    .doc-code { position: fixed; right: 10mm; bottom: 28mm; font-size:9px; }
</style>
</head>
<body>
    <div class="header-top">{{ $company }}<br>{{ $division }}</div>
    <div class="title">{{ $title }}</div>

    <table class="meta">
        <tr>
            <td>Hari/Tanggal :</td><td style="width:35%">{{ Carbon::parse($firstItem->date ?? null)->format('d/m/Y') }}</td>
            <td>Shift :</td><td style="width:20%">{{ $firstItem->shift ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nama Produk :</td><td colspan="3">{{ $firstItem->nama_produk ?? '-' }}</td>
        </tr>
    </table>

    @php
        // Ensure $data is always a collection, even if empty, to allow @foreach to run
        if ($data->isEmpty()) {
            $data = collect([ (object)['mixing' => '[]', 'catatan' => null, 'status_produksi' => 0, 'status_spv' => 0] ]);
        }
        $firstItem = $data->first(); // Re-assign firstItem after potentially creating dummy data
        $mixingData = $firstItem ? (json_decode($firstItem->mixing, true) ?? []) : [];
        if (empty($mixingData)) {
            $mixingData = [[]]; // Ensure at least one empty item to render table structure
        }
    @endphp

    <table class="grid">
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th colspan="2"></th>
                @for($i=0; $i < $cols; $i++)
                    <th colspan="2"></th>
                @endfor
            </tr>
            <tr>
                <th colspan="2">Nama Produk</th>
                @for($i=0; $i < $cols; $i++)
                    <th colspan="2">{{ $mixingData[$i]['nama_produk'] ?? '-' }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            <tr>
                <td rowspan="10" class="text-center">1</td>
                <td colspan="2" class="section">MIXING</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="section"></td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Kode Susunan</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ $mixingData[$i]['kode_produksi'] ?? '-' }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Kode Batch</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ $mixingData[$i]['kode_produksi'] ?? '-' }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Bahan Utama</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ $mixingData[$i]['bahan_utama'] ?? '-' }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Kode Bahan</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ $mixingData[$i]['kode_bahan'] ?? '-' }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Berat (Kg)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ $mixingData[$i]['berat_bahan'] ?? '-' }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Bahan Lain yang Ditambahkan</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">
                        @if(!empty($mixingData[$i]['bahan_lain']))
                            @foreach($mixingData[$i]['bahan_lain'] as $bahan)
                                {{ $bahan['nama'] ?? '-' }} ({{ $bahan['berat'] ?? '-' }} Kg)<br>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Waktu Proses</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ implode(', ', $mixingData[$i]['waktu_proses'] ?? ['-']) }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Vacuum (%)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ implode(', ', $mixingData[$i]['vacuum'] ?? ['-']) }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Suhu Adonan (°C)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ implode(', ', $mixingData[$i]['suhu_adonan'] ?? ['-']) }}</td>
                @endfor
            </tr>

            <tr>
                <td rowspan="3" class="text-center">2</td>
                <td colspan="2" class="section">AGING</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="section"></td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Waktu</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ implode(', ', $mixingData[$i]['waktu_aging'] ?? ['-']) }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">RH Kelembaban (%)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ implode(', ', $mixingData[$i]['rh_aging'] ?? ['-']) }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Suhu Ruangan (°C)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ implode(', ', $mixingData[$i]['suhu_ruang_aging'] ?? ['-']) }}</td>
                @endfor
            </tr>

            <tr>
                <td rowspan="2" class="text-center">3</td>
                <td colspan="2" class="section">ROLLING</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="section"></td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Ukuran Tebal (mm)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ implode(', ', $mixingData[$i]['tebal_rolling'] ?? ['-']) }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Kecepatan (Hz)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">-</td> {{-- No direct mapping --}}
                @endfor
            </tr>

            <tr>
                <td rowspan="2" class="text-center">4</td>
                <td colspan="2" class="section">CUTTING & SLITTING</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="section"></td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Sampling Berat / 1 cut</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ implode(', ', $mixingData[$i]['sampling_cutiing'] ?? ['-']) }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Kecepatan (Hz)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">-</td> {{-- No direct mapping --}}
                @endfor
            </tr>

            <tr>
                <td rowspan="3" class="text-center">5</td>
                <td colspan="2" class="section">BOILING</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="section"></td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Suhu Setting Water (°C)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ $mixingData[$i]['suhu_setting_boiling'] ?? '-' }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Suhu Actual Water (°C)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ implode(', ', $mixingData[$i]['suhu_actual_boiling'] ?? ['-']) }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Waktu (menit)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ $mixingData[$i]['waktu_boiling'] ?? '-' }}</td>
                @endfor
            </tr>

            <tr>
                <td rowspan="3" class="text-center">6</td>
                <td colspan="2" class="section">WASHING</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="section"></td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Suhu Setting Water (°C)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ $mixingData[$i]['suhu_setting_washing'] ?? '-' }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Suhu Actual Water (°C)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ implode(', ', $mixingData[$i]['suhu_actual_washing'] ?? ['-']) }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Waktu (menit)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ $mixingData[$i]['waktu_washing'] ?? '-' }}</td>
                @endfor
            </tr>

            <tr>
                <td rowspan="3" class="text-center">7</td>
                <td colspan="2" class="section">COOLING SHOCK</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="section"></td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Suhu Setting Water (°C)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ $mixingData[$i]['suhu_setting_cooling'] ?? '-' }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Suhu Actual Water (°C)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ implode(', ', $mixingData[$i]['suhu_actual_cooling'] ?? ['-']) }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Waktu (menit)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ $mixingData[$i]['waktu_cooling'] ?? '-' }}</td>
                @endfor
            </tr>

            <tr>
                <td rowspan="2" class="text-center">8</td>
                <td colspan="2" class="section">LAMA PROSES</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="section"></td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Jam Mulai</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ $mixingData[$i]['mulai'] ?? '-' }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Jam Selesai</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ $mixingData[$i]['selesai'] ?? '-' }}</td>
                @endfor
            </tr>

            <tr>
                <td rowspan="5" class="text-center">9</td>
                <td colspan="2" class="section">SENSORI</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="section"></td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Suhu Produk Akhir (°C)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ implode(', ', $mixingData[$i]['suhu_akhir'] ?? ['-']) }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Suhu Produk Setelah 1 Menit (°C)</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ implode(', ', $mixingData[$i]['suhu_after'] ?? ['-']) }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Rasa</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ implode(', ', $mixingData[$i]['rasa'] ?? ['-']) }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Kekenyalan</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ implode(', ', $mixingData[$i]['kekenyalan'] ?? ['-']) }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Warna</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">{{ implode(', ', $mixingData[$i]['warna'] ?? ['-']) }}</td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">QC</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">
                        @if ($firstItem->status_produksi == 1)
                            ✔
                        @elseif ($firstItem->status_produksi == 2)
                            TIDAK OK
                        @else
                            -
                        @endif
                    </td>
                @endfor
            </tr>
            <tr>
                <td colspan="2" class="label">Prod</td>
                @for($i=0; $i < $cols; $i++)
                    <td colspan="2" class="cell">
                        @if ($firstItem->status_spv == 1)
                            ✔
                        @elseif ($firstItem->status_spv == 2)
                            TIDAK OK
                        @else
                            -
                        @endif
                    </td>
                @endfor
            </tr>
        </tbody>
    </table>

    <div class="catatan">Catatan: {{ $firstItem->catatan ?? '-' }}</div>
    <div class="line"></div>

    <table class="sign">
        <tr>
            <td class="slot">
                Diperiksa Oleh:
                <div class="line-sign"></div>
                <div class="role">QC</div>
            </td>
            <td class="slot">
                Disetujui Oleh:
                <div class="line-sign"></div>
                <div class="role">Produksi</div>
            </td>
        </tr>
    </table>

    <div class="doc-code">{{ $doc_code }}</div>
</body>
</html>
