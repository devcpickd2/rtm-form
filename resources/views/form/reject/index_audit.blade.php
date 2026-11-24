
@extends('layouts.app')

@php
    $type_user = auth()->user()->type_user;
@endphp

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm">
        <div class="card-body">

            {{-- JUDUL --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-list-check"></i>
                    Data Monitoring False Rejection
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('reject.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data Monitoring False Rejection
                </a>
                <span class="mx-2">/</span>
                <span class="fw-bold">Data Audit</span>
            </div>

            {{-- INFORMASI AUDIT --}}
            <div class="alert audit-info d-flex align-items-start mb-4" role="alert">
                <i class="bi bi-shield-check me-3 fs-4"></i>
                <div>
                    <strong class="d-block mb-1">Halaman Data Audit</strong>
                    <div class="small">
                        Data Monitoring False Rejection merupakan snapshot atau salinan kondisi data saat pertama kali disimpan.
                        Data ini digunakan untuk pemeriksaan dan audit serta terpisah dari data operasional asli.
                    </div>
                </div>
            </div>

            {{-- FILTER --}}
            <form method="GET"
                  action="{{ route('reject.audit') }}"
                  class="d-flex flex-wrap align-items-end gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                <div>
                    <label for="filter_date" class="form-label mb-1">Tanggal Produksi</label>
                    <input type="date"
                           name="date"
                           id="filter_date"
                           class="form-control form-control-sm"
                           value="{{ request('date') }}">
                </div>

                <div style="min-width:250px;">
                    <label for="search" class="form-label mb-1">Pencarian</label>
                    <input type="text"
                           name="search"
                           id="search"
                           class="form-control form-control-sm"
                           placeholder="Produk, kode produksi, mesin..."
                           value="{{ request('search') }}">
                </div>

                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-search"></i> Cari
                </button>

                @if(request('date') || request('search'))
                    <a href="{{ route('reject.audit') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                @endif
            </form>

            {{-- TABEL AUDIT --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date | Shift</th>
                            <th>Nama Mesin</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Monitoring</th>
                            <th>Catatan</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data as $row)
                            @php
                                $detailId = 'detailRejectAudit' . $row->uuid;

                                $namaMesin = match (strtolower((string) $row->nama_mesin)) {
                                    '1', 'x-ray' => 'X-Ray',
                                    '2', 'metal detector' => 'Metal Detector',
                                    default => $row->nama_mesin ?? '-',
                                };

                                $rejectData = [
                                    'jumlah_tidak_lolos' => $row->jumlah_tidak_lolos,
                                    'jumlah_kontaminan' => $row->jumlah_kontaminan,
                                    'jenis_kontaminan' => $row->jenis_kontaminan,
                                    'posisi_kontaminan' => $row->posisi_kontaminan,
                                    'false_rejection' => $row->false_rejection,
                                ];

                                $isEmpty = collect($rejectData)->every(function ($item) {
                                    return empty($item);
                                });
                            @endphp

                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $loop->index }}
                                </td>

                                <td class="text-center">
                                    {{ $row->date
                                        ? \Carbon\Carbon::parse($row->date)->format('d-m-Y')
                                        : '-' }}
                                    | Shift: {{ $row->shift ?? '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $namaMesin }}
                                </td>

                                <td>{{ $row->nama_produk ?? '-' }}</td>

                                <td class="text-center">
                                    {{ $row->kode_produksi ?? '-' }}
                                </td>

                                <td class="text-center">
                                    @if(!$isEmpty)
                                        <a href="javascript:void(0)"
                                           data-bs-toggle="collapse"
                                           data-bs-target="#{{ $detailId }}"
                                           class="fw-bold text-primary text-decoration-underline">
                                            Detail
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>{{ $row->catatan ?: '-' }}</td>

                                <td class="text-center">
                                    {{ $row->username ?? '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $row->nama_produksi ?? '-' }}
                                </td>

                                <td class="text-center">
                                    @if($row->status_spv == 0)
                                        <span class="fw-bold text-secondary">Created</span>
                                    @elseif($row->status_spv == 1)
                                        <span class="fw-bold text-success">Verified</span>
                                    @elseif($row->status_spv == 2)
                                        <span class="fw-bold text-danger">Revision</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if($type_user != 5)
                                        <a href="{{ route('reject.edit', $row->uuid) }}"
                                           class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i> Update
                                        </a>
                                    @else
                                        <span class="badge bg-secondary">Read Only</span>
                                    @endif
                                </td>
                            </tr>

                            {{-- DETAIL SNAPSHOT --}}
                            @if(!$isEmpty)
                                <tr class="collapse" id="{{ $detailId }}">
                                    <td colspan="11">
                                        <div class="p-3 bg-light rounded">

                                            <h6 class="fw-bold mb-3">
                                                Detail Monitoring False Rejection
                                            </h6>

                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <strong>Tanggal:</strong><br>
                                                    {{ $row->date
                                                        ? \Carbon\Carbon::parse($row->date)->format('d-m-Y')
                                                        : '-' }}
                                                </div>

                                                <div class="col-md-3">
                                                    <strong>Shift:</strong><br>
                                                    {{ $row->shift ?? '-' }}
                                                </div>

                                                <div class="col-md-3">
                                                    <strong>Nama Mesin:</strong><br>
                                                    {{ $namaMesin }}
                                                </div>

                                                <div class="col-md-3">
                                                    <strong>Source ID:</strong><br>
                                                    {{ $row->source_id ?? '-' }}
                                                </div>

                                                <div class="col-md-6">
                                                    <strong>Nama Produk:</strong><br>
                                                    {{ $row->nama_produk ?? '-' }}
                                                </div>

                                                <div class="col-md-6">
                                                    <strong>Kode Produksi:</strong><br>
                                                    {{ $row->kode_produksi ?? '-' }}
                                                </div>

                                                <div class="col-md-6">
                                                    <strong>Jumlah Pack/Tray yang Tidak Lolos:</strong><br>
                                                    {{ $row->jumlah_tidak_lolos ?? '-' }}
                                                </div>

                                                <div class="col-md-6">
                                                    <strong>Jumlah Pack/Tray yang Terdapat Kontaminan:</strong><br>
                                                    {{ $row->jumlah_kontaminan ?? '-' }}
                                                </div>

                                                <div class="col-md-6">
                                                    <strong>Jenis Kontaminan:</strong><br>
                                                    {{ $row->jenis_kontaminan ?? '-' }}
                                                </div>

                                                <div class="col-md-6">
                                                    <strong>Posisi Kontaminan:</strong><br>
                                                    {{ $row->posisi_kontaminan ?? '-' }}
                                                </div>

                                                <div class="col-md-6">
                                                    <strong>False Rejection:</strong><br>
                                                    {{ $row->false_rejection ?? '-' }}
                                                    /
                                                    {{ $row->jumlah_tidak_lolos ?? '-' }}
                                                </div>

                                                <div class="col-md-6">
                                                    <strong>Catatan:</strong><br>
                                                    {{ $row->catatan ?? '-' }}
                                                </div>
                                            </div>

                                            <hr>

                                            <h6 class="fw-bold mb-3">
                                                Informasi Verifikasi
                                            </h6>

                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <strong>Dibuat Oleh (QC):</strong><br>
                                                    {{ $row->username ?? '-' }}
                                                </div>

                                                <div class="col-md-3">
                                                    <strong>Diupdate Oleh:</strong><br>
                                                    {{ $row->username_updated ?? '-' }}
                                                </div>

                                                <div class="col-md-3">
                                                    <strong>Produksi:</strong><br>
                                                    {{ $row->nama_produksi ?? '-' }}
                                                </div>

                                                <div class="col-md-3">
                                                    <strong>Supervisor:</strong><br>
                                                    {{ $row->nama_spv ?? '-' }}
                                                </div>

                                                <div class="col-md-6">
                                                    <strong>Catatan SPV:</strong><br>
                                                    {{ $row->catatan_spv ?? '-' }}
                                                </div>

                                                <div class="col-md-6">
                                                    <strong>Status SPV:</strong><br>
                                                    @if($row->status_spv == 1)
                                                        <span class="badge bg-success">Verified</span>
                                                    @elseif($row->status_spv == 2)
                                                        <span class="badge bg-danger">Revision</span>
                                                    @else
                                                        <span class="badge bg-secondary">Pending</span>
                                                    @endif
                                                </div>

                                                <div class="col-md-6">
                                                    <strong>Waktu Update Produksi:</strong><br>
                                                    {{ $row->tgl_update_produksi
                                                        ? \Carbon\Carbon::parse($row->tgl_update_produksi)->format('d-m-Y H:i:s')
                                                        : '-' }}
                                                </div>

                                                <div class="col-md-6">
                                                    <strong>Waktu Verifikasi SPV:</strong><br>
                                                    {{ $row->tgl_update_spv
                                                        ? \Carbon\Carbon::parse($row->tgl_update_spv)->format('d-m-Y H:i:s')
                                                        : '-' }}
                                                </div>
                                            </div>

                                            <div class="mt-3">
                                                <span class="badge bg-dark">Snapshot Audit</span>
                                                <small class="text-muted ms-2">
                                                    Source UUID: {{ $row->source_uuid ?? '-' }}
                                                </small>
                                            </div>

                                        </div>
                                    </td>
                                </tr>
                            @endif

                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    Belum ada data Audit Monitoring False Rejection.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- INFORMASI DATA --}}
            @if($data->total() > 0)
                <div class="text-muted small mb-2">
                    Menampilkan {{ $data->firstItem() }}–{{ $data->lastPage() > 0 ? $data->lastItem() : 0 }}
                    dari {{ $data->total() }} data
                </div>
            @endif

            {{-- PAGINATION --}}
            <div class="mt-3">
                {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

            {{-- EXPORT PDF --}}
            @if(in_array($type_user, [0, 2, 8]))
                <form method="GET" action="{{ route('reject.exportPdf') }}" class="mt-3">
                    <div class="d-flex flex-wrap align-items-end gap-2">
                        <div>
                            <label for="export_date" class="form-label fw-semibold">
                                Pilih Tanggal
                            </label>
                            <input type="date"
                                   id="export_date"
                                   name="date"
                                   class="form-control form-control-sm"
                                   value="{{ request('date') }}"
                                   required>
                        </div>

                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-file-earmark-pdf"></i> Export PDF
                        </button>
                    </div>
                </form>
            @endif

        </div>
    </div>
</div>

<style>
    .audit-breadcrumb {
        background: #dc3545;
        border: 1px solid #dc3545;
        border-radius: 8px;
        padding: 11px 15px;
        font-size: .88rem;
        color: #fff;
        box-shadow: 0 2px 5px rgba(220, 53, 69, .15);
    }

    .audit-breadcrumb a {
        color: #fff;
        font-weight: 600;
        text-decoration: none;
        transition: .2s ease;
    }

    .audit-breadcrumb a:hover {
        color: #ffe3e6;
        text-decoration: underline;
    }

    .audit-breadcrumb span {
        color: #fff !important;
    }

    .audit-info {
        background: #f5f0ff;
        border: 1px solid #e9d5ff;
        border-left: 4px solid #7c3aed;
        border-radius: 8px;
        color: #4c1d95;
        padding: 14px 16px;
    }

    .audit-info i {
        color: #7c3aed;
    }

    .audit-info strong {
        color: #6b21a8;
        font-size: .95rem;
    }

    .audit-badge {
        background: #7c3aed;
        color: #fff;
        font-size: .65rem;
        font-weight: 700;
        letter-spacing: .5px;
        padding: 6px 10px;
        border-radius: 5px;
        vertical-align: middle;
    }

    .table td,
    .table th {
        font-size: .85rem;
        white-space: nowrap;
    }
</style>
@endsection
