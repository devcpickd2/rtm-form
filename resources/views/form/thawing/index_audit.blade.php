@extends('layouts.app')

@section('content')
    <div class="container-fluid py-0">

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>
                        <i class="bi bi-clipboard-check"></i>
                        Audit Pemeriksaan Proses Thawing
                    </h3>

                    <a href="{{ route('thawing.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <form method="GET" action="{{ route('thawing.audit') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light">

                    <div class="input-group" style="max-width:220px;">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-calendar-date"></i>
                        </span>

                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>

                    <div class="input-group flex-grow-1" style="max-width:350px;">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>

                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="Cari jenis produk / kode produksi / QC...">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Cari
                    </button>

                    <a href="{{ route('thawing.audit') }}" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </form>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Data di halaman ini merupakan snapshot ketika data Thawing
                    pertama kali disimpan. Perubahan pada data operasional
                    berikutnya tidak mengubah data Audit.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th rowspan="2">No.</th>
                                <th rowspan="2">Tanggal</th>
                                <th rowspan="2">Kondisi Ruangan</th>
                                <th rowspan="2">Jenis Produk</th>
                                <th rowspan="2">Kode Produksi</th>
                                <th colspan="5">Sebelum Proses Thawing</th>
                                <th colspan="5">Setelah Proses Thawing</th>
                                <th rowspan="2">Produksi</th>
                                <th rowspan="2">SPV</th>
                                <th rowspan="2">Catatan</th>
                                <th rowspan="2">Waktu Snapshot</th>
                            </tr>
                            <tr>
                                <th>Jumlah</th>
                                <th>Kondisi Produk</th>
                                <th>Keterangan Kondisi</th>
                                <th>Suhu Ruangan °C</th>
                                <th>Mulai Thawing</th>

                                <th>Selesai Thawing</th>
                                <th>Kondisi Produk</th>
                                <th>Keterangan Kondisi</th>
                                <th>Jumlah</th>
                                <th>Suhu Produk °C</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($data as $index => $dep)
                                <tr>
                                    <td class="text-center">
                                        {{ $data->firstItem() + $index }}
                                    </td>

                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}
                                    </td>

                                    <td>{{ $dep->kondisi_ruangan ?? '-' }}</td>
                                    <td>{{ $dep->jenis_produk ?? '-' }}</td>
                                    <td>{{ $dep->kode_produksi ?? '-' }}</td>

                                    <td class="text-center">
                                        {{ $dep->jumlah ?? '-' }}
                                    </td>

                                    <td>{{ $dep->kondisi_produk ?? '-' }}</td>

                                    <td>
                                        {{ $dep->keterangan_kondisi ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->suhu_ruangan ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->mulai_thawing ? \Carbon\Carbon::parse($dep->mulai_thawing)->format('H:i') : '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->selesai_thawing ? \Carbon\Carbon::parse($dep->selesai_thawing)->format('H:i') : '-' }}
                                    </td>

                                    <td>{{ $dep->kondisi_produk_setelah ?? '-' }}</td>

                                    <td>
                                        {{ $dep->keterangan_kondisi_setelah ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->jumlah_setelah ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->suhu_produk ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $dep->nama_produksi ?? '-' }}
                                        <hr class="my-1">
                                        @if ($dep->status_produksi == 1)
                                            <span class="badge bg-success">Selesai</span>
                                        @else
                                            <span class="badge bg-secondary">
                                                {{ $dep->status_produksi ?? '-' }}
                                            </span>
                                        @endif
                                    </td>

                                    <td>
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

                                    <td>{{ $dep->catatan ?? '-' }}</td>

                                    <td class="text-center">
                                        {{ $dep->created_at ? $dep->created_at->format('d-m-Y H:i') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="19" class="text-center text-muted py-4">
                                        Belum ada data Audit Thawing.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>
@endsection
