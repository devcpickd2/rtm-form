@php
    $company   = $company   ?? 'PT. Charoen Pokphand Indonesia';
    $division  = $division  ?? 'Food Division';
    $title     = $title     ?? 'PARAMETER PRODUK SAUS YOSHINOYA';
    $zona      = $zona      ?? '';
    $saus      = $saus      ?? '';
    $shift     = $shift     ?? '';
    $doc_code  = $doc_code  ?? 'QF 20/07';
    $data      = $data      ?? collect(); // Ensure $data is a collection
    $zona      = $zona      ?? ($data->isNotEmpty() ? $data->first()->zona : 'Zona 1');
    $saus      = $saus      ?? ($data->isNotEmpty() ? $data->first()->saus : 'Yoshinoya');
    $shift     = $shift     ?? ($data->isNotEmpty() ? $data->first()->shift : '-');

    // Spesifikasi/target (akan tercetak di baris ke-1 tepat di bawah header)
    $specs = $specs ?? [
        'suhu'       => '24 – 26',
        'brix'       => '62 – 63',
        'salt'       => '1.7 – 2.0',
        'viscositas' => '70 – 280',
        'bf1'        => '3000 – 7000 cP',
        'bf2'        => '3000 – 7000 cP',
    ];

    // label kolom Brookfield (ubah sesuai alat yang dipakai)
    $bf1_label = $bf1_label ?? 'Brookfield (LV, SC4-31, 30 rpm) Suhu produk 25–30 °C';
    $bf2_label = $bf2_label ?? 'Brookfield (LV, SC4-31, 30 rpm) Suhu produk 25–30 °C';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $title }}</title>
<style>
    @page { size: A4 portrait; margin: 14mm 12mm 14mm 12mm; }
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 10px; color:#000; }

    .header-top { font-size:9px; line-height:1.2; }
    .title { text-align:center; font-weight:700; font-size:13px; margin:6px 0 6px; }

    .meta { width:100%; font-size:10px; margin-bottom:6px; }
    .meta td { padding:1px 0; }
    .meta .label { width:18mm; }

    table.grid { width:100%; border-collapse:collapse; }
    table.grid th, table.grid td { border:1px solid #000; padding:3px 4px; }
    table.grid th { text-align:center; font-weight:700; }
    table.grid td { height:18px; vertical-align:top; }

    .tgl-col   { width:22mm; }
    .kode-col  { width:26mm; }
    .suhu-col  { width:20mm; text-align:center; }
    .brix-col  { width:18mm; text-align:center; }
    .salt-col  { width:18mm; text-align:center; }
    .visc-col  { width:24mm; text-align:center; }
    .bf1-col   { width:34mm; text-align:center; }
    .bf2-col   { width:34mm; text-align:center; }

    .spec { color:#b10000; font-weight:700; }
    .doc-code { font-size:9px; font-style: italic; text-align: right; }

    .footer { margin-top: 8mm; width:100%; }
    .footer td { vertical-align:bottom; text-align:center; }
    .sign { height: 16mm; border-bottom: 1px solid #000; margin-bottom:3px; }
    .role { font-size:9px; }

    .catatan { margin-top: 6mm; font-size:10px; }
    .line { border-bottom:1px solid #000; height:14px; }
</style>
</head>
<body>
    <div class="header-top">{{ $company }}<br>{{ $division }}</div>
    <div class="title">{{ $title }}</div>

    <table class="meta">
        <tr>
            <td class="label">Shift :</td><td>{{ $shift }}</td>
            <td class="label">Saus :</td><td colspan="3">{{ $saus }}</td>
        </tr>
    </table>

    <table class="grid">
        <thead>
            <tr>
                <th class="tgl-col" rowspan="3">TANGGAL PRODUKSI</th>
                <th class="kode-col">KODE PRODUKSI</th>
                <th class="suhu-col">SUHU PENGUKURAN (⁰C)</th>
                <th class="brix-col">BRIX (%)</th>
                <th class="salt-col">SALT (%)</th>
                <th class="visc-col">VISCOSITAS (detik.milidetik)</th>
                <th class="bf1-col">Brookfield LV, S 64,. 30 % RPM  suhu saus 24 - 26 °C</th>
                <th class="bf2-col">Brookfield LV, S 64,. 30 % RPM (Setelah Frozen) suhu saus 24 - 26 °C</th>
            </tr>
            <tr>
                <th>Vegetable</th>
                <th rowspan="2">24 - 26</th>
                <th>'6 - 12</th>
                <th>'6 - 12</th>
                <th>20 - 50</th>
                <th>1000 - 3000 Cp</th>
                <th>1000 - 3000 Cp</th>
            </tr>
            <tr>
                <th>Teriyaki</th>
                <th>'33 - 38</th>
                <th>'14 - 17</th>
                <th>70 - 130</th>
                <th>3000 - 5000 Cp</th>
                <th>2500 - 3000 Cp</th>
            </tr>
        </thead>
        <tbody>
            {{-- Baris input --}}
            @forelse ($data as $index => $item)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                    <td>{{ $item->kode_produksi }}</td>
                    <td class="suhu-col">{{ $item->suhu_pengukuran }}</td>
                    <td class="brix-col">{{ $item->brix }}</td>
                    <td class="salt-col">{{ $item->salt }}</td>
                    <td class="visc-col">{{ $item->visco }}</td>
                    <td class="bf1-col">{{ $item->brookfield_sebelum }}</td>
                    <td class="bf2-col">{{ $item->brookfield_frozen }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="no-col">Tidak ada data parameter produk saus yoshinoya untuk tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="doc-code">QR 24/00</div>

    <div class="catatan">
        Catatan:
        <div class="line"></div>
    </div>

    <table class="footer">
        <tr>
            <td style="width:50%;">Diperiksa Oleh,</td>
            <td>Disetujui Oleh,</td>
        </tr>
        <tr>
            <td><div class="sign"></div><div class="role">QC</div></td>
            <td><div class="sign"></div><div class="role">SPV QC</div></td>
        </tr>
    </table>

    
</body>
</html>
