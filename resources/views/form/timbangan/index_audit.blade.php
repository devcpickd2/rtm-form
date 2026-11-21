
@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>
                    <i class="bi bi-clipboard-check"></i>
                    Audit Peneraan Timbangan
                </h3>

                <a href="{{ route('timbangan.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="GET"
                  action="{{ route('timbangan.audit') }}"
                  class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light">

                <div class="input-group" style="max-width:220px;">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-calendar-date"></i>
                    </span>

                    <input type="date"
                           name="date"
                           class="form-control"
                           value="{{ request('date') }}">
                </div>

                <div class="input-group flex-grow-1" style="max-width:350px;">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>

                    <input type="text"
                           name="search"
                           class="form-control"
                           value="{{ request('search') }}"
                           placeholder="Cari kode timbangan / QC / catatan...">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="{{ route('timbangan.audit') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
            </form>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                Halaman ini menampilkan salinan data saat pertama kali disimpan.
                Data Audit bersifat hanya-baca dan tidak mengikuti perubahan
                pada data operasional setelah snapshot dibuat.
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th rowspan="2">No.</th>
                            <th rowspan="2">Tanggal / Shift</th>
                            <th>Kode Timbangan</th>
                            <th>Standar (gr)</th>
                            <th>Waktu Tera</th>
                            <th>Hasil Tera</th>
                            <th>Tindakan Perbaikan</th>
                            <th rowspan="2">QC</th>
                            <th rowspan="2">Produksi</th>
                            <th rowspan="2">SPV</th>
                            <th rowspan="2">Catatan</th>
                            <th rowspan="2">Waktu Snapshot</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $dep)
                            @php
                                $decodeArray = function ($value) {
                                    if (is_array($value)) {
                                        return $value;
                                    }

                                    if (is_string($value)) {
                                        $decoded = json_decode($value, true);
                                        return is_array($decoded) ? $decoded : [];
                                    }

                                    return [];
                                };

                                $kode = $decodeArray($dep->kode_timbangan);
                                $standar = $decodeArray($dep->standar);
                                $waktu = $decodeArray($dep->waktu_tera);
                                $hasil = $decodeArray($dep->hasil_tera);
                                $tindakan = $decodeArray($dep->tindakan_perbaikan);

                                $jumlah = max(
                                    count($kode),
                                    count($standar),
                                    count($waktu),
                                    count($hasil),
                                    count($tindakan),
                                    1
                                );
                            @endphp

                            @for ($i = 0; $i < $jumlah; $i++)
                                <tr>
                                    @if ($i === 0)
                                        <td rowspan="{{ $jumlah }}" class="text-center">
                                            {{ $data->firstItem() + $loop->parent->index }}
                                        </td>

                                        <td rowspan="{{ $jumlah }}" class="text-center">
                                            {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}
                                            <br>
                                            Shift {{ $dep->shift }}
                                        </td>
                                    @endif

                                    <td class="text-center">
                                        {{ $kode[$i] ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $standar[$i] ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $waktu[$i] ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $hasil[$i] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $tindakan[$i] ?? '-' }}
                                    </td>

                                    @if ($i === 0)
                                        <td rowspan="{{ $jumlah }}">
                                            {{ $dep->username ?? '-' }}
                                        </td>

                                        <td rowspan="{{ $jumlah }}">
                                            {{ $dep->nama_produksi ?? '-' }}
                                            <hr class="my-1">
                                            Status:
                                            @if ($dep->status_produksi == 1)
                                                <span class="badge bg-success">Selesai</span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    {{ $dep->status_produksi ?? '-' }}
                                                </span>
                                            @endif
                                        </td>

                                        <td rowspan="{{ $jumlah }}">
                                            {{ $dep->nama_spv ?? '-' }}
                                            <hr class="my-1">
                                            @if ($dep->status_spv == 1)
                                                <span class="badge bg-success">Verified</span>
                                            @elseif ($dep->status_spv == 2)
                                                <span class="badge bg-danger">Revision</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif

                                            @if ($dep->catatan_spv)
                                                <hr class="my-1">
                                                {{ $dep->catatan_spv }}
                                            @endif
                                        </td>

                                        <td rowspan="{{ $jumlah }}">
                                            {{ $dep->catatan ?? '-' }}
                                        </td>

                                        <td rowspan="{{ $jumlah }}" class="text-center">
                                            {{ $dep->created_at
                                                ? $dep->created_at->format('d-m-Y H:i')
                                                : '-' }}
                                        </td>
                                    @endif
                                </tr>
                            @endfor
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted py-4">
                                    Belum ada data Audit Peneraan Timbangan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $data->links() }}
            </div>

        </div>
    </div>
</div>
@endsection
