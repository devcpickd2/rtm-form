
@extends('layouts.app')

@section('content')
    <div class="container-fluid py-0">
        <div class="card shadow-sm">
            <div class="card-body">

                {{-- JUDUL --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="mb-0">
                        <i class="bi bi-list-check"></i>
                        Data Estafet Alat QC
                        <span class="badge audit-badge">MODE AUDIT</span>
                    </h3>
                </div>

                {{-- BREADCRUMB --}}
                <div class="audit-breadcrumb mb-3">
                    <a href="{{ route('inventaris.index') }}">
                        <i class="bi bi-arrow-left me-1"></i>
                        Data Estafet Alat QC
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
                            Data Inventaris Alat QC yang ditampilkan merupakan salinan
                            data untuk kebutuhan pemeriksaan dan audit. Data pada
                            halaman ini tidak memengaruhi data operasional asli.
                        </div>
                    </div>
                </div>

                {{-- FILTER --}}
                <form method="GET"
                    action="{{ route('inventaris.audit') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                    <div class="input-group" style="max-width:220px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-calendar-date text-muted"></i>
                        </span>

                        <input type="date"
                            name="date"
                            class="form-control border-start-0"
                            value="{{ request('date') }}">
                    </div>

                    <div class="input-group flex-grow-1" style="max-width:350px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>

                        <input type="text"
                            name="search"
                            class="form-control border-start-0"
                            placeholder="Cari Cooking, Packing, Warehouse, QC..."
                            value="{{ request('search') }}">
                    </div>

                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i>
                        Filter
                    </button>

                    @if (request('date') || request('search'))
                        <a href="{{ route('inventaris.audit') }}"
                            class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset
                        </a>
                    @endif
                </form>

                <div class="mb-2 text-muted small">
                    Total data Audit: {{ $data->total() }}
                </div>

                {{-- TABEL --}}
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>NO.</th>
                                <th>Date | Shift</th>
                                <th>Area</th>
                                <th>Detail</th>
                                <th>QC Awal</th>
                                <th>QC Update</th>
                                <th>Keterangan</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($data as $dep)
                                @php
                                    $areaNama = match ((int) $dep->area) {
                                        1 => 'Cooking',
                                        2 => 'Packing',
                                        3 => 'Warehouse',
                                        default => '-',
                                    };

                                    $details = is_array($dep->detail)
                                        ? $dep->detail
                                        : (json_decode($dep->detail ?? '[]', true) ?? []);
                                @endphp

                                <tr>
                                    {{-- NOMOR --}}
                                    <td class="text-center align-middle">
                                        {{ $data->firstItem() + $loop->index }}
                                    </td>

                                    {{-- TANGGAL DAN SHIFT --}}
                                    <td class="text-center align-middle">
                                        {{ $dep->date
                                            ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                            : '-' }}
                                        | Shift: {{ $dep->shift ?? '-' }}
                                    </td>

                                    {{-- AREA --}}
                                    <td class="text-center align-middle">
                                        {{ $areaNama }}
                                    </td>

                                    {{-- DETAIL ALAT --}}
                                    <td class="text-center align-middle">
                                        @if (!empty($details))
                                            <button type="button"
                                                class="btn btn-sm btn-info"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalDetail{{ $dep->uuid }}">
                                                <i class="bi bi-eye"></i>
                                                Lihat Detail
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- QC AWAL --}}
                                    <td class="text-center align-middle">
                                        {{ $dep->username ?? '-' }}
                                    </td>

                                    {{-- QC UPDATE --}}
                                    <td class="text-center align-middle">
                                        {{ $dep->username_updated ?? '-' }}
                                    </td>

                                    {{-- KETERANGAN --}}
                                    <td class="text-center align-middle">
                                        {{ $dep->keterangan ?? '-' }}
                                    </td>

                                    {{-- ACTION --}}
                                    <td class="text-center align-middle">
                                        <a href="{{ route('inventaris.edit', $dep->uuid) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i>
                                            Update
                                        </a>
                                    </td>
                                </tr>

                                {{-- MODAL DETAIL ALAT --}}
                                @if (!empty($details))
                                    <div class="modal fade"
                                        id="modalDetail{{ $dep->uuid }}"
                                        tabindex="-1"
                                        aria-labelledby="modalDetailLabel{{ $dep->uuid }}"
                                        aria-hidden="true">

                                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content">

                                                <div class="modal-header bg-info text-white">
                                                    <h6 class="modal-title"
                                                        id="modalDetailLabel{{ $dep->uuid }}">
                                                        <i class="bi bi-tools"></i>
                                                        Detail Pemeriksaan Alat
                                                    </h6>

                                                    <button type="button"
                                                        class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"
                                                        aria-label="Close">
                                                    </button>
                                                </div>

                                                <div class="modal-body p-0">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-sm mb-0 text-center align-middle">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>No.</th>
                                                                    <th>Nama Alat</th>
                                                                    <th>Jumlah</th>
                                                                    <th>Kondisi Awal</th>
                                                                    <th>Kondisi Akhir</th>
                                                                    <th>Keterangan</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                @forelse ($details as $i => $alat)
                                                                    <tr>
                                                                        <td>{{ $i + 1 }}</td>

                                                                        <td class="text-start">
                                                                            {{ $alat['nama_alat'] ?? '-' }}
                                                                        </td>

                                                                        <td>
                                                                            {{ $alat['jumlah'] ?? '-' }}
                                                                        </td>

                                                                        <td>
                                                                            {{ $alat['kondisi_awal'] ?? '-' }}
                                                                        </td>

                                                                        <td>
                                                                            {{ $alat['kondisi_akhir'] ?? '-' }}
                                                                        </td>

                                                                        <td class="text-start">
                                                                            {{ $alat['keterangan'] ?? '-' }}
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="6"
                                                                            class="text-center text-muted">
                                                                            Tidak ada data.
                                                                        </td>
                                                                    </tr>
                                                                @endforelse
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
                                @endif

                            @empty
                                <tr>
                                    <td colspan="8" class="text-center align-middle py-4">
                                        <i class="bi bi-inbox text-muted"
                                            style="font-size:1.5rem;"></i>
                                        <br>
                                        Belum ada data Audit Inventaris.
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
