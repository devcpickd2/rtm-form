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
                        Data Verifikasi Pengemasan
                        <span class="badge audit-badge">MODE AUDIT</span>
                    </h3>
                </div>

                {{-- BREADCRUMB --}}
                <div class="audit-breadcrumb mb-3">
                    <a href="{{ route('pengemasan.index') }}">
                        <i class="bi bi-arrow-left me-1"></i>
                        Data Verifikasi Pengemasan
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
                            Data Verifikasi Pengemasan merupakan snapshot atau salinan
                            kondisi data saat pertama kali disimpan. Data ini digunakan
                            untuk pemeriksaan dan audit serta terpisah dari data
                            operasional asli.
                        </div>
                    </div>
                </div>

                {{-- FILTER --}}
                <form method="GET" action="{{ route('pengemasan.audit') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                    <div class="input-group" style="max-width:220px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-calendar-date text-muted"></i>
                        </span>
                        <input type="date" name="date" class="form-control border-start-0"
                            value="{{ request('date') }}">
                    </div>

                    <div class="input-group flex-grow-1" style="max-width:350px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0"
                            placeholder="Produk, kode produksi, username..." value="{{ request('search') }}">
                    </div>

                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search me-1"></i>
                        Cari
                    </button>

                    @if (request('date') || request('search'))
                        <a href="{{ route('pengemasan.audit') }}" class="btn btn-outline-secondary">
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
                                <th>Pukul</th>
                                <th>Nama Produk</th>
                                <th>Kode Produksi</th>
                                <th>Checking</th>
                                <th>Packing</th>
                                <th>QC</th>
                                <th>Produksi</th>
                                <th>SPV</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($data as $index => $dep)
                                @php
                                    $decodeJson = function ($value) {
                                        if (is_array($value)) {
                                            return $value;
                                        }

                                        if (is_string($value)) {
                                            return json_decode($value, true) ?? [];
                                        }

                                        return [];
                                    };

                                    $trayChecking = $decodeJson($dep->tray_checking);
                                    $boxChecking = $decodeJson($dep->box_checking);
                                    $trayPacking = $decodeJson($dep->tray_packing);
                                    $boxPacking = $decodeJson($dep->box_packing);

                                    $checkingModalId = 'checkingModal' . $dep->uuid;
                                    $packingModalId = 'packingModal' . $dep->uuid;
                                    $revisionModalId = 'revisionModal' . $dep->uuid;
                                @endphp

                                <tr>
                                    <td class="text-center align-middle">
                                        {{ $data->firstItem() + $index }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->date ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y') : '-' }}
                                        | Shift: {{ $dep->shift ?? '-' }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->pukul ? \Carbon\Carbon::parse($dep->pukul)->format('H:i') : '-' }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->nama_produk ?? '-' }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->kode_produksi ?? '-' }}
                                    </td>

                                    {{-- CHECKING --}}
                                    <td class="text-center align-middle">
                                        @if (!empty($trayChecking) || !empty($boxChecking))
                                            <a href="javascript:void(0)" data-bs-toggle="modal"
                                                data-bs-target="#{{ $checkingModalId }}"
                                                class="fw-bold text-decoration-underline">
                                                Lihat Checking
                                            </a>
                                        @else
                                            <span>-</span>
                                        @endif

                                        {{-- MODAL CHECKING --}}
                                        <div class="modal fade" id="{{ $checkingModalId }}" tabindex="-1"
                                            aria-labelledby="{{ $checkingModalId }}Label" aria-hidden="true">

                                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                <div class="modal-content">

                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title" id="{{ $checkingModalId }}Label">
                                                            Detail Pengemasan - Checking
                                                        </h5>

                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close">
                                                        </button>
                                                    </div>

                                                    <div class="modal-body text-start">
                                                        <h6 class="fw-bold">Tray / Pack</h6>

                                                        <div class="table-responsive mb-4">
                                                            <table
                                                                class="table table-bordered table-sm text-center align-middle">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Nama Produk</th>
                                                                        <th>Prod. Code | Best Before</th>
                                                                        <th>QR Code</th>
                                                                        <th>Kondisi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            {{ $trayChecking['nama_produk'] ?? '-' }}
                                                                        </td>
                                                                        <td>
                                                                            @if (!empty($trayChecking['kode_produksi']))
                                                                                <a href="{{ asset('storage/' . $trayChecking['kode_produksi']) }}"
                                                                                    target="_blank">
                                                                                    Lihat Gambar
                                                                                </a>
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            {{ $trayChecking['qrcode'] ?? '-' }}
                                                                        </td>
                                                                        <td>
                                                                            {{ $trayChecking['kondisi'] ?? '-' }}
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        <h6 class="fw-bold">Box</h6>

                                                        <div class="table-responsive">
                                                            <table
                                                                class="table table-bordered table-sm text-center align-middle">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Nama Produk | Prod. Code | Best Before</th>
                                                                        <th>Kondisi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            @if (!empty($boxChecking['kode_produksi']))
                                                                                <a href="{{ asset('storage/' . $boxChecking['kode_produksi']) }}"
                                                                                    target="_blank">
                                                                                    Lihat Gambar
                                                                                </a>
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            {{ $boxChecking['kondisi'] ?? '-' }}
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        <div class="mt-3">
                                                            <strong>Keterangan Checking:</strong>
                                                            <p class="mb-0">
                                                                {{ $dep->keterangan_checking ?? '-' }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm"
                                                            data-bs-dismiss="modal">
                                                            Tutup
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- PACKING --}}
                                    <td class="text-center align-middle">
                                        @if (!empty($trayPacking) || !empty($boxPacking))
                                            <a href="javascript:void(0)" data-bs-toggle="modal"
                                                data-bs-target="#{{ $packingModalId }}"
                                                class="fw-bold text-decoration-underline">
                                                Lihat Packing
                                            </a>
                                        @else
                                            <span>-</span>
                                        @endif

                                        {{-- MODAL PACKING --}}
                                        <div class="modal fade" id="{{ $packingModalId }}" tabindex="-1"
                                            aria-labelledby="{{ $packingModalId }}Label" aria-hidden="true">

                                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                <div class="modal-content">

                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title" id="{{ $packingModalId }}Label">
                                                            Detail Pengemasan - Packing
                                                        </h5>

                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close">
                                                        </button>
                                                    </div>

                                                    <div class="modal-body text-start">
                                                        <h6 class="fw-bold">Tray / Pack</h6>

                                                        <div class="table-responsive mb-4">
                                                            <table
                                                                class="table table-bordered table-sm text-center align-middle">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Nama Produk</th>
                                                                        <th>Prod. Code | Best Before</th>
                                                                        <th>QR Code</th>
                                                                        <th>Kondisi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            {{ $trayPacking['nama_produk'] ?? '-' }}
                                                                        </td>
                                                                        <td>
                                                                            @if (!empty($trayPacking['kode_produksi']))
                                                                                <a href="{{ asset('storage/' . $trayPacking['kode_produksi']) }}"
                                                                                    target="_blank">
                                                                                    Lihat Gambar
                                                                                </a>
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            {{ $trayPacking['qrcode'] ?? '-' }}
                                                                        </td>
                                                                        <td>
                                                                            {{ $trayPacking['kondisi'] ?? '-' }}
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        <h6 class="fw-bold">Box</h6>

                                                        <div class="table-responsive">
                                                            <table
                                                                class="table table-bordered table-sm text-center align-middle">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Nama Produk | Prod. Code | Best Before</th>
                                                                        <th>Isi Box</th>
                                                                        <th>Kondisi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            @if (!empty($boxPacking['kode_produksi']))
                                                                                <a href="{{ asset('storage/' . $boxPacking['kode_produksi']) }}"
                                                                                    target="_blank">
                                                                                    Lihat Gambar
                                                                                </a>
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            {{ $boxPacking['isi_box'] ?? '-' }} pcs
                                                                        </td>
                                                                        <td>
                                                                            {{ $boxPacking['kondisi'] ?? '-' }}
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        <div class="mt-3">
                                                            <strong>Keterangan Packing:</strong>
                                                            <p class="mb-0">
                                                                {{ $dep->keterangan_packing ?? '-' }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm"
                                                            data-bs-dismiss="modal">
                                                            Tutup
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
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
                                            <a href="javascript:void(0)" data-bs-toggle="modal"
                                                data-bs-target="#{{ $revisionModalId }}"
                                                class="fw-bold text-danger text-decoration-none">
                                                Revision
                                            </a>

                                            {{-- MODAL REVISI --}}
                                            <div class="modal fade" id="{{ $revisionModalId }}" tabindex="-1"
                                                aria-labelledby="{{ $revisionModalId }}Label" aria-hidden="true">

                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">

                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title" id="{{ $revisionModalId }}Label">
                                                                Detail Revisi
                                                            </h5>

                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close">
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
                                                            <button type="button" class="btn btn-secondary btn-sm"
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
                                        <a href="{{ route('pengemasan.edit', $dep->uuid) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i>
                                            Update
                                        </a>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        <i class="bi bi-inbox fs-4"></i>
                                        <br>
                                        Belum ada data Audit Pengemasan.
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

                <form method="GET" id="exportForm">

                    <div class="d-flex align-items-end gap-2 flex-wrap">

                        <div class="col-auto">
                            <label for="tanggal_export" class="col-form-label fw-semibold">
                                Pilih Tanggal
                            </label>
                        </div>

                        <div class="col-auto">
                            <input type="date" id="tanggal_export" class="form-control form-control-sm" required>
                        </div>

                        {{-- EXPORT CHECKING --}}
                        <div class="col-auto">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="setCheckingAction()">
                                <i class="bi bi-file-earmark-pdf"></i>
                                Export Checking
                            </button>
                        </div>

                        {{-- EXPORT PACKING --}}
                        <div class="col-auto">
                            <button type="submit" class="btn btn-success btn-sm" onclick="setPackingAction()">
                                <i class="bi bi-file-earmark-pdf"></i>
                                Export Packing
                            </button>
                        </div>

                    </div>

                </form>
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
