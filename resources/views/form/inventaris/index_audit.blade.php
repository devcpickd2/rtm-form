@extends('layouts.app')

@section('content')
    <div class="container-fluid py-0">

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                    <div>
                        <h3>
                            <i class="bi bi-clipboard-check"></i>
                            Audit Inventaris Alat QC
                        </h3>
                        <small class="text-muted">
                            Riwayat pemeriksaan inventaris - hanya dapat dilihat
                        </small>
                    </div>

                    <a href="{{ route('inventaris.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                {{-- Filter --}}
                <form method="GET" action="{{ route('inventaris.audit') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                    <div class="input-group" style="max-width:220px;">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-calendar-date"></i>
                        </span>

                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>

                    <input type="text" name="search" class="form-control" style="max-width:300px;"
                        placeholder="Cari Cooking, Packing, Warehouse, QC..." value="{{ request('search') }}">

                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel"></i> Filter
                    </button>

                    <a href="{{ route('inventaris.audit') }}" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </form>

                <div class="mb-2 text-muted">
                    Total data Audit: {{ $data->total() }}
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>Shift</th>
                                <th>Area</th>
                                <th>Detail Alat</th>
                                <th>Kondisi Awal</th>
                                <th>Kondisi Akhir</th>
                                <th>Keterangan</th>
                                <th>QC Awal</th>
                                <th>QC Update</th>
                                <th>Waktu Snapshot</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($data as $dep)
                                @php
                                    $areaNama = match ((int) $dep->area) {
                                        1 => 'Cooking',
                                        2 => 'Packing',
                                        3 => 'Warehouse',
                                        default => $dep->area ?? '-',
                                    };

                                    $details = is_array($dep->detail)
                                        ? $dep->detail
                                        : json_decode($dep->detail ?? '[]', true) ?? [];
                                @endphp

                                <tr>
                                    <td class="text-center">
                                        {{ $data->firstItem() + $loop->index }}
                                    </td>

                                    <td class="text-center text-nowrap">
                                        {{ $dep->date ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y') : '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->shift ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $areaNama }}
                                    </td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#auditDetail{{ $dep->uuid }}">
                                            <i class="bi bi-eye"></i>
                                            Lihat {{ count($details) }} Alat
                                        </button>
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->kondisi_awal ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->kondisi_akhir ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $dep->keterangan ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->username ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->username_updated ?? '-' }}
                                    </td>

                                    <td class="text-center text-nowrap">
                                        {{ $dep->created_at ? \Carbon\Carbon::parse($dep->created_at)->format('d-m-Y H:i') : '-' }}
                                    </td>
                                </tr>

                                {{-- Modal detail alat --}}
                                <div class="modal fade" id="auditDetail{{ $dep->uuid }}" tabindex="-1"
                                    aria-hidden="true">

                                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">

                                            <div class="modal-header bg-info text-white">
                                                <div>
                                                    <h5 class="modal-title fw-bold">
                                                        Detail Audit Inventaris Alat QC
                                                    </h5>
                                                    <small>
                                                        {{ $areaNama }} |
                                                        {{ $dep->date ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y') : '-' }}
                                                        | Shift {{ $dep->shift }}
                                                    </small>
                                                </div>

                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal">
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm text-center align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Nama Alat</th>
                                                                <th>Jumlah</th>
                                                                <th>Kondisi Awal</th>
                                                                <th>Kondisi Akhir</th>
                                                                <th>Keterangan</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            @forelse ($details as $i => $alat)
                                                                <tr>
                                                                    <td>{{ $i + 1 }}</td>

                                                                    <td class="text-start">
                                                                        {{ $alat['nama_alat'] ?? '-' }}
                                                                    </td>

                                                                    <td>
                                                                        {{ $alat['jumlah'] ?? '-' }}
                                                                    </td>

                                                                    <td>
                                                                        {{ $alat['kondisi_awal'] ?? '-' }}
                                                                    </td>

                                                                    <td>
                                                                        {{ $alat['kondisi_akhir'] ?? '-' }}
                                                                    </td>

                                                                    <td class="text-start">
                                                                        {{ $alat['keterangan'] ?? '-' }}
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="6" class="text-center text-muted">
                                                                        Tidak ada detail alat.
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="border rounded p-3 mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-2">
                                                            <strong>QC Awal:</strong>
                                                            {{ $dep->username ?? '-' }}
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <strong>QC Update:</strong>
                                                            {{ $dep->username_updated ?? '-' }}
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <strong>Kondisi Awal:</strong>
                                                            {{ $dep->kondisi_awal ?? '-' }}
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <strong>Kondisi Akhir:</strong>
                                                            {{ $dep->kondisi_akhir ?? '-' }}
                                                        </div>

                                                        <div class="col-12">
                                                            <strong>Keterangan:</strong>
                                                            {{ $dep->keterangan ?? '-' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Tutup
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        Belum ada data Audit Inventaris.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>

    <style>
        .table td,
        .table th {
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .table thead th {
            background-color: #cfe2ff !important;
            color: #000 !important;
        }
    </style>
@endsection
