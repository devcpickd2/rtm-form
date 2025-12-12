@php
    // default values jika tidak dikirim dari controller/route
    $company    = $company    ?? 'PT Contoh Pangan Indonesia';
    $doc_code   = $doc_code   ?? 'QF-2009';         // kode form kecil di kanan bawah
    $tanggal    = $tanggal    ?? now()->format('d/m/Y');
    $data       = $data       ?? collect(); // Ensure $data is a collection
    
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Premix</title>
    <style>
        @page { size: A4 portrait; margin: 18mm 15mm 15mm 15mm; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 11px; color: #000; }
        .header-top { font-size: 10px; margin-bottom: 4px; }
        .title { text-align: center; font-weight: 700; font-size: 16px; letter-spacing: .5px; margin: 2mm 0 4mm; }

        .meta { width: 100%; margin-bottom: 6px; }
        .meta td { vertical-align: middle; padding: 2px 0; }
        .meta .label { width: 22mm; }

        table.grid { width: 100%; border-collapse: collapse; }
        table.grid th, table.grid td { border: 1px solid #000; padding: 4px 6px; }
        table.grid th { text-align: center; font-weight: 700; }
        table.grid td { height: 18px; vertical-align: top; }
        .center { text-align: center; }
        .no-col { width: 8mm; text-align: center; }
        .nama-col { width: 48mm; }
        .kode-col { width: 32mm; }
        .sensori-col { width: 35mm; }
        .tindakan-col { width: 42mm; }
        .paraf-col { width: 20mm; text-align: center; }

        .note { margin-top: 8px; }
        .note .heading { font-weight: 700; margin-bottom: 3px; }
        .note ul { margin: 0 0 0 16px; padding: 0; }
        .note li { line-height: 1.4; }

        .catatan { margin-top: 6px; }
        .catatan ul{margin:0; padding-left: 16px;}
        .line { border-bottom: 1px solid #000; height: 16px; }

        .sign { margin-top: 18mm; width: 100%; text-align: center; }
        .sign td { vertical-align: bottom; height: 26mm; }
        .sign .slot { width: 33.33%; padding: 0 8mm; }
        .sign .line-sign { border-bottom: 1px solid #000; height: 0; margin: 0 0 4px; }
        .sign .role { font-size: 10px; }

        .doc-code { font-size: 9px; font-style: italic; text-align: right; }
    </style>
</head>
<body>
    <div class="header-top">{{ $company }}</div>
    <div class="title">VERIFIKASI PREMIX</div>

    <table class="meta">
        <tr>
            <td class="label">Hari/Tanggal</td><td>: {{ $tanggal }}</td>
            <td style="width:15mm;"></td>
            <td class="label">Shift</td><td>: {{ $shift }}</td>
        </tr>
    </table>

    <table class="grid">
        <thead>
            <tr>
                <th class="no-col">No.</th>
                <th class="nama-col">Nama Premix</th>
                <th class="kode-col">Kode Produksi</th>
                <th class="sensori-col">Sensori</th>
                <th class="tindakan-col">Tindakan Koreksi</th>
                <th class="paraf-col">Paraf QC</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($data as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $item->nama_premix }}</td>
                <td>{{ $item->kode_produksi }}</td>
                <td>{{ $item->sensori }}</td>
                <td>{{ $item->tindakan_koreksi }}</td>
                <td>
                    @if ($item->status_spv == 1)
                        Verified
                    @elseif ($item->status_spv == 2)
                        Revision
                    @else
                        -
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="center">Tidak ada data verifikasi premix untuk tanggal ini.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div class="doc-code">QR 30/00</div>

    <div class="note">
        <div class="heading">Keterangan:</div>
        <ul>
            <li><strong>Sensori</strong>: Tidak ada yang menggumpal, warna dan aroma normal</li>
            <li><strong>Tindakan koreksi</strong>: diisi jika sensori tidak sesuai atau terdapat kontaminasi benda asing</li>
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
                <div class="line-sign" style="margin-top:22mm;"></div>
                <div class="role">QC</div>
            </td>
            <td class="slot">
                <div>Diketahui oleh :</div>
                <div class="line-sign" style="margin-top:22mm;"></div>
                <div class="role">Produksi</div>
            </td>
            <td class="slot">
                <div>Disetujui oleh,</div>
                <div class="line-sign" style="margin-top:22mm;"></div>
                <div class="role">SPV QC</div>
            </td>
        </tr>
    </table>

    
</body>
</html>
