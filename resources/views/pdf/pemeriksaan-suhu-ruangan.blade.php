@php
    use Carbon\Carbon;
    $company  = $company  ?? 'PT. Charoen Pokphand Indonesia';
    $division = $division ?? 'Food Division';
    $title    = $title    ?? 'PEMERIKSAAN SUHU RUANGAN';
    $doc_code = $doc_code ?? 'QR 02/05';
    $tanggal  = $tanggal  ?? '________________';
    $shift    = $shift    ?? '________';

    // Nama kolom ruangan
    $rooms = [
        'Chill Room RM<br><small>0 - 4</small>',
        'Cold Stor 1 RM<br><small>-20 ± 2</small>',
        'Cold Stor 2 RM<br><small>-20 ± 2</small>',
        'Anteroom RM<br><small>8 - 10</small>',
        'Seasoning T (°C)<br><small>22 - 30</small>',
        'Seasoning RH (%)<br><small>≤ 75</small>',
        'Prep. Room<br><small>9 - 15</small>',
        'Cooking Room<br><small>20 - 30</small>',
        'Filling Room<br><small>9 - 15</small>',
        'Rice Room<br><small>20 - 30</small>',
        'Noodle Room<br><small>20 - 30</small>',
        'Topping Area<br><small>9 - 15</small>',
        'Packing (karton)<br><small>9 - 15</small>',
        'Dry Store T (°C)<br><small>20 - 30</small>',
        'Dry Store RH (%)<br><small>≤ 75</small>',
        'Cold Stor FG<br><small>-19 ± 1</small>',
        'Anteroom FG<br><small>0 - 10</small>',
    ];

    $data      = $data      ?? collect(); // Ensure $data is a collection
    $firstItem = $data->isNotEmpty() ? $data->first() : null;

    // Ensure $data is always a collection, even if empty, to allow @foreach to run
    if ($data->isEmpty()) {
        $data = collect([ (object)[
            'date' => null, 'pukul' => null, 'shift' => null,
            'chillroom' => null, 'cs_1' => null, 'cs_2' => null, 'anteroom_rm' => null,
            'seasoning_suhu' => null, 'seasoning_rh' => null,
            'rice' => null, 'noodle' => null, 'prep_room' => null, 'cooking' => null,
            'filling' => null, 'topping' => null, 'packing' => null,
            'ds_suhu' => null, 'ds_rh' => null,
            'cs_fg' => null, 'anteroom_fg' => null,
            'keterangan' => null, 'catatan' => null,
            'status_produksi' => 0, 'status_spv' => 0
        ] ]);
        $firstItem = $data->first();
    }

    // Create a map of data by hour for easy lookup
    $dataByHour = $data->keyBy('pukul');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $title }}</title>
<style>
    @page { size: A4 landscape; margin: 8mm 8mm 12mm 8mm; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9px; color:#000; }

    .header-top { font-size:9px; }
    .title { text-align:center; font-weight:700; font-size:13px; margin:4px 0 6px; }

    .meta { width:100%; margin-bottom:5px; font-size:9px; }
    .meta td { padding:2px 6px; }

    table.grid { width:100%; border-collapse:collapse; font-size:8px; }
    table.grid th, table.grid td { border:1px solid #000; padding:1px 2px; }
    table.grid th { background:#f7f7f7; text-align:center; vertical-align:middle; }
    table.grid td { height:12px; text-align:center; }

    .catatan { margin-top:4px; font-size:9px; }
    .catatan-line { border:1px solid #000; border-top:0; height:20px; margin-top:2px; }

    .sign { margin-top:8mm; width:100%; font-size:9px; }
    .sign td { width:50%; text-align:left; padding-left:4mm; }
    .sign .line-sign { border-bottom:1px solid #000; width:60%; display:inline-block; margin:0 5mm; }
    .doc-code { position: fixed; right: 8mm; bottom: 22mm; font-size:9px; }
</style>
</head>
<body>
    <div class="header-top">{{ $company }}<br>{{ $division }}</div>
    <div class="title">{{ $title }}</div>

    <table class="meta">
        <tr>
            <td style="width:12%">Hari / Tanggal :</td><td style="width:25%">{{ Carbon::parse($tanggal ?? null)->format('d/m/Y') }}</td>
            <td style="width:8%">Shift :</td><td>{{ $shift ?? '-' }}</td>
        </tr>
    </table>

    <table class="grid">
        <tr>
            <th rowspan="2" style="width:28px;">Pukul</th>
            <th colspan="{{ count($rooms) }}">Ruangan (°C)</th>
            <th rowspan="2" style="width:80px;">Keterangan</th>
            <th colspan="2" rowspan="2" style="width:50px;">PARAF</th>
        </tr>
        <tr>
            @foreach($rooms as $room)
                <th>{!! $room !!}</th>
            @endforeach
        </tr>

        {{-- STD row --}}
        <tr>
            <td>STD (°C)</td>
            @foreach($rooms as $room)
                <td>
                    @php
                        // Extract standard range from room name (e.g., "0 - 4" from "Chill Room RM<br><small>0 - 4</small>")
                        preg_match('/<small>(.*?)<\/small>/', $room, $matches);
                        echo $matches[1] ?? '-';
                    @endphp
                </td>
            @endforeach
            <td></td><td></td><td></td>
        </tr>

        {{-- Jam 0:00 - 23:00 --}}
        @for($h = 0; $h < 24; $h++)
            @php
                $hourString = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
                $suhuItem = $dataByHour->get($hourString);
            @endphp
            <tr>
                <td>{{ $hourString }}</td>
                <td>{{ $suhuItem->chillroom ?? '-' }}</td>
                <td>{{ $suhuItem->cs_1 ?? '-' }}</td>
                <td>{{ $suhuItem->cs_2 ?? '-' }}</td>
                <td>{{ $suhuItem->anteroom_rm ?? '-' }}</td>
                <td>{{ $suhuItem->seasoning_suhu ?? '-' }}</td>
                <td>{{ $suhuItem->seasoning_rh ?? '-' }}</td>
                <td>{{ $suhuItem->prep_room ?? '-' }}</td>
                <td>{{ $suhuItem->cooking ?? '-' }}</td>
                <td>{{ $suhuItem->filling ?? '-' }}</td>
                <td>{{ $suhuItem->rice ?? '-' }}</td>
                <td>{{ $suhuItem->noodle ?? '-' }}</td>
                <td>{{ $suhuItem->topping ?? '-' }}</td>
                <td>{{ $suhuItem->packing ?? '-' }}</td>
                <td>{{ $suhuItem->ds_suhu ?? '-' }}</td>
                <td>{{ $suhuItem->ds_rh ?? '-' }}</td>
                <td>{{ $suhuItem->cs_fg ?? '-' }}</td>
                <td>{{ $suhuItem->anteroom_fg ?? '-' }}</td>
                <td>{{ $suhuItem->keterangan ?? '-' }}</td>
                <td>
                    @if (($suhuItem->status_produksi ?? 0) == 1)
                        ✔
                    @elseif (($suhuItem->status_produksi ?? 0) == 2)
                        TIDAK OK
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if (($suhuItem->status_spv ?? 0) == 1)
                        ✔
                    @elseif (($suhuItem->status_spv ?? 0) == 2)
                        TIDAK OK
                    @else
                        -
                    @endif
                </td>
            </tr>
        @endfor
    </table>

    <div class="catatan">Catatan: {{ $firstItem->catatan ?? '-' }}</div>
    <div class="catatan-line"></div>

    <table class="sign">
        <tr>
            <td>Diperiksa Oleh: <span class="line-sign"></span></td>
            <td>Disetujui Oleh: <span class="line-sign"></span></td>
        </tr>
    </table>

    <div class="doc-code">{{ $doc_code }}</div>
</body>
</html>
