
@extends('layouts.app')

@php
    $user = auth()->user();
    $type_user = $user->type_user ?? null;
@endphp

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm">
        <div class="card-body">

            {{-- JUDUL --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-list-check"></i>
                    Data Pemeriksaan X RAY
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('xray.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data Pemeriksaan X RAY
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
                        Data Pemeriksaan X RAY merupakan snapshot atau salinan
                        kondisi data saat pertama kali disimpan. Data ini digunakan
                        untuk pemeriksaan dan audit serta terpisah dari data
                        operasional asli.
                    </div>
                </div>
            </div>

            {{-- FILTER --}}
            <form method="GET"
                action="{{ route('xray.audit') }}"
                class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                <div class="input-group" style="max-width:220px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-calendar-date text-muted"></i>
                    </span>
                    <input type="date"
                        name="date"
                        class="form-control border-start-0"
                        value="{{ request('date') }}">
                </div>

                <div class="input-group flex-grow-1" style="max-width:350px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text"
                        name="search"
                        class="form-control border-start-0"
                        placeholder="Cari produk, kode produksi, nomor program..."
                        value="{{ request('search') }}">
                </div>

                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-search me-1"></i>
                    Cari
                </button>

                @if(request('date') || request('search'))
                    <a href="{{ route('xray.audit') }}"
                        class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset
                    </a>
                @endif
            </form>

            {{-- TABEL DATA --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
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
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $index => $dep)
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

                                $modalId = 'pemeriksaanModal' . $dep->uuid;
                                $revisionId = 'revisionModal' . $dep->uuid;
                            @endphp

                            <tr>
                                <td class="text-center align-middle">
                                    {{ $data->firstItem() + $index }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->date
                                        ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                        : '-' }}
                                    | Shift: {{ $dep->shift ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->nama_produk ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->kode_produksi ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->no_program ?? '-' }}
                                </td>

                                {{-- PEMERIKSAAN --}}
                                <td class="text-center align-middle">
                                    @if (!empty($pemeriksaan))
                                        <a href="javascript:void(0)"
                                            data-bs-toggle="modal"
                                            data-bs-target="#{{ $modalId }}"
                                            class="fw-bold text-decoration-underline">
                                            Result
                                        </a>

                                        {{-- MODAL DETAIL X-RAY --}}
                                        <div class="modal fade"
                                            id="{{ $modalId }}"
                                            tabindex="-1"
                                            aria-labelledby="{{ $modalId }}Label"
                                            aria-hidden="true">

                                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                <div class="modal-content">

                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title"
                                                            id="{{ $modalId }}Label">
                                                            Detail Pemeriksaan X-Ray
                                                        </h5>

                                                        <button type="button"
                                                            class="btn-close"
                                                            data-bs-dismiss="modal"
                                                            aria-label="Close">
                                                        </button>
                                                    </div>

                                                    <div class="modal-body text-start">
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
                                                                <strong>No. Program:</strong><br>
                                                                {{ $dep->no_program ?? '-' }}
                                                            </div>
                                                        </div>

                                                        <h6 class="fw-bold mt-3">
                                                            Detail Pemeriksaan X-Ray
                                                        </h6>

                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-sm bg-white align-middle">
                                                                <thead class="table-primary text-center">
                                                                    <tr>
                                                                        <th rowspan="2">No.</th>
                                                                        <th rowspan="2">Pukul</th>
                                                                        <th colspan="2">Glass Ball</th>
                                                                        <th colspan="2">Ceramic</th>
                                                                        <th colspan="2">SUS 304 (Wire)</th>
                                                                        <th colspan="2">SUS 304 (Ball)</th>
                                                                        <th rowspan="2">Keterangan</th>
                                                                        <th rowspan="2">Tindakan Koreksi</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Ukuran</th>
                                                                        <th>Status</th>
                                                                        <th>Ukuran</th>
                                                                        <th>Status</th>
                                                                        <th>Ukuran</th>
                                                                        <th>Status</th>
                                                                        <th>Ukuran</th>
                                                                        <th>Status</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody>
                                                                    @forelse ($pemeriksaan as $item)
                                                                        <tr>
                                                                            <td class="text-center">
                                                                                {{ $loop->iteration }}
                                                                            </td>

                                                                            <td class="text-center">
                                                                                {{ $item['pukul'] ?? '-' }}
                                                                            </td>

                                                                            <td class="text-center">
                                                                                {{ $item['glass_ball'] ?? '-' }}
                                                                            </td>

                                                                            <td class="text-center">
                                                                                @if (($item['glass_ball_status'] ?? '') === 'Oke')
                                                                                    <span class="badge bg-success">Oke</span>
                                                                                @else
                                                                                    <span class="badge bg-danger">Tidak Oke</span>
                                                                                @endif
                                                                            </td>

                                                                            <td class="text-center">
                                                                                {{ $item['ceramic'] ?? '-' }}
                                                                            </td>

                                                                            <td class="text-center">
                                                                                @if (($item['ceramic_status'] ?? '') === 'Oke')
                                                                                    <span class="badge bg-success">Oke</span>
                                                                                @else
                                                                                    <span class="badge bg-danger">Tidak Oke</span>
                                                                                @endif
                                                                            </td>

                                                                            <td class="text-center">
                                                                                {{ $item['sus_wire'] ?? '-' }}
                                                                            </td>

                                                                            <td class="text-center">
                                                                                @if (($item['sus_wire_status'] ?? '') === 'Oke')
                                                                                    <span class="badge bg-success">Oke</span>
                                                                                @else
                                                                                    <span class="badge bg-danger">Tidak Oke</span>
                                                                                @endif
                                                                            </td>

                                                                            <td class="text-center">
                                                                                {{ $item['sus_ball'] ?? '-' }}
                                                                            </td>

                                                                            <td class="text-center">
                                                                                @if (($item['sus_ball_status'] ?? '') === 'Oke')
                                                                                    <span class="badge bg-success">Oke</span>
                                                                                @else
                                                                                    <span class="badge bg-danger">Tidak Oke</span>
                                                                                @endif
                                                                            </td>

                                                                            <td class="text-center">
                                                                                @if (($item['keterangan'] ?? '') === 'Terdeteksi')
                                                                                    <span class="badge bg-danger">Terdeteksi</span>
                                                                                @elseif (($item['keterangan'] ?? '') === 'Tidak Terdeteksi')
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
                                                                            <td colspan="12" class="text-center">
                                                                                Tidak ada detail pemeriksaan.
                                                                            </td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        <div class="row g-3 mt-2">
                                                            <div class="col-md-6">
                                                                <strong>Catatan:</strong><br>
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
                                                                <strong>QC:</strong><br>
                                                                {{ $dep->username ?? '-' }}
                                                            </div>

                                                            <div class="col-md-4">
                                                                <strong>Waktu Input:</strong><br>
                                                                {{ $dep->created_at?->format('d-m-Y H:i:s') ?? '-' }}
                                                            </div>

                                                            <div class="col-md-4">
                                                                <strong>Update Produksi:</strong><br>
                                                                {{ $dep->tgl_update_produksi
                                                                    ? \Carbon\Carbon::parse($dep->tgl_update_produksi)->format('d-m-Y H:i')
                                                                    : '-' }}
                                                            </div>

                                                            <div class="col-md-4">
                                                                <strong>Verifikasi SPV:</strong><br>
                                                                {{ $dep->tgl_update_spv
                                                                    ? \Carbon\Carbon::parse($dep->tgl_update_spv)->format('d-m-Y H:i')
                                                                    : '-' }}
                                                            </div>
                                                        </div>

                                                        <div class="mt-3">
                                                            <span class="badge bg-dark">
                                                                Snapshot Audit
                                                            </span>

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
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>

                                {{-- QC --}}
                                <td class="text-center align-middle">
                                    {{ $dep->username ?? '-' }}
                                </td>

                                {{-- PRODUKSI --}}
                                <td class="text-center align-middle">
                                    {{ $dep->nama_produksi ?? '-' }}
                                </td>

                                {{-- STATUS SPV --}}
                                <td class="text-center align-middle">
                                    @if ($dep->status_spv == 0)
                                        <span class="fw-bold text-secondary">Created</span>
                                    @elseif ($dep->status_spv == 1)
                                        <span class="fw-bold text-success">Verified</span>
                                    @elseif ($dep->status_spv == 2)
                                        <a href="javascript:void(0)"
                                            data-bs-toggle="modal"
                                            data-bs-target="#{{ $revisionId }}"
                                            class="fw-bold text-danger text-decoration-none">
                                            Revision
                                        </a>

                                        {{-- MODAL REVISI --}}
                                        <div class="modal fade"
                                            id="{{ $revisionId }}"
                                            tabindex="-1"
                                            aria-labelledby="{{ $revisionId }}Label"
                                            aria-hidden="true">

                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">

                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title"
                                                            id="{{ $revisionId }}Label">
                                                            Detail Revisi
                                                        </h5>

                                                        <button type="button"
                                                            class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal"
                                                            aria-label="Close">
                                                        </button>
                                                    </div>

                                                    <div class="modal-body text-start">
                                                        <p>
                                                            <strong>Status:</strong>
                                                            Revision
                                                        </p>

                                                        <p class="mb-0">
                                                            <strong>Catatan:</strong>
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
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- ACTION --}}
                                <td class="text-center align-middle">
                                    <a href="{{ route('xray.edit', $dep->uuid) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                        Update
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="bi bi-inbox fs-4"></i>
                                    <br>
                                    Belum ada data Audit X-Ray.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- INFORMASI DATA --}}
            @if ($data->total() > 0)
                <div class="text-muted small mb-2">
                    Menampilkan {{ $data->firstItem() }}–{{ $data->lastItem() }}
                    dari {{ $data->total() }} data
                </div>
            @endif

            {{-- PAGINATION --}}
            <div class="mt-3">
                {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

            {{-- EXPORT PDF --}}
            @if (in_array($type_user, [0, 2, 8]))
                <form method="GET" action="{{ route('xray.exportPdfAudit') }}">
                    <div class="d-flex flex-wrap align-items-end gap-2 mt-4 pt-3 border-top">
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
                            <i class="bi bi-file-earmark-pdf"></i>
                            Export PDF
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
