
@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm">
        <div class="card-body">

            {{-- JUDUL --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0 fw-bold">
                    <i class="bi bi-list-check"></i>
                    Data Pemeriksaan Retain
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('retain.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data Pemeriksaan Retain
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
                        Anda sedang melihat data Pemeriksaan Retain yang tersimpan
                        pada halaman Audit. Data di halaman ini merupakan salinan
                        data untuk kebutuhan pemeriksaan dan audit, sehingga
                        perubahan pada data Audit tidak memengaruhi data
                        operasional asli.
                    </div>
                </div>
            </div>

            {{-- FILTER --}}
            <form method="GET"
                  action="{{ route('retain.audit') }}"
                  class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                <div class="input-group" style="max-width:220px;">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-calendar-date text-muted"></i>
                    </span>
                    <input type="date"
                           name="date"
                           class="form-control"
                           value="{{ request('date') }}">
                </div>

                <div class="input-group" style="max-width:350px;">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text"
                           name="search"
                           class="form-control"
                           value="{{ request('search') }}"
                           placeholder="Cari data retain...">
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="{{ route('retain.audit') }}"
                   class="btn btn-secondary btn-sm">
                    Reset
                </a>
            </form>

            {{-- TABEL AUDIT --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Jumlah Sample</th>
                            <th>Kondisi Sample</th>
                            <th>Keterangan</th>
                            <th>QC</th>
                            <th>SPV</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $no = ($data->currentPage() - 1) * $data->perPage() + 1;
                        @endphp

                        @forelse ($data as $dep)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>

                                <td class="text-center">
                                    {{ $dep->date ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y') : '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $dep->nama_produk ?? '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $dep->kode_produksi ?? '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $dep->jumlah_sample ?? '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $dep->kondisi_sample ?? '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $dep->keterangan ?? '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $dep->username ?? '-' }}
                                </td>

                                <td class="text-center">
                                    @if (($dep->status_spv ?? null) == 0)
                                        <span class="fw-bold text-secondary">Created</span>
                                    @elseif (($dep->status_spv ?? null) == 1)
                                        <span class="fw-bold text-success">Verified</span>
                                    @elseif (($dep->status_spv ?? null) == 2)
                                        <span class="fw-bold text-danger">Revision</span>
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- ACTION --}}
                                <td class="text-center">
                                    <a href="{{ route('retain.edit', $dep->uuid) }}"
                                       class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i> Update
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">
                                    Belum ada data Audit Retain.
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

            {{-- EXPORT --}}
            <div class="mt-3">
                <form method="GET" action="{{ route('retain.exportPdfAudit') }}">

                    <div class="d-flex align-items-end gap-2">
                        <div class="col-auto">
                            <label for="date" class="col-form-label fw-semibold">Pilih Tanggal</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" id="date" name="date" class="form-control form-control-sm"
                                value="{{ request('date') }}" required>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bi bi-file-earmark-pdf"></i> Export PDF
                            </button>
                        </div>
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
