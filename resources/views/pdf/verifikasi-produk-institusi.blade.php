@php
    $company    = $company    ?? 'PT Charoen Pokphand Indonesia';
    $division   = $division   ?? 'Food Division';
    $doc_code   = $doc_code   ?? 'QR-3101';
    $tanggal    = $tanggal    ?? now()->format('d/m/Y');
    $data       = $data       ?? collect(); // Ensure $data is a collection
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Produk Institusi</title>
    <style>
        @page { size: A4 portrait; margin: 18mm 15mm 15mm 15mm; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 11px; color: #000; }

        .header-top { font-size: 10px; }
        .title { text-align: center; font-weight: bold; font-size: 15px; margin: 5px 0 8px; }

        .meta { width: 100%; margin-bottom: 6px; }
        .meta td { padding: 2px 0; }
        .meta .label { width: 25mm; }

        table.grid { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.grid th, table.grid td { border: 1px solid #000; padding: 3px 4px; }
        table.grid th { text-align: center; font-weight: 700; }
        table.grid td { height: 18px; vertical-align: top; }

        .no-col { width: 8mm; text-align: center; }
        .jenis-col { width: 35mm; }
        .kode-col { width: 30mm; }
        .thawing-time { width: 25mm; }
        .thawing-loc { width: 30mm; }
        .suhu-col { width: 18mm; text-align: center; }
        .sensori-col { width: 30mm; }
        .ket-col { width: 40mm; }

        .note { margin-top: 6px; font-size: 10px; }
        .note .heading { font-weight: bold; margin-bottom: 2px; }
        .note ul { margin: 0 0 0 16px; padding: 0; }
        .note li { margin: 2px 0; }

        .catatan { margin-top: 5px; font-size: 10px; }
        .catatan ul{margin:0; padding-left: 16px;}
        .line { border-bottom: 1px solid #000; height: 16px; }

        .sign { margin-top: 18mm; width: 100%; text-align: center; font-size: 11px; }
        .sign td { vertical-align: bottom; height: 26mm; }
        .sign .slot { width: 33.33%; padding: 0 8mm; }
        .sign .line-sign { border-bottom: 1px solid #000; height: 0; margin: 20mm 0 3px; }
        .sign .role { font-size: 10px; }

        .doc-code { font-size: 9px; font-style: italic;text-align: right;}
    </style>
</head>
<body>
    <div class="header-top">{{ $company }}<br>{{ $division }}</div>
    <div class="title">VERIFIKASI PRODUK INSTITUSI</div>

    <table class="meta">
        <tr>
            <td class="label">Hari/Tanggal</td><td>: {{ $tanggal }}</td>
            <td style="width:20mm;"></td>
            <td class="label">Shift</td><td>: {{ $shift }}</td>
        </tr>
    </table>

    <table class="grid">
        <thead>
            <tr>
                <th rowspan="2" class="no-col">No.</th>
                <th rowspan="2" class="jenis-col">Jenis Produk</th>
                <th rowspan="2" class="kode-col">Kode Produksi</th>
                <th colspan="2">Proses Thawing</th>
                <th colspan="2">Suhu Produk (°C)</th>
                <th rowspan="2" class="sensori-col">Sensori</th>
                <th rowspan="2" class="ket-col">Keterangan</th>
            </tr>
            <tr>
                <th class="thawing-time">Waktu Proses</th>
                <th class="thawing-loc">Lokasi</th>
                <th class="suhu-col">Sebelum</th>
                <th class="suhu-col">Sesudah</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($data as $index => $item)
            <tr>
                <td class="no-col">{{ $index + 1 }}</td>
                <td>{{ $item->jenis_produk }}</td>
                <td>{{ $item->kode_produksi }}</td>
                <td>
                    @if($item->waktu_proses_mulai && $item->waktu_proses_selesai)
                    {{ \Carbon\Carbon::parse($item->waktu_proses_mulai)->format('H:i') }} -
                    {{ \Carbon\Carbon::parse($item->waktu_proses_selesai)->format('H:i') }}
                    @else
                    -
                    @endif
                </td>
                <td>{{ $item->lokasi }}</td>
                <td>{{ $item->suhu_sebelum }}°C</td>
                <td>{{ $item->suhu_sesudah }}°C</td>
                <td>{{ $item->sensori }}</td>
                <td>{{ $item->keterangan }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="no-col">Tidak ada data verifikasi produk institusi untuk tanggal ini.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div class="doc-code">QR 31/01</div>

    <div class="note">
        <div class="heading">Keterangan:</div>
        <ul>
            <li>Sensori rasa dan tekstur untuk produk yang melewati proses steam</li>
            <li>Sensori aroma, warna, dan penampakan hanya untuk produk hasil proses thawing</li>
        </ul>
    </div>

    <div class="catatan">
        <div>Catatan:</div>
        <div>
            <ul>
                @foreach($data as $index => $item)
                    <li>{{ $item->catatan ?? '-' }}</li>
                @endforeach 
            </ul>
        </div>
    </div>

    <table class="sign">
        <tr>
            <td class="slot">
                <div>Diperiksa oleh,</div>
                <div class="line-sign"></div>
                <div class="role">QC</div>
            </td>
            <td class="slot">
                <div>Diketahui oleh :</div>
                <div class="line-sign"></div>
                <div class="role">Produksi</div>
            </td>
            <td class="slot">
                <div>Disetujui oleh,</div>
                <div class="line-sign"></div>
                <div class="role">SPV QC</div>
            </td>
        </tr>
    </table>

    
</body>
</html>
