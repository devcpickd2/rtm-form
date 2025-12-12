@php
    $company  = $company  ?? 'PT. Charoen Pokphand Indonesia';
    $division = $division ?? 'Food Division';
    $title    = $title    ?? 'PEMERIKSAAN PEMASAKAN DENGAN STEAMER';
    $doc_code = $doc_code ?? 'QR 07/02';
    $tanggal  = $tanggal  ?? '________________';
    $shift    = $shift    ?? '________';
    $produk   = $produk   ?? '________________';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $title }}</title>
<style>
    @page { size: A4 portrait; margin: 8mm 8mm 12mm 8mm; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9px; color:#000; }

    .header-top { font-size:9px; }
    .title { text-align:center; font-weight:700; font-size:12px; margin:4px 0 6px; }

    .meta { width:100%; margin-bottom:5px; font-size:9px; }
    .meta td { padding:2px 6px; }

    table.grid { width:100%; border-collapse:collapse; font-size:8px; margin-bottom:10px; }
    table.grid th, table.grid td { border:1px solid #000; padding:2px 4px; }
    table.grid th { background:#f7f7f7; text-align:center; vertical-align:middle; }
    table.grid td { height:12px; }

    .section-title { font-weight:600; background:#eee; text-align:left; padding:2px 4px; }

    .sign { margin-top:5mm; width:100%; font-size:9px; }
    .sign td { text-align:left; padding:4px; }
    .sign .line-sign { border-bottom:1px solid #000; width:60%; display:inline-block; margin:0 5mm; }
    .doc-code { position: fixed; right: 8mm; bottom: 22mm; font-size:9px; }
</style>
</head>
<body>
    <div class="header-top">{{ $company }}<br>{{ $division }}</div>
    <div class="title">{{ $title }}</div>

    <table class="meta">
        <tr>
            <td style="width:12%">Hari/Tgl:</td><td style="width:25%">{{ $tanggal }}</td>
            <td style="width:8%">Shift:</td><td style="width:20%">{{ $shift }}</td>
            <td style="width:10%">Nama Produk:</td><td>{{ $produk }}</td>
        </tr>
    </table>

    {{-- Bagian 1 --}}
    <table class="grid">
        <tr><th colspan="6" class="section-title">Identitas</th></tr>
        <tr>
            <td>Kode Prod.</td><td colspan="5"></td>
        </tr>
        <tr>
            <td>Raw Material (kg)</td><td colspan="5"></td>
        </tr>
        <tr>
            <td>Jumlah Tray</td><td colspan="5"></td>
        </tr>

        <tr><th colspan="6" class="section-title">Steaming</th></tr>
        <tr>
            <td>1. Produk I (°C)</td><td></td>
            <td>2. Produk II (°C)</td><td></td>
            <td>3. Produk III (°C)</td><td></td>
        </tr>
        <tr>
            <td>4. Produk IV (°C)</td><td></td>
            <td>5. Produk V (°C)</td><td></td>
            <td>6. Produk VI (°C)</td><td></td>
        </tr>
        <tr>
            <td>Keterangan</td><td colspan="5"></td>
        </tr>

        <tr><th colspan="6" class="section-title">Lama Proses</th></tr>
        <tr>
            <td>Jam mulai</td><td></td>
            <td>Jam selesai</td><td></td>
            <td colspan="2"></td>
        </tr>

        <tr><th colspan="6" class="section-title">Sensorik</th></tr>
        <tr>
            <td>Kematangan</td><td></td>
            <td>Aroma</td><td></td>
            <td>Tekstur</td><td></td>
        </tr>
        <tr>
            <td>Warna</td><td></td>
            <td colspan="4"></td>
        </tr>

        <tr><th colspan="6" class="section-title">Paraf</th></tr>
        <tr>
            <td>QC</td><td></td>
            <td>Produksi</td><td></td>
            <td colspan="2"></td>
        </tr>
    </table>

    {{-- Bagian 2 (copy sama persis, untuk batch kedua) --}}
    <table class="grid">
        <tr><th colspan="6" class="section-title">Identitas</th></tr>
        <tr>
            <td>Kode Prod.</td><td colspan="5"></td>
        </tr>
        <tr>
            <td>Raw Material (kg)</td><td colspan="5"></td>
        </tr>
        <tr>
            <td>Jumlah Tray</td><td colspan="5"></td>
        </tr>

        <tr><th colspan="6" class="section-title">Steaming</th></tr>
        <tr>
            <td>1. Produk I (°C)</td><td></td>
            <td>2. Produk II (°C)</td><td></td>
            <td>3. Produk III (°C)</td><td></td>
        </tr>
        <tr>
            <td>4. Produk IV (°C)</td><td></td>
            <td>5. Produk V (°C)</td><td></td>
            <td>6. Produk VI (°C)</td><td></td>
        </tr>
        <tr>
            <td>Keterangan</td><td colspan="5"></td>
        </tr>

        <tr><th colspan="6" class="section-title">Lama Proses</th></tr>
        <tr>
            <td>Jam mulai</td><td></td>
            <td>Jam selesai</td><td></td>
            <td colspan="2"></td>
        </tr>

        <tr><th colspan="6" class="section-title">Sensorik</th></tr>
        <tr>
            <td>Kematangan</td><td></td>
            <td>Aroma</td><td></td>
            <td>Tekstur</td><td></td>
        </tr>
        <tr>
            <td>Warna</td><td></td>
            <td colspan="4"></td>
        </tr>

        <tr><th colspan="6" class="section-title">Paraf</th></tr>
        <tr>
            <td>QC</td><td></td>
            <td>Produksi</td><td></td>
            <td colspan="2"></td>
        </tr>
    </table>

    <p style="font-size:9px; margin-top:4px;">
        Keterangan: ✓ : OK &nbsp;&nbsp;&nbsp; – : tidak digunakan &nbsp;&nbsp;&nbsp; ✗ : TIDAK OK
    </p>

    <div>Catatan:</div>
    <div style="border:1px solid #000; height:30px; margin-top:2px;"></div>

    <table class="sign">
        <tr>
            <td>Diperiksa Oleh: <span class="line-sign"></span></td>
            <td>Diketahui Oleh: <span class="line-sign"></span></td>
        </tr>
    </table>

    <div class="doc-code">{{ $doc_code }}</div>
</body>
</html>
