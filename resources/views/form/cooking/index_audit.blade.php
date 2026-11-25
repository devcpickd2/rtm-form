
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
                    Data Pemeriksaan Pemasakan Produk di Steam/Cooking Kettle
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('cooking.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data Pemeriksaan Pemasakan Produk di Steam/Cooking Kettle
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
                        Data Pemeriksaan Pemasakan Produk di Steam/Cooking Kettle merupakan snapshot
                        atau salinan kondisi data saat pertama kali disimpan. Data ini digunakan
                        untuk pemeriksaan dan audit serta terpisah dari data operasional asli.
                    </div>
                </div>
            </div>

            {{-- FILTER --}}
            <form method="GET" action="{{ route('cooking.audit') }}"
                class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                <div class="input-group" style="max-width:220px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-calendar-date text-muted"></i>
                    </span>
                    <input type="date" name="date" class="form-control border-start-0"
                        value="{{ request('date') }}">
                </div>

                <div class="input-group flex-grow-1" style="max-width:350px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0"
                        value="{{ request('search') }}"
                        placeholder="Cari produk, kode produksi, username, mesin...">
                </div>

                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-search me-1"></i> Cari
                </button>

                @if(request()->filled('date') || request()->filled('search'))
                    <a href="{{ route('cooking.audit') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                @endif
            </form>

            {{-- TABEL --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>NO.</th>
                            <th>Date</th>
                            <th>Shift</th>
                            <th>Nama Produk</th>
                            <th>Sub Produk</th>
                            <th>Jenis Produk</th>
                            <th>Kode Produksi</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Nama Mesin</th>
                            <th>Detail Pemasakan</th>
                            <th>Catatan</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Waktu Snapshot</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data as $item)
                            @php
                                $namaMesin = is_array($item->nama_mesin)
                                    ? $item->nama_mesin
                                    : json_decode($item->nama_mesin ?? '[]', true);

                                $pemasakan = is_array($item->pemasakan)
                                    ? $item->pemasakan
                                    : json_decode($item->pemasakan ?? '[]', true);

                                $catatan = is_array($item->catatan)
                                    ? $item->catatan
                                    : json_decode($item->catatan ?? '[]', true);

                                if (!is_array($namaMesin)) {
                                    $namaMesin = !empty($item->nama_mesin)
                                        ? [$item->nama_mesin]
                                        : [];
                                }

                                if (!is_array($pemasakan)) {
                                    $pemasakan = [];
                                }

                                if (!is_array($catatan)) {
                                    $catatan = !empty($item->catatan)
                                        ? [['isi' => $item->catatan]]
                                        : [];
                                }
                            @endphp

                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $loop->index }}
                                </td>

                                <td class="text-center">
                                    {{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d-m-Y') : '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $item->shift ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->nama_produk ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->sub_produk ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->jenis_produk ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->kode_produksi ?? '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $item->waktu_mulai ?? '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $item->waktu_selesai ?? '-' }}
                                </td>

                                <td>
                                    @forelse($namaMesin as $mesin)
                                        <div>
                                            {{ is_array($mesin) ? json_encode($mesin, JSON_UNESCAPED_UNICODE) : $mesin }}
                                        </div>
                                    @empty
                                        -
                                    @endforelse
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#pemasakan{{ $item->uuid }}">
                                        <i class="bi bi-eye me-1"></i>
                                        Lihat Detail
                                    </button>
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-secondary"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#catatan{{ $item->uuid }}">
                                        <i class="bi bi-journal-text me-1"></i>
                                        Lihat Catatan
                                    </button>
                                </td>

                                <td>
                                    {{ $item->username ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->nama_produksi ?? '-' }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $item->tgl_update_produksi
                                            ? \Carbon\Carbon::parse($item->tgl_update_produksi)->format('d-m-Y H:i')
                                            : '-' }}
                                    </small>
                                </td>

                                <td>
                                    {{ $item->nama_spv ?? '-' }}
                                    <br>
                                    <small>
                                        @if($item->status_spv == 1)
                                            <span class="text-success fw-bold">Verified</span>
                                        @elseif($item->status_spv == 2)
                                            <span class="text-danger fw-bold">Revision</span>
                                        @else
                                            <span class="text-secondary">Belum diverifikasi</span>
                                        @endif

                                        <br>
                                        <span class="text-muted">
                                            {{ $item->tgl_update_spv
                                                ? \Carbon\Carbon::parse($item->tgl_update_spv)->format('d-m-Y H:i')
                                                : '-' }}
                                        </span>
                                    </small>
                                </td>

                                <td class="text-center">
                                    {{ $item->created_at
                                        ? $item->created_at->format('d-m-Y H:i:s')
                                        : '-' }}
                                </td>

                                <td class="text-center">
                                    @if($type_user != 5)
                                        <div class="d-flex flex-column gap-1">
                                            <a href="{{ route('cooking.edit', $item->uuid) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil"></i> Update
                                            </a>

                                            @if(in_array($type_user, [0, 2, 8]))
                                                <form method="GET"
                                                    action="{{ route('cooking.exportPdfAudit') }}"
                                                    target="_blank">
                                                    <input type="hidden" name="uuid"
                                                        value="{{ $item->uuid }}">

                                                    <button type="submit"
                                                        class="btn btn-danger btn-sm w-100">
                                                        <i class="bi bi-file-earmark-pdf"></i>
                                                        Export PDF
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">Read Only</span>
                                    @endif
                                </td>
                            </tr>

                            {{-- DETAIL PEMASAKAN --}}
                            <tr class="collapse" id="pemasakan{{ $item->uuid }}">
                                <td colspan="17">
                                    <div class="p-3 bg-light rounded">
                                        <h6 class="fw-bold mb-3">
                                            <i class="bi bi-clipboard-data me-1"></i>
                                            Detail Pemasakan
                                        </h6>

                                        @forelse($pemasakan as $index => $row)
                                            <div class="border rounded bg-white p-3 mb-3">
                                                <strong>Proses {{ $index + 1 }}</strong>

                                                <div class="table-responsive mt-2">
                                                    <table class="table table-sm table-bordered mb-0">
                                                        <tbody>
                                                            @if(is_array($row))
                                                                @foreach($row as $key => $value)
                                                                    <tr>
                                                                        <th style="width:220px;">
                                                                            {{ ucwords(str_replace('_', ' ', $key)) }}
                                                                        </th>
                                                                        <td>
                                                                            @if(is_array($value))
                                                                                {{ json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}
                                                                            @else
                                                                                {{ $value !== '' && $value !== null ? $value : '-' }}
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td>{{ $row }}</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @empty
                                            <span class="text-muted">
                                                Tidak ada detail pemasakan.
                                            </span>
                                        @endforelse
                                    </div>
                                </td>
                            </tr>

                            {{-- DETAIL CATATAN --}}
                            <tr class="collapse" id="catatan{{ $item->uuid }}">
                                <td colspan="17">
                                    <div class="p-3 bg-light rounded">
                                        <h6 class="fw-bold mb-3">
                                            <i class="bi bi-journal-text me-1"></i>
                                            Catatan Pemeriksaan
                                        </h6>

                                        @forelse($catatan as $note)
                                            <div class="border rounded bg-white p-2 mb-2">
                                                @if(is_array($note))
                                                    @if(!empty($note['pukul_awal']) || !empty($note['pukul_akhir']))
                                                        <strong>
                                                            {{ $note['pukul_awal'] ?? '-' }}
                                                            -
                                                            {{ $note['pukul_akhir'] ?? '-' }}
                                                        </strong>
                                                        <br>
                                                    @endif

                                                    {{ $note['isi'] ?? json_encode($note, JSON_UNESCAPED_UNICODE) }}
                                                @else
                                                    {{ $note }}
                                                @endif
                                            </div>
                                        @empty
                                            <span class="text-muted">
                                                Tidak ada catatan pemeriksaan.
                                            </span>
                                        @endforelse

                                        @if(!empty($item->catatan_spv))
                                            <hr>
                                            <strong>Catatan SPV:</strong>
                                            {{ $item->catatan_spv }}
                                        @endif
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="17" class="text-center py-4">
                                    <i class="bi bi-inbox fs-3 d-block text-muted mb-2"></i>
                                    Belum ada data Audit Cooking.
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
        vertical-align: middle;
    }
</style>
@endsection
