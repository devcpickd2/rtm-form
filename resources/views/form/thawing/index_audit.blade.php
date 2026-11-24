
@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm">
        <div class="card-body">

            {{-- JUDUL --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-list-check"></i>
                    Data Pemeriksaan Proses Thawing
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('thawing.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data Pemeriksaan Proses Thawing
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
                        Data Pemeriksaan Proses Thawing merupakan salinan data
                        saat pertama kali disimpan untuk kebutuhan pemeriksaan
                        dan audit. Perubahan pada data operasional tidak akan
                        mengubah snapshot Audit.
                    </div>
                </div>
            </div>

            {{-- FILTER --}}
            <form id="filterForm"
                method="GET"
                action="{{ route('thawing.audit') }}"
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
                        placeholder="Cari jenis produk / kode produksi / QC...">
                </div>

                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-search me-1"></i> Cari
                </button>

                @if(request('date') || request('search'))
                    <a href="{{ route('thawing.audit') }}"
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
                            <th rowspan="2">NO.</th>
                            <th rowspan="2">Date</th>
                            <th rowspan="2">Kondisi Ruangan</th>
                            <th rowspan="2">Jenis Produk</th>
                            <th rowspan="2">Kode Produksi</th>
                            <th colspan="5">Sebelum Proses Thawing</th>
                            <th colspan="5">Setelah Proses Thawing</th>
                            <th rowspan="2">Produksi</th>
                            <th rowspan="2">SPV</th>
                            <th rowspan="2">Action</th>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <th>Kondisi Produk</th>
                            <th>Keterangan Kondisi</th>
                            <th>Suhu Ruangan °C</th>
                            <th>Mulai Thawing</th>
                            <th>Selesai Thawing</th>
                            <th>Kondisi Produk</th>
                            <th>Keterangan Kondisi</th>
                            <th>Jumlah</th>
                            <th>Suhu Produk (5-10°C)</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $index => $dep)
                            <tr>
                                <td class="text-center align-middle">
                                    {{ $data->firstItem() + $index }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->date
                                        ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                        : '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->kondisi_ruangan ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->jenis_produk ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->kode_produksi ?? '-' }}
                                </td>

                                {{-- SEBELUM THAWING --}}
                                <td class="text-center align-middle">
                                    {{ $dep->jumlah ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->kondisi_produk ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    Keterangan:
                                    {{ $dep->keterangan_kondisi ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->suhu_ruangan ?: '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->mulai_thawing
                                        ? \Carbon\Carbon::parse($dep->mulai_thawing)->format('H:i')
                                        : '-' }}
                                </td>

                                {{-- SETELAH THAWING --}}
                                <td class="text-center align-middle">
                                    {{ $dep->selesai_thawing
                                        ? \Carbon\Carbon::parse($dep->selesai_thawing)->format('H:i')
                                        : '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->kondisi_produk_setelah ?: '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    Keterangan:
                                    {{ $dep->keterangan_kondisi_setelah ?: '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    Jumlah:
                                    {{ $dep->jumlah_setelah ?: '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->suhu_produk ?? '-' }}
                                </td>

                                {{-- STATUS PRODUKSI --}}
                                <td class="text-center align-middle">
                                    @if ($dep->status_produksi == 0)
                                        <span class="fw-bold text-secondary">
                                            Created
                                        </span>
                                    @elseif ($dep->status_produksi == 1)
                                        <a href="javascript:void(0);"
                                            data-bs-toggle="modal"
                                            data-bs-target="#checkedModal{{ $dep->uuid }}"
                                            class="fw-bold text-success text-decoration-none">
                                            Checked
                                        </a>

                                        <div class="modal fade"
                                            id="checkedModal{{ $dep->uuid }}"
                                            tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title">
                                                            Detail Checked
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
                                                                Checked
                                                            </li>
                                                            <li>
                                                                <strong>Nama Produksi:</strong>
                                                                {{ $dep->nama_produksi ?? '-' }}
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
                                    @elseif ($dep->status_produksi == 2)
                                        <span class="fw-bold text-danger">
                                            Recheck
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
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
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- ACTION --}}
                                <td class="text-center align-middle">
                                    <a href="{{ route('thawing.edit', $dep->uuid) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                        Update
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="18"
                                    class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-4"></i>
                                    <br>
                                    Belum ada data Audit Thawing.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if ($data->total() > 0)
                <div class="text-muted small mb-2">
                    Menampilkan {{ $data->firstItem() }}–{{ $data->lastItem() }}
                    dari {{ $data->total() }} data
                </div>
            @endif

            <div class="mt-3">
                {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

            {{-- EXPORT PDF: FITUR SESUAI HALAMAN UTAMA --}}
            <div class="mt-4 pt-3 border-top">
                <form method="GET" action="{{ route('thawing.exportPdf') }}">
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
                            <i class="bi bi-file-earmark-pdf"></i>
                            Export PDF
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
