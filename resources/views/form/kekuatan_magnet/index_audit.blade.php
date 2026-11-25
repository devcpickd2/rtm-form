@extends('layouts.app')

@section('content')
    <div class="container-fluid py-0">
        <div class="card shadow-sm">
            <div class="card-body">

                {{-- JUDUL --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <h3 class="mb-0 fw-bold">
                        <i class="bi bi-list-check me-1"></i>
                        Data Pemeriksaan Kekuatan Magnet Trap
                        <span class="badge audit-badge">MODE AUDIT</span>
                    </h3>
                </div>

                {{-- BREADCRUMB --}}
                <div class="audit-breadcrumb mb-3">
                    <a href="{{ route('kekuatan_magnet.index') }}">
                        <i class="bi bi-arrow-left me-1"></i>
                        Data Pemeriksaan Kekuatan Magnet Trap
                    </a>
                    <span class="mx-2">/</span>
                    <span class="fw-bold">Data Audit</span>
                </div>

                {{-- NOTE AUDIT --}}
                <div class="alert audit-info d-flex align-items-start mb-4" role="alert">
                    <i class="bi bi-shield-check me-3 fs-4"></i>
                    <div>
                        <strong class="d-block mb-1">Halaman Data Audit</strong>
                        <div class="small">
                            Anda sedang melihat data pemeriksaan kekuatan magnet trap
                            yang tersimpan pada halaman Audit. Data ini merupakan
                            salinan untuk kebutuhan pemeriksaan dan audit, sehingga
                            perubahan pada data operasional asli tidak terpengaruh.
                        </div>
                    </div>
                </div>

                {{-- FILTER --}}
                <form method="GET" action="{{ route('kekuatan_magnet.audit') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                    <div class="input-group" style="max-width:220px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-calendar-date text-muted"></i>
                        </span>
                        <input type="date" name="date" class="form-control border-start-0"
                            value="{{ request('date') }}" placeholder="Tanggal">
                    </div>

                    @if (request('date'))
                        <a href="{{ route('kekuatan_magnet.audit') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    @endif
                </form>

                {{-- TABEL --}}
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>NO.</th>
                                <th>Date | Shift</th>
                                <th>Nama Alat</th>
                                <th>Kondisi Magnet (Visual)</th>
                                <th>Parameter Setting</th>
                                <th>Nilai Pengukuran (Gauss)</th>
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

                            @forelse($data as $dep)
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>

                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}
                                        | Shift: {{ $dep->shift }}
                                    </td>

                                    <td class="text-center">{{ $dep->nama_alat }}</td>
                                    <td class="text-center">{{ $dep->kondisi }}</td>
                                    <td class="text-center">{{ $dep->parameter }}</td>
                                    <td class="text-center">{{ $dep->gauss }}</td>
                                    <td class="text-center">{{ $dep->keterangan ?: '-' }}</td>
                                    <td class="text-center">{{ $dep->username ?: '-' }}</td>
                                    <td class="text-center">{{ $dep->nama_produksi ?: '-' }}</td>

                                    <td class="text-center">
                                        @if ($dep->status_spv == 0)
                                            <span class="fw-bold text-secondary">Created</span>
                                        @elseif($dep->status_spv == 1)
                                            <span class="fw-bold text-success">Verified</span>
                                        @elseif($dep->status_spv == 2)
                                            <span class="fw-bold text-danger" title="{{ $dep->catatan_spv }}">
                                                Revision
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('kekuatan_magnet.edit', $dep->uuid) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i> Update
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center">
                                        Belum ada data Audit kekuatan magnet.
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

                <form method="GET" action="{{ route('kekuatan_magnet.exportPdfAudit') }}">

                    <div class="d-flex align-items-center flex-wrap">

                        <label for="year" class="fw-semibold mb-0 export-label">
                            <i class="bi bi-calendar3 me-1 text-danger"></i>
                            Periode Tahun
                        </label>

                        <select id="year" name="year" class="year-select" required>
                            <option value="">Pilih Tahun</option>

                            @for ($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>

                        <button type="submit" class="btn btn-danger btn-sm px-3 export-btn">
                            <i class="bi bi-file-earmark-pdf me-1"></i>
                            Export PDF
                        </button>

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
    </style>
@endsection
