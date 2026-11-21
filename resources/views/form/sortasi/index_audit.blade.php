@extends('layouts.app')

@section('content')
    <div class="container-fluid py-0">

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>
                        <i class="bi bi-clipboard-check"></i>
                        Audit Sortasi Bahan Baku yang Tidak Sesuai
                    </h3>

                    <a href="{{ route('sortasi.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <form method="GET" action="{{ route('sortasi.audit') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

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
                            placeholder="Cari bahan / kode produksi / QC...">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Cari
                    </button>

                    <a href="{{ route('sortasi.audit') }}" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </form>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Data ini merupakan snapshot saat pertama kali disimpan.
                    Perubahan data operasional setelah snapshot dibuat
                    tidak mengubah data Audit.
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th rowspan="2">No.</th>
                                <th rowspan="2">Tanggal / Shift</th>
                                <th rowspan="2">Nama Bahan</th>
                                <th rowspan="2">Kode Produksi</th>
                                <th>Jumlah Bahan</th>
                                <th colspan="2">Jumlah Bahan Setelah Sortasi</th>
                                <th rowspan="2">Tindakan Koreksi</th>
                                <th rowspan="2">Catatan</th>
                                <th rowspan="2">Produksi</th>
                                <th rowspan="2">SPV</th>
                                <th rowspan="2">Waktu Snapshot</th>
                            </tr>
                            <tr>
                                <th>Sebelum Sortasi</th>
                                <th>Sesuai</th>
                                <th>Tidak Sesuai</th>
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
                                        <br>
                                        Shift: {{ $dep->shift }}
                                    </td>

                                    <td>{{ $dep->nama_bahan ?? '-' }}</td>

                                    <td>{{ $dep->kode_produksi ?? '-' }}</td>

                                    <td class="text-center">
                                        {{ $dep->jumlah_bahan ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->jumlah_sesuai ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->jumlah_tidak_sesuai ?? '-' }}
                                    </td>

                                    <td>{{ $dep->tindakan_koreksi ?? '-' }}</td>

                                    <td>{{ $dep->catatan ?? '-' }}</td>

                                    <td>
                                        {{ $dep->nama_produksi ?? '-' }}
                                        <hr class="my-1">

                                        @if ($dep->status_produksi == 0)
                                            <span class="fw-bold text-secondary">Created</span>
                                        @elseif ($dep->status_produksi == 1)
                                            <span class="fw-bold text-success">Checked</span>
                                        @elseif ($dep->status_produksi == 2)
                                            <span class="fw-bold text-danger">Recheck</span>
                                        @else
                                            {{ $dep->status_produksi ?? '-' }}
                                        @endif
                                    </td>

                                    <td>
                                        {{ $dep->nama_spv ?? '-' }}
                                        <hr class="my-1">

                                        @if ($dep->status_spv == 0)
                                            <span class="fw-bold text-secondary">Created</span>
                                        @elseif ($dep->status_spv == 1)
                                            <span class="fw-bold text-success">Verified</span>
                                        @elseif ($dep->status_spv == 2)
                                            <span class="fw-bold text-danger">Revision</span>
                                        @else
                                            {{ $dep->status_spv ?? '-' }}
                                        @endif

                                        @if ($dep->catatan_spv)
                                            <hr class="my-1">
                                            Catatan: {{ $dep->catatan_spv }}
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->created_at ? $dep->created_at->format('d-m-Y H:i') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-4">
                                        Belum ada data Audit Sortasi.
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
