@php
    $company  = $company  ?? 'PT. Charoen Pokphand Indonesia';
    $division = $division ?? 'Food Division';
    $title    = $title    ?? 'PENERAAN TIMBANGAN';
    $doc_code = $doc_code ?? 'QR 13/01';
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
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 11px; color: #000; }

    .header-top { font-size: 10px; line-height: 1.2; }
    .title { text-align: center; font-weight: 700; font-size: 16px; margin: 6px 0 8px; }

    .meta { width: 100%; margin-bottom: 6px; }
    .meta td { padding: 1px 0; }
    .meta .label { width: 24mm; }

    table.grid { width: 100%; border-collapse: collapse; }
    table.grid th, table.grid td { border: 1px solid #000; padding: 4px 5px; }
    table.grid th { text-align: center; font-weight: 700; }
    table.grid td { height: 22px; vertical-align: top; }

    .kode-col   { width: 45mm; }
    .std-col    { width: 25mm; text-align:center; }
    .pukul-col  { width: 25mm; text-align:center; }
    .hasil-col  { width: 25mm; text-align:center; }
    .tindak-col { width: auto; }

    .note { margin-top: 8px; font-size: 10px; }
    .note .heading { font-weight: 700; margin-bottom: 2px; }
    .note ul { margin: 0 0 0 16px; padding: 0; }
    .note li { margin: 1px 0; }

    .catatan { margin-top: 6px; font-size: 10px; }
    .catatan ul{margin:0; padding-left: 16px;}
    .line { border-bottom: 1px solid #000; height: 16px; }

    .footer-area { width: 100%; margin-top: 10mm; }
    .footer-area td { vertical-align: bottom; }
    .lbl { font-size: 10px; }
    .sign { height: 20mm; border-bottom: 1px solid #000; margin-bottom: 3px; }
    .role { font-size: 10px; text-align: center; }

    .doc-code { font-size: 9px; text-align: right;font-style: italic;}
</style>
</head>
<body>
    <div class="header-top">{{ $company }}<br>{{ $division }}</div>
    <div class="title">{{ $title }}</div>

    <table class="meta">
        <tr>
            <td class="label">Hari/Tanggal :</td><td>{{ $tanggal }}</td>
        </tr>
        <tr>
            <td class="label">Shift</td><td>: {{ $shift }}</td>
        </tr>
    </table>

    <table class="grid">
        <thead>
            <tr>
                <th class="kode-col" rowspan="2">KODE TIMBANGAN</th>
                <th class="std-col" rowspan="2">STANDAR (gr)</th>
                <th colspan="2">PENERAAN</th>
                <th class="tindak-col" rowspan="2">TINDAKAN PERBAIKAN</th>
            </tr>
            <tr>
                <th class="pukul-col">PUKUL</th>
                <th class="hasil-col">HASIL TERA</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($data as $index => $item)
            <tr>
                <td>{{ $item->kode_timbangan }}</td>
                <td class="std-col">{{ $item->standar }}</td>
                <td class="pukul-col">{{ $item->waktu_tera }}</td>
                <td class="hasil-col">{{ $item->hasil_tera }}</td>
                <td>{{ $item->tindakan_perbaikan }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="no-col">Tidak ada data peneraan timbangan untuk tanggal ini.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div class="doc-code">QR 18 / 01</div>
    <table>
        <tr>
            <td style="width:60%">
                <div class="note">
                    <div class="heading">Keterangan :</div>
                    <ul>
                        <li>Tera timbangan dilakukan di setiap awal produksi</li>
                        <li>Timbangan ditera menggunakan anak timbangan standar</li>
                        <li>Jika ada selisih angka timbang dengan berat timbangan standar, beri keterangan (+) atau (-) angka selisih</li>
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
            </td>
            <td style="width:40%">
                <table class="footer-area">
                    <tr>
                        <td></td>
                        <td class="lbl" style="text-align:center;">Dibuat oleh</td>
                        <td></td>
                        <td class="lbl" style="text-align:center;">Diketahui oleh</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><div class="sign"></div><div class="role">QC</div></td>
                        <td></td>
                        <td><div class="sign"></div><div class="role">QC SPV</div></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    

    

    
</body>
</html>
