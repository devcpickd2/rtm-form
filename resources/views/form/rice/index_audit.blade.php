
@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm">
        <div class="card-body">

            {{-- JUDUL --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0 fw-bold">
                    <i class="bi bi-list-check"></i>
                    Data Pemeriksaan Pemasakan dengan Rice Cooker
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('rice.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data Pemeriksaan Pemasakan dengan Rice Cooker
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
                        Anda sedang melihat data Pemeriksaan Pemasakan dengan
                        Rice Cooker yang tersimpan pada halaman Audit. Data
                        merupakan salinan untuk kebutuhan pemeriksaan dan audit,
                        sehingga perubahan pada data Audit tidak memengaruhi
                        data operasional asli.
                    </div>
                </div>
            </div>

            {{-- FILTER --}}
            <form id="filterForm"
                  method="GET"
                  action="{{ route('rice.audit') }}"
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

                <a href="{{ route('rice.audit') }}"
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
                            <th>Date | Shift</th>
                            <th>Nama Produk</th>
                            <th>Rice Cooker</th>
                            <th>QC</th>
                            <th>Produksi</th>
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
                                $cooker = is_array($dep->cooker)
                                    ? $dep->cooker
                                    : json_decode($dep->cooker ?? '[]', true);

                                $cooker = is_array($cooker) ? $cooker : [];
                            @endphp

                            <tr>
                                <td class="text-center align-middle">
                                    {{ $no++ }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->date ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y') : '-' }}
                                    | Shift: {{ $dep->shift ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->nama_produk ?? '-' }}
                                </td>

                                {{-- DETAIL RICE COOKER --}}
                                <td class="text-center align-middle">
                                    @if (!empty($cooker))
                                        <a href="javascript:void(0)"
                                           data-bs-toggle="modal"
                                           data-bs-target="#cookerModal{{ $dep->uuid }}"
                                           class="fw-bold text-decoration-underline">
                                            Hasil Pemasakan
                                        </a>

                                        <div class="modal fade"
                                             id="cookerModal{{ $dep->uuid }}"
                                             tabindex="-1"
                                             aria-hidden="true">

                                            <div class="modal-dialog"
                                                 style="max-width:500px;">

                                                <div class="modal-content">

                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title">
                                                            Detail Rice Cooker
                                                        </h5>

                                                        <button type="button"
                                                                class="btn-close"
                                                                data-bs-dismiss="modal">
                                                        </button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-sm text-start align-middle">
                                                                <tbody>
                                                                    <tr>
                                                                        <th>Kode Berat</th>
                                                                        @foreach ($cooker as $item)
                                                                            <td>{{ $item['kode_beras'] ?? '-' }}</td>
                                                                        @endforeach
                                                                    </tr>

                                                                    <tr>
                                                                        <th>Berat (Kg)</th>
                                                                        @foreach ($cooker as $item)
                                                                            <td>{{ $item['berat'] ?? '-' }}</td>
                                                                        @endforeach
                                                                    </tr>

                                                                    <tr>
                                                                        <th>Kode Produksi</th>
                                                                        @foreach ($cooker as $item)
                                                                            <td>{{ $item['kode_produksi'] ?? '-' }}</td>
                                                                        @endforeach
                                                                    </tr>

                                                                    <tr>
                                                                        <th>Waktu Cooker (Menit)</th>
                                                                        @foreach ($cooker as $item)
                                                                            <td>{{ $item['waktu_masak'] ?? '-' }}</td>
                                                                        @endforeach
                                                                    </tr>

                                                                    <tr>
                                                                        <th>Suhu Produk (°C)</th>
                                                                        @foreach ($cooker as $item)
                                                                            <td>{{ $item['suhu_produk'] ?? '-' }}</td>
                                                                        @endforeach
                                                                    </tr>

                                                                    <tr>
                                                                        <th>Suhu Produk 1 Menit (°C)</th>
                                                                        @foreach ($cooker as $item)
                                                                            <td>{{ $item['suhu_after'] ?? '-' }}</td>
                                                                        @endforeach
                                                                    </tr>

                                                                    <tr>
                                                                        <th>Suhu After Vacuum (°C)</th>
                                                                        @foreach ($cooker as $item)
                                                                            <td>{{ $item['suhu_vacuum'] ?? '-' }}</td>
                                                                        @endforeach
                                                                    </tr>

                                                                    <tr>
                                                                        <th>Jam Mulai</th>
                                                                        @foreach ($cooker as $item)
                                                                            <td>{{ $item['jam_mulai'] ?? '-' }}</td>
                                                                        @endforeach
                                                                    </tr>

                                                                    <tr>
                                                                        <th>Jam Selesai</th>
                                                                        @foreach ($cooker as $item)
                                                                            <td>{{ $item['jam_selesai'] ?? '-' }}</td>
                                                                        @endforeach
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
                                        -
                                    @endif
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
                                                            <strong>Catatan:</strong><br>
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
                                <td class="text-center align-middle">
                                    <a href="{{ route('rice.edit', $dep->uuid) }}"
                                       class="btn btn-warning btn-sm mb-1">
                                        <i class="bi bi-pencil"></i> Update
                                    </a>

                                    <form method="GET"
                                          action="{{ route('rice.exportPdf') }}"
                                          target="_blank"
                                          class="d-inline-block">

                                        <input type="hidden"
                                               name="uuid"
                                               value="{{ $dep->uuid }}">

                                        <button type="submit"
                                                class="btn btn-danger btn-sm fw-bold mb-1">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                            Export PDF
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8"
                                    class="text-center align-middle">
                                    Belum ada data Audit Pemasakan dengan Rice Cooker.
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
