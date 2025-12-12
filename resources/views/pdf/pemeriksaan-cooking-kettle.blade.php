@php
    use Carbon\Carbon;
    $company   = $company   ?? 'PT. Charoen Pokphand Indonesia';
    $division  = $division  ?? 'Food Division';
    $title     = $title     ?? 'PEMERIKSAAN PEMASAKAN PRODUK DI STEAM / COOKING KETTLE';
    $doc_code  = $doc_code  ?? 'QR 07/03';
    $tanggal   = $tanggal   ?? '________________';
    $shift     = $shift     ?? '________';
    $produk    = $produk    ?? '________________';
    $jenis     = $jenis     ?? '________________';
    $kodeProd  = $kodeProd  ?? '________________';
    $waktu     = $waktu     ?? '________________';
    $mesin     = $mesin     ?? '________________';
    $rows      = (int)($rows ?? 18);   // atur jumlah baris

    $data      = $data      ?? collect(); // Ensure $data is a collection
    $firstItem = $data->isNotEmpty() ? $data->first() : null;
    $pemasakanData = $firstItem ? (json_decode($firstItem->pemasakan, true) ?? []) : [];

    // Ensure $data is always a collection, even if empty, to allow @foreach to run
    if ($data->isEmpty()) {
        $data = collect([ (object)[
            'date' => null, 'shift' => null, 'nama_produk' => null, 'sub_produk' => null,
            'jenis_produk' => null, 'kode_produksi' => null, 'waktu_mulai' => null,
            'waktu_selesai' => null, 'nama_mesin' => null, 'catatan' => null,
            'pemasakan' => '[]', 'status_produksi' => 0, 'status_spv' => 0
        ] ]);
        $firstItem = $data->first();
    }

    // Ensure $pemasakanData has at least one empty item if it's truly empty, to render table structure
    if (empty($pemasakanData)) {
        $pemasakanData = [[]];
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $title }}</title>
<style>
  @page { size: A4 landscape; margin: 10mm 10mm 14mm 10mm; }
  body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 10px; color:#000; }

  .header-top { font-size:9px; }
  .title { text-align:center; font-weight:700; font-size:13px; margin:4px 0 4px; }

  .meta { width:100%; font-size:9px; margin-bottom:6px; border:1px solid #000; border-bottom:0; }
  .meta td { padding:2px 6px; border-bottom:1px solid #000; }
  .meta .r { text-align:right; padding-right:10px; border-left:1px solid #000; }

  table.grid { width:100%; border-collapse:collapse; font-size:8.6px; }
  table.grid th, table.grid td { border:1px solid #000; padding:2px 3px; }
  table.grid th { text-align:center; background:#f7f7f7; font-weight:700; }
  table.grid td { height:14px; vertical-align:top; text-align:center; }

  .note, .catatan { font-size:9px; margin-top:6px; }
  .catatan-lines { border:1px solid #000; border-top:0; padding:8px 10px; height:28px; }
  .sign { margin-top:10mm; width:100%; }
  .sign td { text-align:center; vertical-align:bottom; }
  .sign .slot { width:33%; }
  .sign .line-sign { border-bottom:1px solid #000; margin:10mm 15mm 3px; height:0; }
  .doc-code { position: fixed; right: 10mm; bottom: 24mm; font-size:9px; }
</style>
</head>
<body>
  <div class="header-top">{{ $company }}<br>{{ $division }}</div>
  <div class="title">{{ $title }}</div>

  {{-- header info + shift di kanan --}}
  <table class="meta">
    <tr>
      <td style="width:12%">Hari / Tanggal :</td><td style="width:55%">{{ Carbon::parse($firstItem->date ?? null)->format('d/m/Y') }}</td>
      <td class="r" style="width:10%">Shift :</td><td style="width:23%">{{ $firstItem->shift ?? '-' }}</td>
    </tr>
    <tr><td>Nama Produk</td><td>: {{ $firstItem->nama_produk ?? '-' }}</td><td class="r"> </td><td></td></tr>
    <tr><td>Jenis Produk</td><td>: {{ $firstItem->jenis_produk ?? '-' }}</td><td class="r"> </td><td></td></tr>
    <tr><td>Kode Produksi</td><td>: {{ $firstItem->kode_produksi ?? '-' }}</td><td class="r"> </td><td></td></tr>
    <tr><td>Waktu (Start - Stop)</td><td>: {{ ($firstItem->waktu_mulai ?? '-') . ' - ' . ($firstItem->waktu_selesai ?? '-') }}</td><td class="r"> </td><td></td></tr>
    <tr><td>Mesin</td><td>: {{ $firstItem->nama_mesin ?? '-' }}</td><td class="r"> </td><td></td></tr>
  </table>

  <table class="grid">
    <tr>
      <th rowspan="2">Pukul</th>
      <th rowspan="2">Tahapan Proses</th>

      <th colspan="4">Bahan baku</th>

      <th colspan="6">Parameter Pemasakan</th>

      <th rowspan="2">Produk</th>
      <th colspan="4">Organoleptik</th>
      <th rowspan="2">Catatan</th>
    </tr>
    <tr>
      <th>Jenis bahan</th>
      <th>Jumlah Standar (Kg)</th>
      <th>Jumlah Aktual</th>
      <th>Sensori</th>

      <th>Lama Proses (menit)</th>
      <th colspan="2">Mixing Paddle</th>
      <th>Pressure (Bar)</th>
      <th>Temperature (°C) / Api</th>
      <th>Set Temperature / Real Temperature</th>

      <th>Setelah 1/30</th>
      <th>Warna</th><th>Aroma</th><th>Rasa</th><th>Tekstur</th>
    </tr>

    @foreach($pemasakanData as $pemasakanItem)
      <tr>
        <td>{{ ($pemasakanItem['waktu_mulai'] ?? '-') . ' - ' . ($pemasakanItem['waktu_selesai'] ?? '-') }}</td>
        <td>{{ $pemasakanItem['tahapan_proses'] ?? '-' }}</td> {{-- Assuming tahapan_proses exists --}}

        <td>{{ $pemasakanItem['jenis_bahan'] ?? '-' }}</td> {{-- Assuming jenis_bahan exists --}}
        <td>{{ $pemasakanItem['jumlah_standar'] ?? '-' }}</td> {{-- Assuming jumlah_standar exists --}}
        <td>{{ $pemasakanItem['jumlah_aktual'] ?? '-' }}</td> {{-- Assuming jumlah_aktual exists --}}
        <td>{{ $pemasakanItem['sensori_bahan_baku'] ?? '-' }}</td> {{-- Assuming sensori_bahan_baku exists --}}

        <td>{{ $pemasakanItem['lama_proses'] ?? '-' }}</td>
        <td colspan="2">-</td> {{-- Mixing Paddle - No direct mapping for two values --}}
        <td>{{ $pemasakanItem['pressure'] ?? '-' }}</td> {{-- Assuming pressure exists --}}
        <td>{{ $pemasakanItem['suhu_produk'] ?? '-' }}</td>
        <td>-</td> {{-- Set Temperature / Real Temperature - No direct mapping for two values --}}

        <td>{{ $pemasakanItem['produk_setelah'] ?? '-' }}</td> {{-- Assuming produk_setelah exists --}}
        <td>{{ $pemasakanItem['sensori_warna'] ?? '-' }}</td>
        <td>{{ $pemasakanItem['sensori_aroma'] ?? '-' }}</td>
        <td>{{ $pemasakanItem['sensori_rasa'] ?? '-' }}</td>
        <td>{{ $pemasakanItem['sensori_tekstur'] ?? '-' }}</td>
        <td>{{ $pemasakanItem['keterangan'] ?? '-' }}</td>
      </tr>
    @endforeach
    {{-- Fill remaining rows with hyphens if $pemasakanData has fewer than $rows items --}}
    @for($i=count($pemasakanData); $i < $rows; $i++)
      <tr>
        <td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td colspan="2">-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>
      </tr>
    @endfor
  </table>

  <div class="catatan">Catatan: {{ $firstItem->catatan ?? '-' }}</div>
  <div class="catatan-lines"></div>
  <div class="note">*Coret yang tidak perlu</div>

  <table class="sign">
    <tr>
      <td class="slot">Diperiksa oleh QC:<div class="line-sign"></div></td>
      <td class="slot">Diketahui oleh Produksi:<div class="line-sign"></div></td>
      <td class="slot">Disetujui oleh:<div class="line-sign"></div></td>
    </tr>
  </table>

  <div class="doc-code">{{ $doc_code }}</div>
</body>
</html>
