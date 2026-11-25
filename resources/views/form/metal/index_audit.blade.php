
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
                    Data Pemeriksaan Metal Detector
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('metal.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data Pemeriksaan Metal Detector
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
                        Data Pemeriksaan Metal Detector merupakan snapshot atau salinan kondisi data saat pertama kali disimpan.
                        Data ini digunakan untuk pemeriksaan dan audit serta terpisah dari data operasional asli.
                    </div>
                </div>
            </div>

            {{-- FILTER --}}
            <form method="GET"
                  action="{{ route('metal.audit') }}"
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
                           placeholder="Produk, kode produksi, no. program..."
                           value="{{ request('search') }}">
                </div>

                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-search"></i> Cari
                </button>

                @if(request('date') || request('search'))
                    <a href="{{ route('metal.audit') }}" class="btn btn-outline-secondary btn-sm">
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
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>No. Program</th>
                            <th>Pemeriksaan</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Waktu Snapshot</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data as $dep)
                            @php
                                $pemeriksaan = $dep->pemeriksaan ?? [];

                                while (is_string($pemeriksaan)) {
                                    $decoded = json_decode($pemeriksaan, true);

                                    if (!is_array($decoded)) {
                                        $pemeriksaan = [];
                                        break;
                                    }

                                    $pemeriksaan = $decoded;
                                }

                                if (!is_array($pemeriksaan)) {
                                    $pemeriksaan = [];
                                }

                                $detailId = 'pemeriksaanAudit' . $dep->uuid;
                            @endphp

                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $loop->index }}
                                </td>

                                <td class="text-center">
                                    {{ $dep->date ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y') : '-' }}
                                    | Shift: {{ $dep->shift ?? '-' }}
                                </td>

                                <td>{{ $dep->nama_produk ?? '-' }}</td>

                                <td class="text-center">
                                    {{ $dep->kode_produksi ?? '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $dep->no_program ?? '-' }}
                                </td>

                                <td class="text-center">
                                    @if(count($pemeriksaan))
                                        <a href="javascript:void(0)"
                                           data-bs-toggle="modal"
                                           data-bs-target="#{{ $detailId }}"
                                           class="fw-bold text-primary text-decoration-underline">
                                            Result ({{ count($pemeriksaan) }})
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="text-center">
                                    {{ $dep->username ?? '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $dep->nama_produksi ?? '-' }}
                                </td>

                                <td class="text-center">
                                    @if($dep->status_spv == 0)
                                        <span class="fw-bold text-secondary">Created</span>
                                    @elseif($dep->status_spv == 1)
                                        <span class="fw-bold text-success">Verified</span>
                                    @elseif($dep->status_spv == 2)
                                        <a href="javascript:void(0)"
                                           data-bs-toggle="modal"
                                           data-bs-target="#revisionAudit{{ $dep->uuid }}"
                                           class="fw-bold text-danger text-decoration-none">
                                            Revision
                                        </a>

                                        {{-- MODAL CATATAN REVISI --}}
                                        <div class="modal fade"
                                             id="revisionAudit{{ $dep->uuid }}"
                                             tabindex="-1"
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Detail Revisi</h5>
                                                        <button type="button"
                                                                class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <p class="mb-2">
                                                            <strong>Status:</strong> Revision
                                                        </p>
                                                        <p class="mb-0">
                                                            <strong>Catatan:</strong><br>
                                                            {{ $dep->catatan_spv ?? '-' }}
                                                        </p>
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
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    {{ $dep->created_at
                                        ? \Carbon\Carbon::parse($dep->created_at)->format('d-m-Y H:i:s')
                                        : '-' }}
                                </td>

                                <td class="text-center">
                                    @if($type_user != 5)
                                        <a href="{{ route('metal.edit', $dep->uuid) }}"
                                           class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i> Update
                                        </a>
                                    @else
                                        <span class="badge bg-secondary">Read Only</span>
                                    @endif
                                </td>
                            </tr>

                            {{-- MODAL DETAIL PEMERIKSAAN --}}
                            @if(count($pemeriksaan))
                                <div class="modal fade"
                                     id="{{ $detailId }}"
                                     tabindex="-1"
                                     aria-labelledby="{{ $detailId }}Label"
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title" id="{{ $detailId }}Label">
                                                    Detail Pemeriksaan Metal Detector
                                                </h5>
                                                <button type="button"
                                                        class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-3">
                                                        <strong>Tanggal:</strong><br>
                                                        {{ $dep->date
                                                            ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                                            : '-' }}
                                                    </div>

                                                    <div class="col-md-3">
                                                        <strong>Shift:</strong><br>
                                                        {{ $dep->shift ?? '-' }}
                                                    </div>

                                                    <div class="col-md-3">
                                                        <strong>Nama Produk:</strong><br>
                                                        {{ $dep->nama_produk ?? '-' }}
                                                    </div>

                                                    <div class="col-md-3">
                                                        <strong>Kode Produksi:</strong><br>
                                                        {{ $dep->kode_produksi ?? '-' }}
                                                    </div>

                                                    <div class="col-md-3">
                                                        <strong>No. Program:</strong><br>
                                                        {{ $dep->no_program ?? '-' }}
                                                    </div>
                                                </div>

                                                <h6 class="fw-bold mt-3">
                                                    Detail Pemeriksaan Metal Detector
                                                </h6>

                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped table-sm align-middle">
                                                        <thead class="table-primary text-center">
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Pukul</th>
                                                                <th>Fe (1.5)</th>
                                                                <th>Non Fe (2.0)</th>
                                                                <th>SUS 316 (2.5)</th>
                                                                <th>Keterangan</th>
                                                                <th>Tindakan Koreksi</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            @forelse($pemeriksaan as $item)
                                                                <tr>
                                                                    <td class="text-center">
                                                                        {{ $loop->iteration }}
                                                                    </td>

                                                                    <td class="text-center">
                                                                        {{ $item['pukul'] ?? '-' }}
                                                                    </td>

                                                                    <td class="text-center">
                                                                        @if(($item['fe'] ?? '') === 'Oke')
                                                                            <span class="badge bg-success">Oke</span>
                                                                        @else
                                                                            <span class="badge bg-danger">
                                                                                {{ $item['fe'] ?? 'Tidak Oke' }}
                                                                            </span>
                                                                        @endif
                                                                    </td>

                                                                    <td class="text-center">
                                                                        @if(($item['non_fe'] ?? '') === 'Oke')
                                                                            <span class="badge bg-success">Oke</span>
                                                                        @else
                                                                            <span class="badge bg-danger">
                                                                                {{ $item['non_fe'] ?? 'Tidak Oke' }}
                                                                            </span>
                                                                        @endif
                                                                    </td>

                                                                    <td class="text-center">
                                                                        @if(($item['sus_316'] ?? '') === 'Oke')
                                                                            <span class="badge bg-success">Oke</span>
                                                                        @else
                                                                            <span class="badge bg-danger">
                                                                                {{ $item['sus_316'] ?? 'Tidak Oke' }}
                                                                            </span>
                                                                        @endif
                                                                    </td>

                                                                    <td class="text-center">
                                                                        @if(($item['keterangan'] ?? '') === 'Terdeteksi')
                                                                            <span class="badge bg-danger">Terdeteksi</span>
                                                                        @elseif(($item['keterangan'] ?? '') === 'Tidak Terdeteksi')
                                                                            <span class="badge bg-success">Tidak Terdeteksi</span>
                                                                        @else
                                                                            {{ $item['keterangan'] ?? '-' }}
                                                                        @endif
                                                                    </td>

                                                                    <td>
                                                                        {{ $item['tindakan_koreksi'] ?? '-' }}
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="7" class="text-center">
                                                                        Tidak ada detail pemeriksaan.
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="row g-3 mt-2">
                                                    <div class="col-md-6">
                                                        <strong>Catatan QC:</strong><br>
                                                        {{ $dep->catatan ?? '-' }}
                                                    </div>

                                                    <div class="col-md-6">
                                                        <strong>Catatan SPV:</strong><br>
                                                        {{ $dep->catatan_spv ?? '-' }}
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <strong>Dibuat Oleh (QC):</strong><br>
                                                        {{ $dep->username ?? '-' }}
                                                    </div>

                                                    <div class="col-md-4">
                                                        <strong>Produksi:</strong><br>
                                                        {{ $dep->nama_produksi ?? '-' }}
                                                    </div>

                                                    <div class="col-md-4">
                                                        <strong>Supervisor:</strong><br>
                                                        {{ $dep->nama_spv ?? '-' }}
                                                    </div>

                                                    <div class="col-md-6">
                                                        <strong>Update Produksi:</strong><br>
                                                        {{ $dep->tgl_update_produksi
                                                            ? \Carbon\Carbon::parse($dep->tgl_update_produksi)->format('d-m-Y H:i')
                                                            : '-' }}
                                                    </div>

                                                    <div class="col-md-6">
                                                        <strong>Verifikasi SPV:</strong><br>
                                                        {{ $dep->tgl_update_spv
                                                            ? \Carbon\Carbon::parse($dep->tgl_update_spv)->format('d-m-Y H:i')
                                                            : '-' }}
                                                    </div>
                                                </div>

                                                <div class="mt-3">
                                                    <span class="badge bg-dark">Snapshot Audit</span>
                                                    <small class="text-muted ms-2">
                                                        Source UUID: {{ $dep->source_uuid ?? '-' }}
                                                    </small>
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
                            @endif

                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    Belum ada data Audit Metal Detector.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- INFORMASI DATA --}}
            @if($data->total() > 0)
                <div class="text-muted small mb-2">
                    Menampilkan {{ $data->firstItem() }}–{{ $data->lastItem() }}
                    dari {{ $data->total() }} data
                </div>
            @endif

            {{-- PAGINATION --}}
            <div class="mt-3">
                {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

            {{-- EXPORT PDF: mengikuti fitur pada halaman utama --}}
            @if(in_array($type_user, [0, 2, 8]))
                <form method="GET" action="{{ route('metal.exportPdfAudit') }}" class="mt-3">
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
