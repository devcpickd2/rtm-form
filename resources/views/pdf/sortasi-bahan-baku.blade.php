@php
    $company  = $company  ?? 'PT. Charoen Pokphand Indonesia';
    $division = $division ?? 'Food Division';
    $title    = $title    ?? 'SORTASI BAHAN BAKU YANG TIDAK SESUAI';
    $doc_code = $doc_code ?? 'QR 27/09';
    $tanggal  = $tanggal  ?? now()->format('d/m/Y');
    $data     = $data     ?? collect(); // Ensure $data is a collection
    $shift    = $data->isNotEmpty() ? $data->first()->shift : '-';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $title }}</title>
<style>
    @page { size: A4 portrait; margin: 16mm 14mm 15mm 14mm; }
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 11px; color:#000; }

    .header-top { font-size:10px; line-height:1.2; }
    .title { text-align:center; font-weight:700; font-size:15px; margin:6px 0 8px; }

    .meta { width:100%; margin-bottom:6px; }
    .meta td { padding:2px 0; }
    .meta .label { width:26mm; }

    table.grid { width:100%; border-collapse:collapse; }
    table.grid th, table.grid td { border:1px solid #000; padding:4px 5px; }
    table.grid th { text-align:center; font-weight:700; }
    table.grid td { height:20px; vertical-align:top; }

    .no-col     { width:8mm; text-align:center; }
    .nama-col   { width:50mm; }
    .kode-col   { width:32mm; }
    .sebelum-col{ width:28mm; text-align:center; }
    .sesuai-col { width:28mm; text-align:center; }
    .tdksesuai-col { width:28mm; text-align:center; }
    .tindakan-col { width:auto; }

    .note { margin-top:8px; font-size:10px; }
    .note .heading { font-weight:700; margin-bottom:2px; }

    .catatan { margin-top:6px; font-size:10px; }
    .catatan ul{margin:0; padding-left: 16px;}
    .line { border-bottom:1px solid #000; height:16px; }

    .sign { margin-top:16mm; width:100%; }
    .sign td { vertical-align:bottom; text-align:center; }
    .sign .slot { width:33.33%; padding:0 10mm; }
    .sign .line-sign { height:0; border-bottom:1px solid #000; margin:18mm 0 3px; }
    .role { font-size:10px; }

    .doc-code { font-size:9px; font-style: italic; text-align: right;}
</style>
</head>
<body>
    <div class="header-top">{{ $company }}<br>{{ $division }}</div>
    <div class="title">{{ $title }}</div>

    <table class="meta">
        <tr>
            <td class="label">Hari/ Tanggal</td><td>: {{ $tanggal }}</td>
        </tr>
        <tr>
            <td class="label">Shift</td><td>: {{ $shift }}</td>
        </tr>
    </table>

    <table class="grid">
        <thead>
            <tr>
                <th class="no-col" rowspan="2">No.</th>
                <th class="nama-col" rowspan="2">Nama Bahan</th>
                <th class="kode-col" rowspan="2">Kode Produksi</th>
                <th class="sebelum-col" rowspan="2">Jumlah Bahan<br>Sebelum Sortasi</th>
                <th colspan="2">Jumlah Bahan Setelah Sortasi</th>
                <th class="tindakan-col" rowspan="2">Tindakan Koreksi</th>
            </tr>
            <tr>
                <th class="sesuai-col">Sesuai</th>
                <th class="tdksesuai-col">Tidak Sesuai</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($data as $index => $item)
            <tr>
                <td class="no-col">{{ $index + 1 }}</td>
                <td>{{ $item->nama_bahan }}</td>
                <td>{{ $item->kode_produksi }}</td>
                <td class="sebelum-col">{{ $item->jumlah_bahan }}</td>
                <td class="sesuai-col">{{ $item->jumlah_sesuai }}</td>
                <td class="tdksesuai-col">{{ $item->jumlah_tidak_sesuai }}</td>
                <td>{{ $item->tindakan_koreksi }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="no-col">Tidak ada data sortasi bahan baku untuk tanggal ini.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="doc-code">QR 27/00</div>

    <div class="catatan">
        Catatan:
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
                Dilaporkan oleh,
                <div class="line-sign"></div>
                <div class="role">QC</div>
            </td>
            <td class="slot">
                Diketahui oleh,
                <div class="line-sign"></div>
                <div class="role">Produksi</div>
            </td>
            <td class="slot">
                Disetujui oleh,
                <div class="line-sign"></div>
                <div class="role">Spv QC</div>
            </td>
        </tr>
    </table>
</body>
</html>
