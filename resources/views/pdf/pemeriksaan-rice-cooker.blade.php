@php
    $company   = $company   ?? 'PT. Charoen Pokphand Indonesia';
    $division  = $division  ?? 'Food Division';
    $title     = $title     ?? 'PEMERIKSAAN PEMASAKAN DENGAN RICE COOKER';
    $doc_code  = $doc_code  ?? 'QF 07/08';
    $tanggal   = $tanggal   ?? now()->format('d/m/Y');
    $data      = $data      ?? collect(); // Ensure $data is a collection
    $shift     = $data->isNotEmpty() ? $data->first()->shift : '-';
    $produk    = $data->isNotEmpty() ? $data->first()->nama_produk : '-';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $title }}</title>
<style>
    @page { size: A4 portrait; margin: 12mm 10mm 12mm 10mm; }
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 10px; }

    .header-top { font-size:9px; }
    .title { text-align:center; font-weight:700; font-size:13px; margin:4px 0 4px; }

    .meta { width:100%; margin-bottom:4px; font-size:10px; }
    .meta td { padding:2px 0; }

    table.grid { width:100%; border-collapse:collapse; font-size:9px; margin-bottom:8px; }
    table.grid th, table.grid td { border:1px solid #000; padding:2px 3px; }
    table.grid th { text-align:center; font-weight:700; }
    table.grid td { height:16px; vertical-align:top; }

    .section-title { font-weight:700; text-align:left; background:#f2f2f2; }
    .doc-code { position: fixed; right: 10mm; bottom: 26mm; font-size:9px; }

    .note { font-size:9px; margin-top:4px; }
    .catatan { font-size:9px; margin-top:6px; }
    .line { border-bottom:1px solid #000; height:14px; }

    .sign { margin-top:10mm; width:100%; }
    .sign td { text-align:center; vertical-align:bottom; }
    .sign .slot { width:50%; padding:0 10mm; }
    .sign .line-sign { border-bottom:1px solid #000; height:0; margin:12mm 0 3px; }
    .role { font-size:9px; }
</style>
</head>
<body>
    <div class="header-top">{{ $company }}<br>{{ $division }}</div>
    <div class="title">{{ $title }}</div>

    <table class="meta">
        <tr>
            <td>Hari/Tgl :</td><td>{{ $tanggal }}</td>
            <td>Shift :</td><td>{{ $shift }}</td>
            <td>Nama Produk :</td><td>{{ $produk }}</td>
        </tr>
    </table>

    @php
        // Ensure $data is always a collection, even if empty, to allow @foreach to run
        if ($data->isEmpty()) {
            $data = collect([ (object)['cooker' => '[]', 'catatan' => null, 'status_produksi' => 0, 'status_spv' => 0] ]);
        }
    @endphp

    @foreach ($data as $item)
        @php
            $cookerData = json_decode($item->cooker, true) ?? [];
            if (empty($cookerData)) {
                $cookerData = [[]]; // Ensure at least one empty item to render table structure
            }
        @endphp

        @foreach($cookerData as $index => $cookerItem)
            <div style="margin-top: 8px; font-weight: bold;">Pemeriksaan Rice Cooker {{ $cookerItem['cooker_ke'] ?? ($index + 1) }}</div>
            <table class="grid">
                <tr><th colspan="8" class="section-title">DATA PEMASAKAN</th></tr>
                <tr>
                    <th>Cooker Ke</th>
                    <th>Suhu Awal (°C)</th>
                    <th>Suhu Akhir (°C)</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Selesai</th>
                    <th>Lama Pemasakan (menit)</th>
                    <th>Hasil Pemasakan</th>
                    <th>Keterangan</th>
                </tr>
                <tr>
                    <td>{{ $cookerItem['cooker_ke'] ?? '-' }}</td>
                    <td>{{ $cookerItem['suhu_awal'] ?? '-' }}</td>
                    <td>{{ $cookerItem['suhu_akhir'] ?? '-' }}</td>
                    <td>{{ $cookerItem['waktu_mulai'] ?? '-' }}</td>
                    <td>{{ $cookerItem['waktu_selesai'] ?? '-' }}</td>
                    <td>{{ $cookerItem['lama_pemasakan'] ?? '-' }}</td>
                    <td>{{ $cookerItem['hasil_pemasakan'] ?? '-' }}</td>
                    <td>{{ $cookerItem['keterangan'] ?? '-' }}</td>
                </tr>
            </table>
        @endforeach
    @endforeach

    <div class="note">
        Keterangan: ✔ = OK, × = tidak digunakan, TIDK OK = Tidak OK
    </div>

    <div class="catatan">Catatan: {{ $data->isNotEmpty() ? $data->first()->catatan : '-' }}</div>
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
