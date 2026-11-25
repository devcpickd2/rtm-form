
@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">

    <div class="card shadow-sm">
        <div class="card-body">

            {{-- JUDUL --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-list-check"></i>
                    Data Kebersihan Ruang, Mesin dan Peralatan Produksi
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('kebersihan_ruang.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data Kebersihan Ruang, Mesin dan Peralatan Produksi
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
                        Anda sedang melihat data Kebersihan Ruang, Mesin dan Peralatan
                        Produksi yang tersimpan pada halaman Audit. Data di halaman ini
                        merupakan salinan data untuk kebutuhan pemeriksaan dan audit,
                        sehingga perubahan pada data Audit tidak memengaruhi data
                        operasional asli.
                    </div>
                </div>
            </div>

            {{-- FILTER DAN SEARCH --}}
            <form id="filterForm"
                method="GET"
                action="{{ route('kebersihan_ruang.audit') }}"
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
                        placeholder="Cari QC, Produksi, SPV, catatan...">
                </div>

                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-funnel me-1"></i>
                    Filter
                </button>

                @if (request('date') || request('search'))
                    <a href="{{ route('kebersihan_ruang.audit') }}"
                        class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset
                    </a>
                @endif
            </form>

            @php
                $auditCheckIconKebersihan = function ($arr) {
                    if (is_string($arr)) {
                        $arr = json_decode($arr, true);
                    }

                    return is_array($arr) && !empty($arr['jam'])
                        ? '<i class="bi bi-check-circle-fill text-success"></i>'
                        : '<i class="bi bi-x-circle text-danger"></i>';
                };
            @endphp

            {{-- TABEL AUDIT --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date | Shift</th>
                            <th>Rice - Boiling</th>
                            <th>Noodle</th>
                            <th>Chillroom</th>
                            <th>CS 1</th>
                            <th>CS 2</th>
                            <th>Seasoning</th>
                            <th>Prep Room</th>
                            <th>Cooking</th>
                            <th>Filling</th>
                            <th>Topping</th>
                            <th>Packing</th>
                            <th>IQF</th>
                            <th>CS FG</th>
                            <th>DryStore</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $dep)
                            <tr>
                                <td class="text-center align-middle">
                                    {{ $data->firstItem() + $loop->index }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->date
                                        ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                        : '-' }}
                                    | Shift: {{ $dep->shift ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {!! $auditCheckIconKebersihan($dep->rice_boiling ?? []) !!}
                                </td>

                                <td class="text-center align-middle">
                                    {!! $auditCheckIconKebersihan($dep->noodle ?? []) !!}
                                </td>

                                <td class="text-center align-middle">
                                    {!! $auditCheckIconKebersihan($dep->cr_rm ?? []) !!}
                                </td>

                                <td class="text-center align-middle">
                                    {!! $auditCheckIconKebersihan($dep->cs_1 ?? []) !!}
                                </td>

                                <td class="text-center align-middle">
                                    {!! $auditCheckIconKebersihan($dep->cs_2 ?? []) !!}
                                </td>

                                <td class="text-center align-middle">
                                    {!! $auditCheckIconKebersihan($dep->seasoning ?? []) !!}
                                </td>

                                <td class="text-center align-middle">
                                    {!! $auditCheckIconKebersihan($dep->prep_room ?? []) !!}
                                </td>

                                <td class="text-center align-middle">
                                    {!! $auditCheckIconKebersihan($dep->cooking ?? []) !!}
                                </td>

                                <td class="text-center align-middle">
                                    {!! $auditCheckIconKebersihan($dep->filling ?? []) !!}
                                </td>

                                <td class="text-center align-middle">
                                    {!! $auditCheckIconKebersihan($dep->topping ?? []) !!}
                                </td>

                                <td class="text-center align-middle">
                                    {!! $auditCheckIconKebersihan($dep->packing ?? []) !!}
                                </td>

                                <td class="text-center align-middle">
                                    {!! $auditCheckIconKebersihan($dep->iqf ?? []) !!}
                                </td>

                                <td class="text-center align-middle">
                                    {!! $auditCheckIconKebersihan($dep->cs_fg ?? []) !!}
                                </td>

                                <td class="text-center align-middle">
                                    {!! $auditCheckIconKebersihan($dep->ds ?? []) !!}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->username ?? '-' }}
                                </td>

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
                                        <span class="text-secondary">-</span>
                                    @endif
                                </td>

                                {{-- ACTION --}}
                                <td class="text-center align-middle">
                                    <a href="{{ route('kebersihan_ruang.edit', $dep->uuid) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                        Update
                                    </a>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="20" class="text-center align-middle py-4">
                                    <i class="bi bi-inbox text-muted"
                                        style="font-size:1.5rem;"></i>
                                    <br>

                                    @if (request('date') || request('search'))
                                        Tidak ada data yang cocok dengan pencarian/filter.
                                    @else
                                        Belum ada data Audit Kebersihan Ruang.
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

            {{-- EXPORT PDF --}}
            <div class="mt-4 pt-3 border-top">
                <form method="GET"
                    action="{{ route('kebersihan_ruang.exportPdfAudit') }}"
                    class="d-flex flex-wrap align-items-end gap-2">

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

                            <option value="">-- Pilih Shift --</option>

                            <option value="1" {{ request('shift') == '1' ? 'selected' : '' }}>
                                Shift 1
                            </option>

                            <option value="2" {{ request('shift') == '2' ? 'selected' : '' }}>
                                Shift 2
                            </option>

                            <option value="3" {{ request('shift') == '3' ? 'selected' : '' }}>
                                Shift 3
                            </option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-file-earmark-pdf"></i>
                        Export PDF
                    </button>
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
</style>
@endsection
