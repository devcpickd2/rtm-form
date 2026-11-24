
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
                    Data Verifikasi Mesin
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('mesin.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data Verifikasi Mesin
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
                        Data Verifikasi Mesin merupakan snapshot atau salinan
                        kondisi data saat pertama kali disimpan. Data ini
                        digunakan untuk pemeriksaan dan audit serta terpisah
                        dari data operasional asli.
                    </div>
                </div>
            </div>

            {{-- FILTER --}}
            <form id="filterForm"
                method="GET"
                action="{{ route('mesin.audit') }}"
                class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                <div class="input-group" style="max-width:220px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-calendar-date text-muted"></i>
                    </span>
                    <input type="date"
                        name="date"
                        id="filter_date"
                        class="form-control border-start-0"
                        value="{{ request('date') }}">
                </div>

                <div class="input-group flex-grow-1" style="max-width:350px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text"
                        name="search"
                        id="search"
                        class="form-control border-start-0"
                        value="{{ request('search') }}"
                        placeholder="Cari username, mesin, tindakan...">
                </div>

                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-search me-1"></i>
                    Cari
                </button>

                @if(request('date') || request('search'))
                    <a href="{{ route('mesin.audit') }}"
                        class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset
                    </a>
                @endif
            </form>

            {{-- TABEL --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date | Shift</th>
                            <th>Verifikasi</th>
                            <th>Tindakan Perbaikan</th>
                            <th>Keterangan</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody">
                        @forelse ($data as $index => $dep)
                            @php
                                $verif_mesin = $dep->verif_mesin;

                                if (is_string($verif_mesin)) {
                                    $decoded = json_decode($verif_mesin, true);
                                    $verif_mesin = is_array($decoded) ? $decoded : [];
                                } elseif (!is_array($verif_mesin)) {
                                    $verif_mesin = [];
                                }
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

                                {{-- DETAIL VERIFIKASI MESIN --}}
                                <td class="text-center align-middle">
                                    @if (!empty($verif_mesin))
                                        <a href="javascript:void(0);"
                                            data-bs-toggle="modal"
                                            data-bs-target="#verifMesinModal{{ $dep->uuid }}"
                                            class="fw-bold text-decoration-underline">
                                            Verifikasi
                                        </a>

                                        <div class="modal fade"
                                            id="verifMesinModal{{ $dep->uuid }}"
                                            tabindex="-1"
                                            aria-labelledby="verifMesinModalLabel{{ $dep->uuid }}"
                                            aria-hidden="true">

                                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                                <div class="modal-content">

                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title text-start"
                                                            id="verifMesinModalLabel{{ $dep->uuid }}">
                                                            Detail Verifikasi Mesin
                                                        </h5>

                                                        <button type="button"
                                                            class="btn-close"
                                                            data-bs-dismiss="modal"
                                                            aria-label="Close">
                                                        </button>
                                                    </div>

                                                    <div class="modal-body p-0">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-sm mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th style="width:50px;">No</th>
                                                                        <th>Nama Mesin</th>
                                                                        <th>Standar Setting</th>
                                                                        <th>Aktual</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody>
                                                                    @foreach ($verif_mesin as $itemIndex => $item)
                                                                        <tr>
                                                                            <td>
                                                                                {{ $itemIndex + 1 }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $item['nama_mesin'] ?? '-' }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $item['standar_setting'] ?? '-' }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $item['aktual'] ?? '-' }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
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

                                {{-- TINDAKAN PERBAIKAN --}}
                                <td class="text-center align-middle">
                                    {{ $dep->tindakan_perbaikan ?? '-' }}
                                </td>

                                {{-- KETERANGAN --}}
                                <td class="text-center align-middle">
                                    {{ $dep->keterangan ?? '-' }}
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
                                        <span class="fw-bold text-secondary">
                                            Created
                                        </span>
                                    @elseif ($dep->status_spv == 1)
                                        <span class="fw-bold text-success">
                                            Verified
                                        </span>
                                    @elseif ($dep->status_spv == 2)
                                        <a href="javascript:void(0);"
                                            data-bs-toggle="modal"
                                            data-bs-target="#revisionModal{{ $dep->uuid }}"
                                            class="text-danger fw-bold text-decoration-none">
                                            Revision
                                        </a>

                                        {{-- MODAL REVISI --}}
                                        <div class="modal fade"
                                            id="revisionModal{{ $dep->uuid }}"
                                            tabindex="-1"
                                            aria-labelledby="revisionModalLabel{{ $dep->uuid }}"
                                            aria-hidden="true">

                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">

                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title"
                                                            id="revisionModalLabel{{ $dep->uuid }}">
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
                                    <a href="{{ route('mesin.edit', $dep->uuid) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                        Update
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="bi bi-inbox fs-4"></i>
                                    <br>
                                    Belum ada data Audit Verifikasi Mesin.
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

            {{-- EXPORT PDF SESUAI HALAMAN UTAMA --}}
            @if (in_array($type_user, [0, 2, 8]))
                <form method="GET" action="{{ route('mesin.exportPdf') }}">
                    <div class="d-flex align-items-end gap-2 flex-wrap mt-4 pt-3 border-top">

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

                        <div>
                            <label for="export_shift" class="form-label fw-semibold">
                                Pilih Shift
                            </label>
                            <select id="export_shift"
                                name="shift"
                                class="form-select form-select-sm"
                                required>
                                <option value="" disabled
                                    {{ request('shift') ? '' : 'selected' }}>
                                    Pilih shift
                                </option>
                                <option value="1"
                                    {{ request('shift') == '1' ? 'selected' : '' }}>
                                    Shift 1
                                </option>
                                <option value="2"
                                    {{ request('shift') == '2' ? 'selected' : '' }}>
                                    Shift 2
                                </option>
                                <option value="3"
                                    {{ request('shift') == '3' ? 'selected' : '' }}>
                                    Shift 3
                                </option>
                            </select>
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
