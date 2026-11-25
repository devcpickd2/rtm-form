@extends('layouts.app')

@section('content')
    <div class="container-fluid py-0">
        <div class="card shadow-sm">
            <div class="card-body">

                {{-- JUDUL --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="mb-0">
                        <i class="bi bi-list-check"></i>
                        Data Verifikasi Magnet Trap
                        <span class="badge audit-badge">MODE AUDIT</span>
                    </h3>
                </div>

                {{-- BREADCRUMB --}}
                <div class="audit-breadcrumb mb-3">
                    <a href="{{ route('verifikasi_magnet.index') }}">
                        <i class="bi bi-arrow-left me-1"></i>
                        Data Verifikasi Magnet Trap
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
                            Anda sedang melihat data Verifikasi Magnet Trap yang tersimpan
                            pada halaman Audit. Data di halaman ini merupakan salinan
                            data untuk kebutuhan pemeriksaan dan audit, sehingga
                            perubahan pada data Audit tidak memengaruhi data operasional asli.
                        </div>
                    </div>
                </div>

                {{-- FILTER DAN SEARCH --}}
                <form id="filterForm" method="GET" action="{{ route('verifikasi_magnet.audit') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                    <div class="input-group" style="max-width:220px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-calendar-date text-muted"></i>
                        </span>
                        <input type="date" name="date" id="filter_date" class="form-control border-start-0"
                            value="{{ request('date') }}">
                    </div>

                    <div class="input-group flex-grow-1" style="max-width:350px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" id="search" class="form-control border-start-0"
                            value="{{ request('search') }}" placeholder="Cari username, produksi, kode, produk...">
                    </div>

                    @if (request('search') || request('date'))
                        <a href="{{ route('verifikasi_magnet.audit') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle me-1"></i> Reset
                        </a>
                    @endif
                </form>

                {{-- TABEL AUDIT --}}
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>NO.</th>
                                <th>Date | Shift</th>
                                <th>Pukul</th>
                                <th>Nama Produk</th>
                                <th>Kode Produksi</th>
                                <th>Bukti</th>
                                <th>Jumlah Temuan</th>
                                <th>Keterangan</th>
                                <th>QC</th>
                                <th>Produksi</th>
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
                                    <td class="text-center align-middle">
                                        {{ $no++ }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}
                                        | Shift: {{ $dep->shift }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->pukul ? \Carbon\Carbon::parse($dep->pukul)->format('H:i') : '-' }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->nama_produk ?? '-' }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->kode_produksi ?? '-' }}
                                    </td>

                                    <td class="text-center align-middle">
                                        @if ($dep->bukti)
                                            <a href="{{ asset('storage/' . $dep->bukti) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $dep->bukti) }}" alt="Bukti"
                                                    style="max-width:100px;height:auto;cursor:pointer;" loading="lazy">
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->jumlah_temuan ?? '-' }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->keterangan ?? '-' }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->username ?? '-' }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $dep->nama_produksi ?? '-' }}
                                    </td>

                                    {{-- STATUS SPV --}}
                                    <td class="text-center align-middle">
                                        @if ((string) $dep->status_spv === '0')
                                            <span class="fw-bold text-secondary">Created</span>
                                        @elseif ((string) $dep->status_spv === '1')
                                            <span class="fw-bold text-success">Verified</span>
                                        @elseif ((string) $dep->status_spv === '2')
                                            <a href="javascript:void(0)" data-bs-toggle="modal"
                                                data-bs-target="#revisionModal{{ $dep->uuid }}"
                                                class="text-danger fw-bold text-decoration-none">
                                                Revision
                                            </a>

                                            <div class="modal fade" id="revisionModal{{ $dep->uuid }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title">
                                                                Detail Revisi
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
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
                                                            <button type="button" class="btn btn-secondary btn-sm"
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

                                    {{-- ACTION --}}
                                    <td class="text-center align-middle">
                                        <a href="{{ route('verifikasi_magnet.edit', $dep->uuid) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i> Update
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center align-middle py-4">
                                        <i class="bi bi-inbox text-muted" style="font-size:1.5rem;"></i>
                                        <br>
                                        @if (request('search') || request('date'))
                                            Tidak ada data yang cocok dengan pencarian/filter.
                                        @else
                                            Belum ada data Audit Verifikasi Magnet Trap.
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

                <form method="GET" action="{{ route('verifikasi_magnet.exportPdfAudit') }}">

                    <div class="d-flex align-items-end gap-2">

                        <div class="col-auto">
                            <label for="start_date" class="col-form-label fw-semibold">
                                Dari Tanggal
                            </label>
                            <input type="date" id="start_date" name="start_date" class="form-control form-control-sm"
                                value="{{ request('start_date') }}" required>
                        </div>

                        <div class="col-auto">
                            <label for="end_date" class="col-form-label fw-semibold">
                                Sampai Tanggal
                            </label>
                            <input type="date" id="end_date" name="end_date" class="form-control form-control-sm"
                                value="{{ request('end_date') }}" required>
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
