
@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">

    <div class="card shadow-sm">
        <div class="card-body">

            {{-- JUDUL --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-list-check"></i>
                    Data GMP Karyawan
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('gmp.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data GMP Karyawan
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
                        Anda sedang melihat data GMP Karyawan yang tersimpan
                        pada halaman Audit. Data di halaman ini merupakan salinan
                        data untuk kebutuhan pemeriksaan dan audit, sehingga
                        perubahan pada data Audit tidak memengaruhi data operasional asli.
                    </div>
                </div>
            </div>

            {{-- FILTER TANGGAL --}}
            <form id="filterForm"
                method="GET"
                action="{{ route('gmp.audit') }}"
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
                        placeholder="Cari QC, Produksi, atau SPV..."
                        value="{{ request('search') }}">
                </div>

                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-funnel me-1"></i>
                    Filter
                </button>

                @if (request('date') || request('search'))
                    <a href="{{ route('gmp.audit') }}"
                        class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset
                    </a>
                @endif
            </form>

            {{-- TABEL AUDIT --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date</th>
                            <th>Noodle & Rice</th>
                            <th>Cooking</th>
                            <th>Packing</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody">
                        @php
                            $no = ($data->currentPage() - 1) * $data->perPage() + 1;

                            $hitungPresentaseAudit = function ($json) {
                                if (!$json) {
                                    return 0;
                                }

                                $rows = is_array($json)
                                    ? $json
                                    : json_decode($json, true);

                                if (!is_array($rows)) {
                                    return 0;
                                }

                                $total = 0;
                                $count = 0;

                                foreach ($rows as $row) {
                                    if (!is_array($row)) {
                                        continue;
                                    }

                                    foreach ($row as $key => $val) {
                                        if ($key !== 'nama_karyawan') {
                                            $total++;

                                            if ($val == 1) {
                                                $count++;
                                            }
                                        }
                                    }
                                }

                                return $total > 0
                                    ? round(($count / $total) * 100, 1)
                                    : 0;
                            };

                            $topKaryawanAudit = function ($json, $limit = 3) {
                                if (!$json) {
                                    return [];
                                }

                                $rows = is_array($json)
                                    ? $json
                                    : json_decode($json, true);

                                if (!is_array($rows)) {
                                    return [];
                                }

                                $scores = [];

                                foreach ($rows as $row) {
                                    if (!is_array($row)) {
                                        continue;
                                    }

                                    $nama = $row['nama_karyawan'] ?? 'Tanpa Nama';
                                    $count = 0;

                                    foreach ($row as $key => $val) {
                                        if ($key !== 'nama_karyawan' && $val == 1) {
                                            $count++;
                                        }
                                    }

                                    $scores[] = [
                                        'nama' => $nama,
                                        'nilai' => $count,
                                    ];
                                }

                                usort($scores, function ($a, $b) {
                                    return $b['nilai'] <=> $a['nilai'];
                                });

                                return array_slice($scores, 0, $limit);
                            };
                        @endphp

                        @forelse ($data as $dep)
                            @php
                                $areaGmp = [
                                    'Noodle & Rice' => is_array($dep->noodle_rice)
                                        ? $dep->noodle_rice
                                        : (json_decode($dep->noodle_rice ?? '[]', true) ?: []),

                                    'Cooking' => is_array($dep->cooking)
                                        ? $dep->cooking
                                        : (json_decode($dep->cooking ?? '[]', true) ?: []),

                                    'Packing' => is_array($dep->packing)
                                        ? $dep->packing
                                        : (json_decode($dep->packing ?? '[]', true) ?: []),
                                ];
                            @endphp

                            <tr>
                                <td class="text-center align-middle">
                                    {{ $no++ }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->date
                                        ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                        : '-' }}
                                </td>

                                {{-- NOODLE & RICE --}}
                                <td class="text-center align-middle">
                                    {{ $hitungPresentaseAudit($dep->noodle_rice) }} %
                                    <br>
                                    <small>
                                        @foreach ($topKaryawanAudit($dep->noodle_rice) as $row)
                                            • {{ $row['nama'] }} ({{ $row['nilai'] }})<br>
                                        @endforeach
                                    </small>
                                </td>

                                {{-- COOKING --}}
                                <td class="text-center align-middle">
                                    {{ $hitungPresentaseAudit($dep->cooking) }} %
                                    <br>
                                    <small>
                                        @foreach ($topKaryawanAudit($dep->cooking) as $row)
                                            • {{ $row['nama'] }} ({{ $row['nilai'] }})<br>
                                        @endforeach
                                    </small>
                                </td>

                                {{-- PACKING --}}
                                <td class="text-center align-middle">
                                    {{ $hitungPresentaseAudit($dep->packing) }} %
                                    <br>
                                    <small>
                                        @foreach ($topKaryawanAudit($dep->packing) as $row)
                                            • {{ $row['nama'] }} ({{ $row['nilai'] }})<br>
                                        @endforeach
                                    </small>
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->username ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->nama_produksi ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->nama_spv ?? '-' }}

                                    <br>

                                    @if ($dep->status_spv == 0)
                                        <span class="fw-bold text-secondary">Created</span>
                                    @elseif ($dep->status_spv == 1)
                                        <span class="fw-bold text-success">Verified</span>
                                    @elseif ($dep->status_spv == 2)
                                        <a href="javascript:void(0);"
                                            data-bs-toggle="modal"
                                            data-bs-target="#revisionModal{{ $dep->uuid }}"
                                            class="text-danger fw-bold text-decoration-none">
                                            Revision
                                        </a>
                                    @endif
                                </td>

                                {{-- ACTION --}}
                                <td class="text-center align-middle">
                                    <div class="d-flex justify-content-center gap-1 flex-wrap">

                                        <a href="{{ route('gmp.edit', $dep->uuid) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i>
                                            Update
                                        </a>

                                        <button type="button"
                                            class="btn btn-primary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#auditGmp{{ $dep->uuid }}">
                                            <i class="bi bi-eye"></i>
                                            Detail
                                        </button>

                                    </div>
                                </td>
                            </tr>

                            {{-- MODAL REVISI --}}
                            @if ($dep->status_spv == 2)
                                <div class="modal fade"
                                    id="revisionModal{{ $dep->uuid }}"
                                    tabindex="-1"
                                    aria-hidden="true">

                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">

                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">
                                                    Detail Revisi
                                                </h5>

                                                <button type="button"
                                                    class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal">
                                                </button>
                                            </div>

                                            <div class="modal-body">
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
                            @endif

                            {{-- MODAL DETAIL AUDIT --}}
                            <div class="modal fade"
                                id="auditGmp{{ $dep->uuid }}"
                                tabindex="-1"
                                aria-hidden="true">

                                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                    <div class="modal-content">

                                        <div class="modal-header bg-primary text-white">
                                            <div>
                                                <h5 class="modal-title fw-bold">
                                                    Detail Audit GMP Karyawan
                                                </h5>

                                                <small>
                                                    Tanggal:
                                                    {{ $dep->date
                                                        ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                                        : '-' }}
                                                </small>
                                            </div>

                                            <button type="button"
                                                class="btn-close btn-close-white"
                                                data-bs-dismiss="modal">
                                            </button>
                                        </div>

                                        <div class="modal-body">

                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <strong>QC:</strong>
                                                    {{ $dep->username ?? '-' }}
                                                </div>

                                                <div class="col-md-4">
                                                    <strong>Produksi:</strong>
                                                    {{ $dep->nama_produksi ?? '-' }}
                                                </div>

                                                <div class="col-md-4">
                                                    <strong>SPV:</strong>
                                                    {{ $dep->nama_spv ?? '-' }}
                                                </div>
                                            </div>

                                            @foreach ($areaGmp as $namaArea => $rows)
                                                <div class="mb-4">
                                                    <h6 class="fw-bold bg-light border p-2">
                                                        {{ $namaArea }}
                                                    </h6>

                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered align-middle">
                                                            <thead class="table-secondary text-center">
                                                                <tr>
                                                                    <th>No.</th>
                                                                    <th>Nama Karyawan</th>
                                                                    <th>Seragam</th>
                                                                    <th>Boot</th>
                                                                    <th>Masker</th>
                                                                    <th>Ciput</th>
                                                                    <th>Parfum</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                @forelse ($rows as $karyawan)
                                                                    <tr>
                                                                        <td class="text-center">
                                                                            {{ $loop->iteration }}
                                                                        </td>

                                                                        <td>
                                                                            {{ $karyawan['nama_karyawan'] ?? '-' }}
                                                                        </td>

                                                                        @foreach (['seragam', 'boot', 'masker', 'ciput', 'parfum'] as $atribut)
                                                                            <td class="text-center">
                                                                                @if (($karyawan[$atribut] ?? 0) == 1)
                                                                                    <span class="badge bg-success">
                                                                                        Sesuai
                                                                                    </span>
                                                                                @else
                                                                                    <span class="badge bg-danger">
                                                                                        Tidak Sesuai
                                                                                    </span>
                                                                                @endif
                                                                            </td>
                                                                        @endforeach
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="7" class="text-center">
                                                                            Tidak ada data karyawan.
                                                                        </td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endforeach

                                            <div class="border rounded p-3">
                                                <h6 class="fw-bold">
                                                    Informasi Verifikasi
                                                </h6>

                                                <div class="row">
                                                    <div class="col-md-4 mb-2">
                                                        <strong>Status SPV:</strong>

                                                        @if ($dep->status_spv == 1)
                                                            <span class="badge bg-success">
                                                                Verified
                                                            </span>
                                                        @elseif ($dep->status_spv == 2)
                                                            <span class="badge bg-danger">
                                                                Revision
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary">
                                                                Created
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-8 mb-2">
                                                        <strong>Catatan SPV:</strong>
                                                        {{ $dep->catatan_spv ?? '-' }}
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <button type="button"
                                                class="btn btn-secondary"
                                                data-bs-dismiss="modal">
                                                Tutup
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="bi bi-inbox text-muted"
                                        style="font-size:1.5rem;"></i>
                                    <br>

                                    @if (request('date') || request('search'))
                                        Tidak ada data yang cocok dengan pencarian/filter.
                                    @else
                                        Belum ada data Audit GMP Karyawan.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- INFO JUMLAH DATA --}}
            @if ($data->total() > 0)
                <div class="text-muted small mb-2">
                    Menampilkan {{ $data->firstItem() }}-{{ $data->lastItem() }}
                    dari {{ $data->total() }} data
                </div>
            @endif

            {{-- PAGINATION --}}
            <div class="mt-3">
                {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

            {{-- EXPORT EXCEL --}}
            <div class="mt-4 pt-3 border-top">
                <form action="{{ route('gmp.exportExcelAudit') }}"
                    method="GET"
                    class="row g-2 align-items-end">

                    <div class="col-auto">
                        <label for="export_date" class="form-label fw-semibold">
                            Pilih Bulan
                        </label>

                        <input type="month"
                            id="export_date"
                            name="date"
                            class="form-control form-control-sm"
                            value="{{ request('date') }}"
                            required>
                    </div>

                    <div class="col-auto">
                        <label for="atribut" class="form-label fw-semibold">
                            Area
                        </label>

                        <select id="atribut"
                            name="atribut"
                            class="form-control form-control-sm"
                            required>

                            <option value="">-- Pilih Area --</option>

                            <option value="noodle_rice">
                                Noodle Rice
                            </option>

                            <option value="cooking">
                                Cooking
                            </option>

                            <option value="packing">
                                Packing
                            </option>
                        </select>
                    </div>

                    <div class="col-auto">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-file-earmark-excel"></i>
                            Export Excel
                        </button>
                    </div>

                </form>
            </div>

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

    .audit-breadcrumb span:last-child {
        font-weight: 700;
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

    .modal .table td,
    .modal .table th {
        font-size: .8rem;
    }
</style>
@endsection
