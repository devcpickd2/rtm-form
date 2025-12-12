@php
    $company  = $company  ?? 'PT. Charoen Pokphand Indonesia';
    $division = $division ?? 'FOOD DIVISION';
    $title    = $title    ?? 'PEMERIKSAAN PROSES THAWING';
    $doc_code = $doc_code ?? 'QR 20/09';
    $tanggal  = $tanggal  ?? now()->format('d/m/Y');
    $data     = $data     ?? collect(); // Ensure $data is a collection
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $title }}</title>
<style>
    @page { size: A4 landscape; margin: 14mm 12mm 14mm 12mm; }
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 10px; }

    .header-top { font-size:10px; font-weight:600; }
    .title { text-align:center; font-weight:700; font-size:14px; margin:4px 0 6px; }

    .meta { margin-bottom:6px; }
    .meta td { padding:2px 0; }

    table tr td{text-align: center;}

    table.grid { width:100%; border-collapse:collapse; }
    table.grid th, table.grid td { border:1px solid #000; padding:3px 4px; }
    table.grid th { text-align:center; font-weight:700; }
    table.grid td { height:20px; vertical-align:top; }

    .no-col {  text-align:center; }
    .cond-col { width:25mm; }
    .jenis-col { width:25mm; }
    .jumlah-col { width:20mm; text-align:center; }
    .kode-col { width:25mm; }
    .cond-prod-col { width:35mm; }
    .suhu-col { width:20mm; text-align:center; }
    .pukul-col { width:22mm; text-align:center; }
    .selesai-col { width:22mm; text-align:center; }
    .cond-prod-after-col { width:35mm; }
    .suhu-final-col { width:28mm; text-align:center; }

    .note { margin-top:8px; font-size:9px; }
    .note .heading { font-weight:700; }

    .catatan { margin-top:4px; font-size:9px; }
    .catatan ul{margin:0; padding-left: 16px;}
    .line { border-bottom:1px solid #000; height:16px; }

    .sign { margin-top:12mm; width:100%; font-size:10px; }
    .sign td { vertical-align:bottom; text-align:center; }
    .sign .slot { width:50%; padding:0 10mm; }
    .sign .line-sign { border-bottom:1px solid #000; height:0; margin:14mm 0 3px; }

    .doc-code { font-size:9px; font-style: italic; text-align: right;}
</style>
</head>
<body>
    <div class="header-top">{{ $company }}<br>{{ $division }}</div>
    <div class="title">{{ $title }}</div>

    <table class="meta">
        <tr>
            <td>Hari/Tanggal :</td><td>{{ $tanggal }}</td>
        </tr>
    </table>

    <table class="grid">
        <thead>
            <tr>
                <th rowspan="2" class="no-col" style="width: 15px;">No.</th>
                <th rowspan="2" class="cond-col">Kondisi Ruangan</th>
                <th rowspan="2" class="jenis-col">Jenis Produk</th>
                <th colspan="5">Sebelum Proses Thawing</th>
                <th colspan="3">Setelah Proses Thawing</th>
            </tr>
            <tr>
                <th class="jumlah-col" style="width: 15px;">Jumlah</th>
                <th class="kode-col">Kode Produksi</th>
                <th class="cond-prod-col">Kondisi Produk</th>
                <th class="suhu-col">Suhu<br> Ruangan °C</th>
                <th class="pukul-col">Mulai Thawing Pukul</th>
                <th class="selesai-col" style="width: 50px;">Selesai Thawing Pukul</th>
                <th class="cond-prod-after-col">Kondisi Produk</th>
                <th class="suhu-final-col">Suhu Produk °C <br>(5-10°C)</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($data as $index => $item)
            <tr>
                <td class="no-col">{{ $index + 1 }}</td>
                <td>{{ $item->kondisi_ruangan }}</td>
                <td>{{ $item->jenis_produk }}</td>
                <td>{{ $item->jumlah }}</td>
                <td>{{ $item->kode_produksi }}</td>
                <!-- <td>{{ $item->kondisi_produk }}<br>Keterangan: {{ $item->keterangan_kondisi }}</td> -->
                <td>
                    <table style="border-collapse:collapse;">
                        <tr>
                            <td style="border-top:0;border-bottom:0;border-left:0;width:50%;">{{ $item->kondisi_produk }}</td>
                            <td style="border-top:0;border-bottom:0;border-left:0;border-right:0;width:50%;">Keterangan:<br>{{ $item->keterangan_kondisi }}</td>
                        </tr>
                    </table>
                </td>
                <td>{{ $item->suhu_ruangan }}</td>
                <td>{{ \Carbon\Carbon::parse($item->mulai_thawing)->format('H:i') }}</td>
                <td>{{ \Carbon\Carbon::parse($item->selesai_thawing)->format('H:i') }}</td>
                <!-- <td>{{ $item->kondisi_produk_setelah }}<br>Keterangan: {{ $item->keterangan_kondisi_setelah }}</td> -->
                <td>
                    <table style="border-collapse:collapse;">
                        <tr>
                            <td style="border-top:0;border-bottom:0;border-left:0;">{{ $item->kondisi_produk_setelah }}</td>
                            <td style="border-top:0;border-bottom:0;border-left:0;">Keterangan:<br>{{ $item->keterangan_kondisi_setelah }}</td>
                            <td style="border-top:0;border-bottom:0;border-left:0;border-right:0;">Jumlah:<br>{{ $item->jumlah_setelah }}<br></td>
                        </tr>
                    </table>
                </td>
                <td>{{ $item->suhu_produk }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="no-col">Tidak ada data pemeriksaan proses thawing untuk tanggal ini.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div class="doc-code">QR 20/00</div>

    <div class="note">
        <div class="heading">Keterangan:</div>
        <div>- *) Coret yang tidak perlu</div>
        <div>- Kondisi ruangan: Bersih, tidak ada dripping atau kondensasi</div>
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
                Dibuat Oleh
                <div class="line-sign"></div>
                <div>Quality Control</div>
            </td>
            <td class="slot">
                Disetujui Oleh
                <div class="line-sign"></div>
                <div>SPV QC</div>
            </td>
        </tr>
    </table>

    
</body>
</html>
