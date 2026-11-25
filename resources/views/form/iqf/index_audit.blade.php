
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
                    Data Pemeriksaan Suhu Produk setelah IQF
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('iqf.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data Pemeriksaan Suhu Produk setelah IQF
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
                        Data Pemeriksaan Suhu Produk setelah IQF merupakan snapshot
                        atau salinan kondisi data saat pertama kali disimpan.
                        Data ini digunakan untuk pemeriksaan dan audit serta
                        terpisah dari data operasional asli.
                    </div>
                </div>
            </div>

            {{-- FILTER --}}
            <form id="filterForm"
                method="GET"
                action="{{ route('iqf.audit') }}"
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
                        placeholder="Cari produk, kode produksi, IQF No....">
                </div>

                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-search me-1"></i>
                    Cari
                </button>

                @if(request('date') || request('search'))
                    <a href="{{ route('iqf.audit') }}"
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
                            <th>Pukul</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Std CT (°C)</th>
                            <th>Suhu Pusat Produk (°C)</th>
                            <th>Problem</th>
                            <th>Tindakan Koreksi</th>
                            <th>Catatan</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody">
                        @forelse ($data as $index => $dep)
                            @php
                                $suhu_pusat = $dep->suhu_pusat ?? [];

                                if (is_string($suhu_pusat)) {
                                    $decoded = json_decode($suhu_pusat, true);
                                    $suhu_pusat = is_array($decoded) ? $decoded : [];
                                }

                                if (!is_array($suhu_pusat)) {
                                    $suhu_pusat = [];
                                }

                                $values = [];
                                $kets = [];

                                for ($i = 1; $i <= 10; $i++) {
                                    $val = $suhu_pusat[$i - 1]['value'] ?? null;
                                    $ket = $suhu_pusat[$i - 1]['ket'] ?? null;

                                    $values[$i] = $val;
                                    $kets[$i] = $ket;
                                }

                                $numericVals = array_filter(
                                    $values,
                                    fn($v) => is_numeric($v)
                                );

                                $avg = count($numericVals)
                                    ? array_sum($numericVals) / count($numericVals)
                                    : null;

                                $std = is_numeric($dep->std_suhu)
                                    ? $dep->std_suhu
                                    : null;
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
                                    {{ $dep->pukul
                                        ? \Carbon\Carbon::parse($dep->pukul)->format('H:i')
                                        : '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->nama_produk ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->kode_produksi ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->std_suhu ?? '-' }}
                                </td>

                                {{-- DETAIL SUHU PUSAT --}}
                                <td class="text-center align-middle">
                                    @if (!empty($suhu_pusat))
                                        <a href="javascript:void(0);"
                                            data-bs-toggle="modal"
                                            data-bs-target="#suhuPusatModal{{ $dep->uuid }}"
                                            class="fw-bold text-decoration-underline">
                                            Hasil Suhu Pusat
                                        </a>

                                        <div class="modal fade"
                                            id="suhuPusatModal{{ $dep->uuid }}"
                                            tabindex="-1"
                                            aria-labelledby="suhuPusatModalLabel{{ $dep->uuid }}"
                                            aria-hidden="true">

                                            <div class="modal-dialog modal-lg"
                                                style="max-width:90%;">
                                                <div class="modal-content">

                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title"
                                                            id="suhuPusatModalLabel{{ $dep->uuid }}">
                                                            Detail Pemeriksaan Suhu IQF
                                                        </h5>

                                                        <button type="button"
                                                            class="btn-close"
                                                            data-bs-dismiss="modal"
                                                            aria-label="Close">
                                                        </button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-sm text-center align-middle">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th colspan="11">
                                                                            Suhu Pusat (°C)
                                                                        </th>
                                                                    </tr>
                                                                    <tr>
                                                                        @for ($i = 1; $i <= 10; $i++)
                                                                            <th>{{ $i }}</th>
                                                                        @endfor
                                                                        <th>Rata-rata</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody>
                                                                    <tr>
                                                                        @for ($i = 1; $i <= 10; $i++)
                                                                            @php
                                                                                $val = $values[$i];
                                                                                $ket = $kets[$i];
                                                                            @endphp

                                                                            <td>
                                                                                @if ($val !== null && $std !== null && is_numeric($val) && $val > $std)
                                                                                    <strong class="text-danger">
                                                                                        {{ $val }}
                                                                                    </strong>
                                                                                @elseif ($val !== null)
                                                                                    <strong>{{ $val }}</strong>
                                                                                @else
                                                                                    -
                                                                                @endif

                                                                                @if (!empty($ket))
                                                                                    <br>
                                                                                    <small class="text-muted">
                                                                                        {{ $ket }}
                                                                                    </small>
                                                                                @endif
                                                                            </td>
                                                                        @endfor

                                                                        <td>
                                                                            @if ($avg !== null && $std !== null && $avg > $std)
                                                                                <strong class="text-danger">
                                                                                    {{ number_format($avg, 2) }}
                                                                                </strong>
                                                                            @elseif ($avg !== null)
                                                                                <strong>
                                                                                    {{ number_format($avg, 2) }}
                                                                                </strong>
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                    </tr>
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

                                {{-- PROBLEM --}}
                                <td class="text-center align-middle">
                                    {{ $dep->problem ?? '-' }}
                                </td>

                                {{-- TINDAKAN KOREKSI --}}
                                <td class="text-center align-middle">
                                    {{ $dep->tindakan_koreksi ?? '-' }}
                                </td>

                                {{-- CATATAN --}}
                                <td class="text-center align-middle">
                                    {{ $dep->catatan ?? '-' }}
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
                                                        <ul class="list-unstyled mb-0">
                                                            <li>
                                                                <strong>Status:</strong>
                                                                Revision
                                                            </li>
                                                            <li>
                                                                <strong>Catatan:</strong>
                                                                {{ $dep->catatan_spv ?? '-' }}
                                                            </li>
                                                        </ul>
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
                                    <a href="{{ route('iqf.edit', $dep->uuid) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                        Update
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center py-4">
                                    <i class="bi bi-inbox fs-4"></i>
                                    <br>
                                    Belum ada data Audit IQF.
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
                <form method="GET" action="{{ route('iqf.exportPdfAudit') }}">
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

    .text-danger {
        font-weight: bold;
    }
</style>
@endsection
