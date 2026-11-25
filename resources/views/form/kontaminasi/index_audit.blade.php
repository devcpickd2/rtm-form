
@extends('layouts.app')

@php
    $user = auth()->user();
    $type_user = $user->type_user ?? null;
@endphp

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm">
        <div class="card-body">

            {{-- JUDUL --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-list-check"></i>
                    Data Kontaminasi Benda Asing
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('kontaminasi.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data Kontaminasi Benda Asing
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
                        Data Kontaminasi Benda Asing merupakan snapshot atau salinan kondisi data saat pertama kali disimpan.
                        Data ini digunakan untuk pemeriksaan dan audit serta terpisah dari data operasional asli.
                    </div>
                </div>
            </div>

            {{-- FILTER --}}
            <form method="GET"
                  action="{{ route('kontaminasi.audit') }}"
                  class="d-flex flex-wrap align-items-end gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                <div>
                    <label for="filter_date" class="form-label mb-1">Tanggal Produksi</label>
                    <input type="date"
                           name="date"
                           id="filter_date"
                           class="form-control form-control-sm"
                           value="{{ request('date') }}">
                </div>

                <div style="min-width:250px;">
                    <label for="search" class="form-label mb-1">Pencarian</label>
                    <input type="text"
                           name="search"
                           id="search"
                           class="form-control form-control-sm"
                           placeholder="QC, Produksi, jenis kontaminasi, kode produksi..."
                           value="{{ request('search') }}">
                </div>

                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-search"></i> Cari
                </button>

                @if(request('date') || request('search'))
                    <a href="{{ route('kontaminasi.audit') }}"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
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
                            <th>Jenis Kontaminasi</th>
                            <th>Bukti</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Tahapan</th>
                            <th>Tindakan Koreksi</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data as $dep)
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
                                    {{ $dep->pukul
                                        ? \Carbon\Carbon::parse($dep->pukul)->format('H:i')
                                        : '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->jenis_kontaminasi ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    @if($dep->bukti)
                                        <a href="{{ asset('storage/' . $dep->bukti) }}"
                                           target="_blank"
                                           rel="noopener noreferrer">
                                            <img src="{{ asset('storage/' . $dep->bukti) }}"
                                                 alt="Bukti Kontaminasi"
                                                 style="max-width:100px;height:auto;">
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->nama_produk ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->kode_produksi ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->tahapan ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->tindakan_koreksi ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->username ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->nama_produksi ?? '-' }}
                                </td>

                                {{-- STATUS SPV --}}
                                <td class="text-center align-middle">
                                    @if($dep->status_spv == 1)
                                        <span class="fw-bold text-success">
                                            Verified
                                        </span>
                                    @elseif($dep->status_spv == 2)
                                        <a href="javascript:void(0)"
                                           data-bs-toggle="modal"
                                           data-bs-target="#revisionAudit{{ $dep->uuid }}"
                                           class="fw-bold text-danger text-decoration-none">
                                            Revision
                                        </a>

                                        @if($dep->catatan_spv)
                                            <div class="modal fade"
                                                 id="revisionAudit{{ $dep->uuid }}"
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
                                                                    data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body text-start">
                                                            <p>
                                                                <strong>Status:</strong>
                                                                Revision
                                                            </p>
                                                            <p class="mb-0">
                                                                <strong>Catatan SPV:</strong><br>
                                                                {{ $dep->catatan_spv }}
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
                                            <span class="small text-muted">
                                                Tanpa catatan
                                            </span>
                                        @endif
                                    @else
                                        <span class="fw-bold text-secondary">
                                            Created
                                        </span>
                                    @endif
                                </td>

                                {{-- ACTION --}}
                                <td class="text-center align-middle">
                                    @if($type_user != 5)
                                        <a href="{{ route('kontaminasi.edit', $dep->uuid) }}"
                                           class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i> Update
                                        </a>
                                    @else
                                        <span class="badge bg-secondary">
                                            Read Only
                                        </span>
                                    @endif
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="13" class="text-center align-middle py-4">
                                    Belum ada data Audit Kontaminasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- INFORMASI DATA --}}
            @if($data->total() > 0)
                <div class="text-muted small mb-2">
                    Menampilkan {{ $data->firstItem() }}–{{ $data->lastItem() }}
                    dari {{ $data->total() }} data
                </div>
            @endif

            {{-- PAGINATION --}}
            <div class="mt-3">
                {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

            {{-- EXPORT PDF --}}
            @if(in_array($type_user, [0, 2, 8]))
                <form method="GET" action="{{ route('kontaminasi.exportPdfAudit') }}" class="mt-3">
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
                            <i class="bi bi-file-earmark-pdf"></i> Export PDF
                        </button>
                    </div>
                </form>
            @endif

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
