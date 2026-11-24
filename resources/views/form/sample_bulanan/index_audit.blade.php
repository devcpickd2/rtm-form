
@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm">
        <div class="card-body">

            {{-- JUDUL --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-list-check"></i>
                    Data Sample Bulanan RND
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('sample_bulanan.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data Sample Bulanan RND
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
                        Anda sedang melihat data Sample Bulanan RND yang tersimpan
                        pada halaman Audit. Data di halaman ini merupakan salinan
                        data untuk kebutuhan pemeriksaan dan audit, sehingga
                        perubahan pada data Audit tidak memengaruhi data
                        operasional asli.
                    </div>
                </div>
            </div>

            {{-- FILTER --}}
            <form id="filterForm"
                  method="GET"
                  action="{{ route('sample_bulanan.audit') }}"
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

                <a href="{{ route('sample_bulanan.audit') }}"
                   class="btn btn-secondary btn-sm">
                    Reset
                </a>
            </form>

            {{-- TABEL --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date</th>
                            <th>Plant</th>
                            <th>Sample Bulan</th>
                            <th>Sample Storage</th>
                            <th>List Sample</th>
                            <th>Catatan</th>
                            <th>Warehouse</th>
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
                                $namaStorage = is_array($dep->sample_storage)
                                    ? $dep->sample_storage
                                    : json_decode($dep->sample_storage ?? '[]', true);

                                $namaStorage = is_array($namaStorage)
                                    ? $namaStorage
                                    : [];

                                $sample = is_array($dep->sample)
                                    ? $dep->sample
                                    : json_decode($dep->sample ?? '[]', true);

                                $sample = is_array($sample) ? $sample : [];

                                try {
                                    $sampleBulan = $dep->sample_bulan
                                        ? \Carbon\Carbon::createFromFormat('Y-m', $dep->sample_bulan)
                                            ->translatedFormat('F Y')
                                        : '-';
                                } catch (\Throwable $e) {
                                    $sampleBulan = $dep->sample_bulan ?? '-';
                                }
                            @endphp

                            <tr>
                                <td class="text-center">{{ $no++ }}</td>

                                <td class="text-center">
                                    {{ $dep->date ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y') : '-' }}
                                </td>

                                <td>{{ $dep->plant ?? '-' }}</td>

                                <td>{{ $sampleBulan }}</td>

                                <td>
                                    {{ implode(', ', $namaStorage) ?: '-' }}
                                </td>

                                {{-- LIST SAMPLE --}}
                                <td class="text-center">
                                    @if (!empty($sample))
                                        <a href="javascript:void(0)"
                                           data-bs-toggle="modal"
                                           data-bs-target="#sampleModal{{ $dep->uuid }}"
                                           class="fw-bold text-decoration-underline">
                                            Lihat Sample
                                        </a>

                                        <div class="modal fade"
                                             id="sampleModal{{ $dep->uuid }}"
                                             tabindex="-1"
                                             aria-hidden="true">

                                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                                <div class="modal-content">

                                                    <div class="modal-header bg-warning text-white">
                                                        <h5 class="modal-title">
                                                            Detail Sample Bulanan
                                                        </h5>

                                                        <button type="button"
                                                                class="btn-close"
                                                                data-bs-dismiss="modal">
                                                        </button>
                                                    </div>

                                                    <div class="modal-body p-0">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-sm mb-0">
                                                                <thead class="table-light text-center">
                                                                    <tr>
                                                                        <th>No</th>
                                                                        <th>Nama Produk</th>
                                                                        <th>Kode Produksi</th>
                                                                        <th>Best Before</th>
                                                                        <th>Quantity (gr)</th>
                                                                        <th>Keterangan</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody>
                                                                    @foreach ($sample as $index => $item)
                                                                        <tr>
                                                                            <td class="text-center">
                                                                                {{ $index + 1 }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $item['nama_produk'] ?? '-' }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $item['kode_produksi'] ?? '-' }}
                                                                            </td>
                                                                            <td class="text-center">
                                                                                {{ $item['best_before'] ?? '-' }}
                                                                            </td>
                                                                            <td class="text-center">
                                                                                {{ $item['quantity'] ?? '-' }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $item['keterangan'] ?? '-' }}
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
                                        -
                                    @endif
                                </td>

                                <td>{{ $dep->catatan ?? '-' }}</td>

                                {{-- STATUS WAREHOUSE --}}
                                <td class="text-center align-middle">
                                    @if ($dep->status_warehouse == 0)
                                        <span class="fw-bold text-secondary">
                                            Created
                                        </span>
                                    @elseif ($dep->status_warehouse == 1)
                                        <a href="javascript:void(0)"
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
                                                        <p>
                                                            <strong>Status:</strong>
                                                            Checked
                                                        </p>
                                                        <p class="mb-0">
                                                            <strong>Nama Warehouse:</strong>
                                                            {{ $dep->nama_warehouse ?? '-' }}
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
                                    @elseif ($dep->status_warehouse == 2)
                                        <span class="fw-bold text-danger">
                                            Recheck
                                        </span>
                                    @else
                                        -
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
                                        -
                                    @endif
                                </td>

                                {{-- ACTION AUDIT --}}
                                <td class="text-center">
                                    <a href="{{ route('sample_bulanan.edit', $dep->uuid) }}"
                                       class="btn btn-warning btn-sm mb-1">
                                        <i class="bi bi-pencil"></i> Update
                                    </a>

                                    <a href="{{ route('sample_bulanan.exportPdf', ['uuid' => $dep->uuid]) }}"
                                       target="_blank"
                                       class="btn btn-danger btn-sm mb-1">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        Export PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">
                                    Belum ada data Sample Bulanan RND.
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
