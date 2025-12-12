@php
    $company   = $company   ?? 'PT. Charoen Pokphand Indonesia';
    $division  = $division  ?? 'Food Division';
    $title     = $title     ?? 'PEMERIKSAAN PEMASAKAN DENGAN STEAMER';
    $doc_code  = $doc_code  ?? 'QF 07/07';
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

    @forelse ($data as $item)
        @php
            $steamingData = json_decode($item->steaming, true);
        @endphp

        @if(!empty($steamingData))
            @foreach($steamingData as $index => $steamingItem)
                <div style="margin-top: 8px; font-weight: bold;">Pemeriksaan {{ $index + 1 }}</div>
                <table class="grid">
                    <tr><th colspan="6" class="section-title">STEAMING</th></tr>
                    <tr>
                        <th>Kode Produksi</th>
                        <th>T. Raw Material (°C)</th>
                        <th>Jumlah Tray</th>
                        <th>T. Ruang (°C)</th>
                        <th>T. Produk (°C)</th>
                        <th>T. Produk 1 Menit (°C)</th>
                    </tr>
                    <tr>
                        <td>{{ $steamingItem['kode_produksi'] ?? '-' }}</td>
                        <td>{{ $steamingItem['suhu_rm'] ?? '-' }}</td>
                        <td>{{ $steamingItem['jumlah_tray'] ?? '-' }}</td>
                        <td>{{ $steamingItem['suhu_ruang'] ?? '-' }}</td>
                        <td>{{ $steamingItem['suhu_produk'] ?? '-' }}</td>
                        <td>{{ $steamingItem['suhu_after'] ?? '-' }}</td>
                    </tr>
                </table>

                <table class="grid">
                    <tr><th colspan="5" class="section-title">LAMA PROSES</th></tr>
                    <tr>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
                        <th>Lama (menit)</th>
                        <th>Keterangan</th>
                        <th>Paraf</th>
                    </tr>
                    <tr>
                        <td>{{ $steamingItem['jam_mulai'] ?? '-' }}</td>
                        <td>{{ $steamingItem['jam_selesai'] ?? '-' }}</td>
                        <td>{{ $steamingItem['waktu'] ?? '-' }}</td>
                        <td>{{ $steamingItem['keterangan'] ?? '-' }}</td>
                        <td>
                            @if ($item->status_produksi == 1)
                                ✔
                            @elseif ($item->status_produksi == 2)
                                TIDAK OK
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>

                <table class="grid">
                    <tr><th colspan="6" class="section-title">SENSORI</th></tr>
                    <tr>
                        <th>Kekenyalan</th>
                        <th>Warna</th>
                        <th>Aroma</th>
                        <th>Tekstur</th>
                        <th>Keterangan</th>
                        <th>Paraf</th>
                    </tr>
                    <tr>
                        <td>-</td> {{-- Data sensori tidak ada di model Steamer, jadi diisi default --}}
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>
                            @if ($item->status_spv == 1)
                                ✔
                            @elseif ($item->status_spv == 2)
                                TIDAK OK
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            @endforeach
        @else
            <table class="grid">
                <tr>
                    <td colspan="6" class="no-col">Tidak ada data steaming untuk produk ini.</td>
                </tr>
            </table>
        @endif
    @empty
        <table class="grid">
            <tr>
                <td colspan="6" class="no-col">Tidak ada data pemeriksaan pemasakan dengan steamer untuk tanggal ini.</td>
            </tr>
        </table>
    @endforelse

    <div class="note">
        Keterangan: ✔ = OK, × = tidak digunakan, TIDK OK = Tidak OK
    </div>

    <div class="catatan">Catatan:</div>
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
