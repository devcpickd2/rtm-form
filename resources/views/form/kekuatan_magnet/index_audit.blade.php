
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h3 class="mb-1">
                        <i class="bi bi-clipboard-check"></i>
                        Audit Pemeriksaan Kekuatan Magnet Trap
                    </h3>
                    <small class="text-muted">
                        Data snapshot saat pertama kali pemeriksaan disimpan.
                    </small>
                </div>

                @if ((int) auth()->user()->type_user !== 5)
                    <a href="{{ route('kekuatan_magnet.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('kekuatan_magnet.audit') }}"
                class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light">

                <div class="input-group" style="max-width:220px;">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-calendar-date"></i>
                    </span>
                    <input type="date" name="date" class="form-control"
                        value="{{ request('date') }}">
                </div>

                <div class="input-group" style="max-width:350px;">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control"
                        placeholder="Cari nama alat, gauss, kondisi..."
                        value="{{ request('search') }}">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="{{ route('kekuatan_magnet.audit') }}"
                    class="btn btn-outline-secondary">
                    Reset
                </a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Nama Alat</th>
                            <th>Nilai Pengukuran (Gauss)</th>
                            <th>Kondisi Magnet</th>
                            <th>Parameter Setting</th>
                            <th>Keterangan</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>Status Produksi</th>
                            <th>Waktu Input Produksi</th>
                            <th>SPV</th>
                            <th>Status SPV</th>
                            <th>Catatan SPV</th>
                            <th>Waktu Verifikasi SPV</th>
                            <th>Terakhir Diubah Oleh</th>
                            <th>Waktu Snapshot</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $item)
                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $loop->index }}
                                </td>
                                <td class="text-nowrap">
                                    {{ $item->date ? $item->date->format('d-m-Y') : '-' }}
                                </td>
                                <td class="text-center">{{ $item->shift ?? '-' }}</td>
                                <td>{{ $item->nama_alat ?? '-' }}</td>
                                <td class="text-center">{{ $item->gauss ?? '-' }}</td>
                                <td>{{ $item->kondisi ?? '-' }}</td>
                                <td class="text-center">
                                    @if ($item->parameter === 'sesuai')
                                        <span class="badge bg-success">Sesuai</span>
                                    @elseif ($item->parameter === 'tidak sesuai')
                                        <span class="badge bg-danger">Tidak Sesuai</span>
                                    @else
                                        {{ $item->parameter ?? '-' }}
                                    @endif
                                </td>
                                <td style="min-width:200px; white-space:pre-wrap;">
                                    {{ $item->keterangan ?? '-' }}
                                </td>
                                <td>{{ $item->username ?? '-' }}</td>
                                <td>{{ $item->nama_produksi ?? '-' }}</td>
                                <td class="text-center">
                                    {{ $item->status_produksi ?? '-' }}
                                </td>
                                <td class="text-nowrap">
                                    {{ $item->tgl_update_produksi ? \Carbon\Carbon::parse($item->tgl_update_produksi)->format('d-m-Y H:i:s') : '-' }}
                                </td>
                                <td>{{ $item->nama_spv ?? '-' }}</td>
                                <td class="text-center">
                                    @if ((string) $item->status_spv === '1')
                                        <span class="badge bg-success">Verified</span>
                                    @elseif ((string) $item->status_spv === '2')
                                        <span class="badge bg-danger">Revision</span>
                                    @elseif ((string) $item->status_spv === '0')
                                        <span class="badge bg-warning text-dark">Created</span>
                                    @else
                                        {{ $item->status_spv ?? '-' }}
                                    @endif
                                </td>
                                <td style="min-width:180px; white-space:pre-wrap;">
                                    {{ $item->catatan_spv ?? '-' }}
                                </td>
                                <td class="text-nowrap">
                                    {{ $item->tgl_update_spv ? \Carbon\Carbon::parse($item->tgl_update_spv)->format('d-m-Y H:i:s') : '-' }}
                                </td>
                                <td>{{ $item->username_updated ?? '-' }}</td>
                                <td class="text-nowrap">
                                    {{ $item->created_at ? $item->created_at->format('d-m-Y H:i:s') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="18" class="text-center py-4">
                                    Belum ada data Audit Kekuatan Magnet Trap.
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
