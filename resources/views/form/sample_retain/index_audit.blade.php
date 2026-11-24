
@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">

    <div class="card shadow-sm">
        <div class="card-body">

            {{-- JUDUL --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-list-check"></i>
                    Data Pemeriksaan Sampel Retain
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('sample_retain.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data Pemeriksaan Sampel Retain
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
                        Anda sedang melihat data Pemeriksaan Sampel Retain
                        yang tersimpan pada halaman Audit. Data di halaman ini
                        merupakan salinan data untuk kebutuhan pemeriksaan
                        dan audit, sehingga perubahan pada data Audit tidak
                        memengaruhi data operasional asli.
                    </div>
                </div>
            </div>

            {{-- FILTER --}}
            <form id="filterForm"
                  method="GET"
                  action="{{ route('sample_retain.audit') }}"
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

                <a href="{{ route('sample_retain.audit') }}"
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
                            <th>Nama Produk</th>
                            <th>Kode Batch</th>
                            <th>Analisa</th>
                            <th>SPV</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody">
                        @php
                            $no = ($data->currentPage() - 1) * $data->perPage() + 1;
                        @endphp

                        @forelse ($data as $dep)
                            @php
                                $analisa = is_string($dep->analisa)
                                    ? json_decode($dep->analisa, true)
                                    : ($dep->analisa ?? []);

                                $analisa = is_array($analisa) ? $analisa : [];
                            @endphp

                            <tr>
                                <td class="text-center">
                                    {{ $no++ }}
                                </td>

                                <td>
                                    {{ $dep->nama_produk ?? '-' }}
                                </td>

                                <td>
                                    {{ $dep->kode_produksi ?? '-' }}
                                </td>

                                {{-- DETAIL ANALISA --}}
                                <td class="text-center">
                                    @if (!empty($analisa))
                                        <a href="javascript:void(0)"
                                           data-bs-toggle="modal"
                                           data-bs-target="#analisaModal{{ $dep->uuid }}"
                                           class="fw-bold text-decoration-underline">
                                            Lihat Analisa
                                        </a>

                                        <div class="modal fade"
                                             id="analisaModal{{ $dep->uuid }}"
                                             tabindex="-1"
                                             aria-hidden="true">

                                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                <div class="modal-content">

                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title">
                                                            Detail Analisa Sampel Retain
                                                        </h5>

                                                        <button type="button"
                                                                class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal">
                                                        </button>
                                                    </div>

                                                    <div class="modal-body p-0">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-sm mb-0 text-center align-middle">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>No</th>
                                                                        <th>Bulan</th>
                                                                        <th>Fisik/Tekstur</th>
                                                                        <th>Aroma</th>
                                                                        <th>Rasa</th>
                                                                        <th>Average Score</th>
                                                                        <th>Cemaran</th>
                                                                        <th>Keterangan</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody>
                                                                    @foreach ($analisa as $index => $item)
                                                                        <tr>
                                                                            <td>
                                                                                {{ $index + 1 }}
                                                                            </td>

                                                                            <td>
                                                                                @if (!empty($item['bulan']))
                                                                                    @try
                                                                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $item['bulan'])->translatedFormat('F Y') }}
                                                                                    @catch (\Throwable $e)
                                                                                        {{ $item['bulan'] }}
                                                                                    @endtry
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>

                                                                            <td>
                                                                                {{ $item['fisik'] ?? '-' }}
                                                                            </td>

                                                                            <td>
                                                                                {{ $item['aroma'] ?? '-' }}
                                                                            </td>

                                                                            <td>
                                                                                {{ $item['rasa'] ?? '-' }}
                                                                            </td>

                                                                            <td>
                                                                                {{ $item['rata_score'] ?? '-' }}
                                                                            </td>

                                                                            <td>
                                                                                {{ $item['cemaran'] ?? '-' }}
                                                                            </td>

                                                                            <td>
                                                                                @if (($item['release'] ?? '') === 'Release')
                                                                                    <span class="fw-bold text-success">
                                                                                        Release
                                                                                    </span>
                                                                                @elseif (($item['release'] ?? '') === 'Tidak Release')
                                                                                    <span class="fw-bold text-danger">
                                                                                        Tidak Release
                                                                                    </span>
                                                                                @else
                                                                                    {{ $item['release'] ?? '-' }}
                                                                                @endif
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
                                        <span class="text-muted">Belum ada analisa</span>
                                    @endif
                                </td>

                                {{-- STATUS SPV --}}
                                <td class="text-center">
                                    @if ($dep->status_spv == 0)
                                        <span class="fw-bold text-secondary">
                                            Created
                                        </span>

                                    @elseif ($dep->status_spv == 1)
                                        <span class="fw-bold text-success">
                                            Verified
                                        </span>

                                    @elseif ($dep->status_spv == 2)
                                        <a href="javascript:void(0)"
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
                                        -
                                    @endif
                                </td>

                                {{-- ACTION AUDIT --}}
                                <td class="text-center">
                                    <a href="{{ route('sample_retain.edit', $dep->uuid) }}"
                                       class="btn btn-warning btn-sm me-1 mb-1">
                                        <i class="bi bi-pencil"></i> Update
                                    </a>

                                    <a href="{{ route('sample_retain.exportPdf', ['uuid' => $dep->uuid]) }}"
                                       target="_blank"
                                       class="btn btn-danger btn-sm mb-1">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        Export PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    Belum ada data Audit Sampel Retain.
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
