
@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm">
        <div class="card-body">

            {{-- JUDUL --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h3 class="mb-0 fw-bold">
                    <i class="bi bi-list-check me-1"></i>
                    Data Memo Retur
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('memo.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data Memo Retur
                </a>
                <span class="mx-2">/</span>
                <span class="fw-bold">Data Audit</span>
            </div>

            {{-- NOTE --}}
            <div class="alert audit-info d-flex align-items-start mb-4" role="alert">
                <i class="bi bi-shield-check me-3 fs-4"></i>
                <div>
                    <strong class="d-block mb-1">Halaman Data Audit</strong>
                    <div class="small">
                        Anda sedang melihat data Memo Retur yang tersimpan pada
                        halaman Audit. Data ini merupakan salinan untuk kebutuhan
                        pemeriksaan dan audit, sehingga perubahan pada data
                        operasional asli tidak terpengaruh.
                    </div>
                </div>
            </div>

            {{-- FILTER --}}
            <form id="filterForm" method="GET"
                  action="{{ route('memo.audit') }}"
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
                           placeholder="Search...">
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i> Cari
                </button>

                @if(request()->hasAny(['date', 'search']))
                    <a href="{{ route('memo.audit') }}"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                @endif
            </form>

            {{-- TABEL --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle table-memo">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date | Shift</th>
                            <th>From</th>
                            <th>Ke</th>
                            <th>Subject</th>
                            <th>Detail</th>
                            <th>QC</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $no = ($data->currentPage() - 1) * $data->perPage() + 1;
                        @endphp

                        @forelse($data as $dep)
                            <tr>
                                <td class="text-center align-middle">
                                    {{ $no++ }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}
                                    | Shift: {{ $dep->shift }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->retur_dari }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->retur_ke }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->subject }}
                                </td>

                                <td class="text-center align-middle">
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm fw-bold"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detailRetur{{ $dep->uuid }}">
                                        <i class="bi bi-eye-fill"></i> Detail
                                    </button>

                                    <div class="modal fade"
                                         id="detailRetur{{ $dep->uuid }}"
                                         tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">

                                                <div class="modal-header bg-primary text-white border-0">
                                                    <h5 class="modal-title">
                                                        <i class="bi bi-file-earmark-text-fill me-2"></i>
                                                        Detail Retur
                                                    </h5>
                                                    <button type="button"
                                                            class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal">
                                                    </button>
                                                </div>

                                                <div class="modal-body p-4 bg-light">
                                                    <div class="bg-white rounded-4 shadow-sm p-4 border-start border-4 border-primary">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <i class="bi bi-chat-left-text fs-4 text-primary me-2"></i>
                                                            <span class="fw-bold text-secondary">
                                                                Isi Memo Retur
                                                            </span>
                                                        </div>

                                                        <pre class="mb-0"
                                                             style="white-space:pre-wrap;font-family:inherit;text-align:left;">{{ $dep->detail_retur }}</pre>
                                                    </div>
                                                </div>

                                                <div class="modal-footer border-0">
                                                    <button type="button"
                                                            class="btn btn-primary px-4"
                                                            data-bs-dismiss="modal">
                                                        Tutup
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->username ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    <a href="{{ route('memo.edit', $dep->uuid) }}"
                                       class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i> Update
                                    </a>

                                    <form method="GET"
                                          action="{{ route('memo.exportPdfAudit') }}"
                                          target="_blank"
                                          class="d-inline">
                                        <input type="hidden"
                                               name="uuid"
                                               value="{{ $dep->uuid }}">

                                        <button type="submit"
                                                class="btn btn-danger btn-sm fw-bold shadow-sm">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                            Export PDF
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    Belum ada data Memo Retur pada Audit.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="mt-3">
                {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
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
        box-shadow: 0 2px 5px rgba(220,53,69,.15);
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

    .table-memo {
        table-layout: auto;
        width: 100%;
    }

    .table-memo th,
    .table-memo td {
        font-size: .78rem;
        padding: 8px 6px;
        vertical-align: middle;
    }

    .table-memo .col-no {
        width: 40px;
    }

    .table-memo .col-date {
        min-width: 110px;
    }

    .table-memo .col-from,
    .table-memo .col-to {
        min-width: 85px;
    }

    .table-memo .col-subject {
        min-width: 130px;
        white-space: normal;
    }

    .table-memo .col-detail {
        min-width: 75px;
    }

    .table-memo .col-qc {
        min-width: 100px;
    }
</style>
@endsection
