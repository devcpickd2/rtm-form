@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3><i class="bi bi-trash"></i> Recycle Bin Tahapan Suhu Produk Setiap Tahapan Proses</h3>
                <a href="{{ route('tahapan.verification') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle mb-0">
                    <thead class="table-danger text-center">
                       <tr>
                        <th rowspan="2">NO.</th>
                        <th rowspan="2">Date | Shift</th>
                        <th rowspan="2">Nama Produk</th>
                        <th rowspan="2">Kode Produksi</th>
                        <th colspan="8">JAM MULAI</th>
                        <th>SUHU PRODUK (°C)</th>
                        <th rowspan="2">Catatan</th>
                        <th rowspan="2">QC</th>
                        <th rowspan="2">Dihapus Pada</th>
                        <th rowspan="2">Action</th>
                    </tr>
                    <tr>
                        <th>Filling/Portioning</th>
                        <th>IQF</th>
                        <th>Top Sealer</th>
                        <th>X-Ray</th>
                        <th>Sticker</th>
                        <th>Shrink</th>
                        <th>Packing Box</th>
                        <th>Cold Storage</th>

                        <th>Result Suhu</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($tahapan as $item)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                        <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }} | Shift: {{ $item->shift }}</td>   
                        <td class="text-center align-middle">{{ $item->nama_produk }}</td>
                        <td class="text-center align-middle">{{ $item->kode_produksi }}</td>
                        <td class="text-center align-middle">
                            {{ $item->filling_mulai ? \Carbon\Carbon::parse($item->filling_mulai)->format('H:i') : '-' }}
                            -
                            {{ $item->filling_selesai ? \Carbon\Carbon::parse($item->filling_selesai)->format('H:i') : '-' }}

                            <br>

                            <small class="text-muted">
                                {{ $item->tanggal_filling ? \Carbon\Carbon::parse($item->tanggal_filling)->format('d-m-Y') : '' }}
                            </small>
                        </td>

                        <td class="text-center align-middle">
                            {{ $item->waktu_iqf ? \Carbon\Carbon::parse($item->waktu_iqf)->format('H:i') : '-' }}

                            <br>

                            <small class="text-muted">
                                {{ $item->tanggal_iqf ? \Carbon\Carbon::parse($item->tanggal_iqf)->format('d-m-Y') : '' }}
                            </small>
                        </td>

                        <td class="text-center align-middle">
                            {{ $item->waktu_sealer ? \Carbon\Carbon::parse($item->waktu_sealer)->format('H:i') : '-' }}

                            <br>

                            <small class="text-muted">
                                {{ $item->tanggal_sealer ? \Carbon\Carbon::parse($item->tanggal_sealer)->format('d-m-Y') : '' }}
                            </small>
                        </td>

                        <td class="text-center align-middle">
                            {{ $item->waktu_xray ? \Carbon\Carbon::parse($item->waktu_xray)->format('H:i') : '-' }}

                            <br>

                            <small class="text-muted">
                                {{ $item->tanggal_xray ? \Carbon\Carbon::parse($item->tanggal_xray)->format('d-m-Y') : '' }}
                            </small>
                        </td>

                        <td class="text-center align-middle">
                            {{ $item->waktu_sticker ? \Carbon\Carbon::parse($item->waktu_sticker)->format('H:i') : '-' }}

                            <br>

                            <small class="text-muted">
                                {{ $item->tanggal_sticker ? \Carbon\Carbon::parse($item->tanggal_sticker)->format('d-m-Y') : '' }}
                            </small>
                        </td>

                        <td class="text-center align-middle">
                            {{ $item->waktu_shrink ? \Carbon\Carbon::parse($item->waktu_shrink)->format('H:i') : '-' }}

                            <br>

                            <small class="text-muted">
                                {{ $item->tanggal_shrink ? \Carbon\Carbon::parse($item->tanggal_shrink)->format('d-m-Y') : '' }}
                            </small>
                        </td>

                        <td class="text-center align-middle">
                            {{ $item->waktu_packing ? \Carbon\Carbon::parse($item->waktu_packing)->format('H:i') : '-' }}

                            <br>

                            <small class="text-muted">
                                {{ $item->tanggal_packing ? \Carbon\Carbon::parse($item->tanggal_packing)->format('d-m-Y') : '' }}
                            </small>
                        </td>

                        <td class="text-center align-middle">
                            {{ $item->waktu_cs ? \Carbon\Carbon::parse($item->waktu_cs)->format('H:i') : '-' }}

                            <br>

                            <small class="text-muted">
                                {{ $item->tanggal_cs ? \Carbon\Carbon::parse($item->tanggal_cs)->format('d-m-Y') : '' }}
                            </small>
                        </td>
                        <td class="text-center align-middle">
                            @php

                            // ======================
                            // SUHU FILLING
                            // ======================
                            $suhu_filling = $item->suhu_filling;

                            if (is_array($suhu_filling)) {

                                $suhu_filling = $suhu_filling;

                            } elseif (!empty($suhu_filling) && is_string($suhu_filling)) {

                                $decoded = json_decode($suhu_filling, true);
                                $suhu_filling = is_array($decoded) ? $decoded : [];

                            } else {

                                $suhu_filling = [];
                            }

                            // Hilangkan data filling kosong
                            $suhu_filling = array_values(
                            array_filter($suhu_filling, function ($item) {
                                return !empty($item['nama_bahan']) || !empty($item['suhu']);
                            })
                            );

                            // ======================
                            // SUHU LAINNYA
                            // ======================
                            $suhuFields = [
                            'suhu_masuk_iqf',
                            'suhu_keluar_iqf',
                            'suhu_sealer',
                            'suhu_xray',
                            'suhu_sticker',
                            'suhu_shrink'
                            ];

                            $suhuData = [];

                            foreach ($suhuFields as $field) {

                                $value = $item->$field;

                                if (is_array($value)) {

                                    $suhuData[$field] = array_values(
                                    array_filter($value, function ($v) {
                                        return $v !== null && $v !== '';
                                    })
                                    );

                                } elseif (!empty($value) && is_string($value)) {

                                    $decoded = json_decode($value, true);

                                    $suhuData[$field] = is_array($decoded)
                                    ? array_values(
                                    array_filter($decoded, function ($v) {
                                        return $v !== null && $v !== '';
                                    })
                                    )
                                    : [];

                                } else {

                                    $suhuData[$field] = [];
                                }
                            }

                            // ======================
                            // TOTAL ROW
                            // ======================
                            $totalRow = max(
                            count($suhu_filling),
                            count($suhuData['suhu_masuk_iqf']),
                            count($suhuData['suhu_keluar_iqf']),
                            count($suhuData['suhu_sealer']),
                            count($suhuData['suhu_xray']),
                            count($suhuData['suhu_sticker']),
                            count($suhuData['suhu_shrink']),
                            1
                            );

                            @endphp

                            @if(
                            !empty($suhu_filling)
                            || !empty(array_filter($suhuData))
                            || !empty($item->downtime)
                            || !empty($item->suhu_cs)
                            )

                            <a href="#"
                            data-bs-toggle="modal"
                            data-bs-target="#suhuModal{{ $item->uuid }}"
                            style="font-weight:bold;text-decoration:underline;">
                            Result
                        </a>

                        <div class="modal fade"
                        id="suhuModal{{ $item->uuid }}"
                        tabindex="-1"
                        aria-labelledby="suhuModalLabel{{ $item->uuid }}"
                        aria-hidden="true">

                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">

                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title" id="suhuModalLabel{{ $item->uuid }}">
                                        Detail Pengecekan Suhu Produk
                                    </h5>

                                    <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body p-0">

                                    <div class="table-responsive">

                                        <table class="table table-bordered table-sm mb-0 text-center">

                                            <thead class="table-light">

                                                <tr>
                                                    <th rowspan="2">No</th>
                                                    <th rowspan="2">Filling</th>
                                                    <th colspan="6">Tahapan Lainnya</th>
                                                    <th rowspan="2">Downtime</th>
                                                    <th rowspan="2">Cold Storage</th>
                                                </tr>

                                                <tr>
                                                    <th>Masuk IQF</th>
                                                    <th>Keluar IQF</th>
                                                    <th>Top Sealer</th>
                                                    <th>X-Ray</th>
                                                    <th>Sticker</th>
                                                    <th>Shrink</th>
                                                </tr>

                                            </thead>

                                            <tbody>

                                                @for($i = 0; $i < $totalRow; $i++)

                                                <tr>

                                                    <td>{{ $i + 1 }}</td>

                                                    <td class="text-start">

                                                        @if(isset($suhu_filling[$i]))

                                                        {{ $suhu_filling[$i]['nama_bahan'] ?? '-' }}

                                                        @if(isset($suhu_filling[$i]['suhu']) && $suhu_filling[$i]['suhu'] !== '')
                                                        : {{ $suhu_filling[$i]['suhu'] }}°C
                                                        @endif

                                                        @else
                                                        -
                                                        @endif

                                                    </td>

                                                    <td>
                                                        {{ isset($suhuData['suhu_masuk_iqf'][$i])
                                                        ? $suhuData['suhu_masuk_iqf'][$i].'°C'
                                                        : '-' }}
                                                    </td>

                                                    <td>
                                                        {{ isset($suhuData['suhu_keluar_iqf'][$i])
                                                        ? $suhuData['suhu_keluar_iqf'][$i].'°C'
                                                        : '-' }}
                                                    </td>

                                                    <td>
                                                        {{ isset($suhuData['suhu_sealer'][$i])
                                                        ? $suhuData['suhu_sealer'][$i].'°C'
                                                        : '-' }}
                                                    </td>

                                                    <td>
                                                        {{ isset($suhuData['suhu_xray'][$i])
                                                        ? $suhuData['suhu_xray'][$i].'°C'
                                                        : '-' }}
                                                    </td>

                                                    <td>
                                                        {{ isset($suhuData['suhu_sticker'][$i])
                                                        ? $suhuData['suhu_sticker'][$i].'°C'
                                                        : '-' }}
                                                    </td>

                                                    <td>
                                                        {{ isset($suhuData['suhu_shrink'][$i])
                                                        ? $suhuData['suhu_shrink'][$i].'°C'
                                                        : '-' }}
                                                    </td>

                                                    @if($i == 0)

                                                    <td rowspan="{{ $totalRow }}" class="align-middle">
                                                        {{ $item->downtime !== null && $item->downtime !== '' ? $item->downtime . ' Menit' : '-' }}
                                                    </td>

                                                    <td rowspan="{{ $totalRow }}" class="align-middle">
                                                        {{ $item->suhu_cs !== null && $item->suhu_cs !== '' ? $item->suhu_cs . ' °C' : '-' }}
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="p-3">
                                        <div class="alert alert-light mb-0">
                                            <strong>Catatan :</strong><br>
                                            {!! !empty($item->catatan) ? nl2br(e($item->catatan)) : '-' !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button"
                                    class="btn btn-secondary btn-sm"
                                    data-bs-dismiss="modal">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <span>-</span>
                @endif
            </td>
            <td class="text-center align-middle">{{ $item->keterangan ?? '-' }}</td>
            <td class="text-center align-middle">{{ $item->username }}</td>
            <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->deleted_at)->format('d-m-Y H:i') }}</td>

            <td class="text-center align-middle">
                <form action="{{ route('tahapan.restore', $item->uuid) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-success btn-sm mb-1">
                        <i class="bi bi-arrow-clockwise"></i> Restore
                    </button>
                </form>

                <form action="{{ route('tahapan.deletePermanent', $item->uuid) }}" 
                  method="POST" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-danger btn-sm mb-1"
                  onclick="return confirm('Hapus permanen?')">
                  <i class="bi bi-x-circle"></i> Delete
              </button>
          </form>
      </td>
  </tr>
  @empty
  <tr>
    <td colspan="18" class="text-center align-middle">Recycle bin kosong.</td>
</tr>
@endforelse
</tbody>
</table>
</div>

<div class="d-flex justify-content-end mt-2">
    {{ $tahapan->links('pagination::bootstrap-5') }}
</div>
</div>
</div>

</div>
@endsection
