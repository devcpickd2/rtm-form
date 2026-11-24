
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
                    Data Pemeriksaan Proses Tumbling
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('thumbling.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data Pemeriksaan Proses Tumbling
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
                        Data Pemeriksaan Proses Tumbling merupakan snapshot
                        atau salinan kondisi data saat pertama kali disimpan.
                        Data ini digunakan untuk pemeriksaan dan audit serta
                        terpisah dari data operasional asli.
                    </div>
                </div>
            </div>

            {{-- FILTER --}}
            <form id="filterForm"
                method="GET"
                action="{{ route('thumbling.audit') }}"
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
                        placeholder="Cari produk, kode produksi, username...">
                </div>

                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-search me-1"></i>
                    Cari
                </button>

                @if(request('date') || request('search'))
                    <a href="{{ route('thumbling.audit') }}"
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
                            <th>NO.</th>
                            <th>Date | Shift</th>
                            <th>Nama Produk</th>
                            <th>Tumbling</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody">
                        @forelse ($data as $index => $dep)
                            <tr>
                                <td class="text-center align-middle">
                                    {{ $data->firstItem() + $index }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->date
                                        ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                        : '-' }}
                                    | Shift: {{ $dep->shift ?? '-' }}
                                </td>

                                <td class="text-center align-middle">
                                    {{ $dep->nama_produk ?? '-' }}
                                </td>

                                {{-- DETAIL TUMBLING --}}
                                <td class="text-center align-middle">
                                    <a href="javascript:void(0);"
                                        data-bs-toggle="modal"
                                        data-bs-target="#thumblingModal{{ $dep->uuid }}"
                                        class="fw-bold text-decoration-underline">
                                        Detail Tumbling
                                    </a>

                                    <div class="modal fade"
                                        id="thumblingModal{{ $dep->uuid }}"
                                        tabindex="-1"
                                        aria-labelledby="thumblingModalLabel{{ $dep->uuid }}"
                                        aria-hidden="true">

                                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                            <div class="modal-content">

                                                <div class="modal-header bg-warning text-white">
                                                    <h5 class="modal-title text-start"
                                                        id="thumblingModalLabel{{ $dep->uuid }}">
                                                        Detail Tumbling
                                                    </h5>

                                                    <button type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal"
                                                        aria-label="Close">
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-sm align-middle">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th style="width:250px; text-align:left;">
                                                                        Parameter
                                                                    </th>
                                                                    <th style="text-align:left;">
                                                                        Data
                                                                    </th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                {{-- PROSES TUMBLING --}}
                                                                <tr class="table-secondary">
                                                                    <td colspan="2" class="text-start">
                                                                        <strong>PROSES TUMBLING</strong>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-start">Batch No.</td>
                                                                    <td class="text-start">
                                                                        {{ $dep->kode_produksi ?? '-' }}
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-start">Identifikasi Daging</td>
                                                                    <td class="text-start">
                                                                        {{ $dep->identifikasi_daging ?? '-' }}
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-start">Asal Daging</td>
                                                                    <td class="text-start">
                                                                        {{ $dep->asal_daging ?? '-' }}
                                                                    </td>
                                                                </tr>

                                                                {{-- KODE DAGING --}}
                                                                <tr>
                                                                    <td class="text-start">Kode Daging</td>
                                                                    <td class="text-start">
                                                                        @php
                                                                            $kodeDaging = is_array($dep->kode_daging)
                                                                                ? $dep->kode_daging
                                                                                : (json_decode($dep->kode_daging ?? '[]', true) ?: []);

                                                                            $beratDaging = is_array($dep->berat_daging)
                                                                                ? $dep->berat_daging
                                                                                : (json_decode($dep->berat_daging ?? '[]', true) ?: []);

                                                                            $suhuDaging = is_array($dep->suhu_daging)
                                                                                ? $dep->suhu_daging
                                                                                : (json_decode($dep->suhu_daging ?? '[]', true) ?: []);

                                                                            $rataDaging = is_array($dep->rata_daging)
                                                                                ? $dep->rata_daging
                                                                                : (json_decode($dep->rata_daging ?? '[]', true) ?: []);
                                                                        @endphp

                                                                        @if(count($kodeDaging))
                                                                            <table class="table table-bordered table-sm mb-0">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th>Kode</th>
                                                                                        <th>Berat (kg)</th>
                                                                                        <th>Suhu (0-10°C)</th>
                                                                                        <th>Rata-rata</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($kodeDaging as $i => $kode)
                                                                                        <tr>
                                                                                            <td>{{ $kode ?? '-' }}</td>
                                                                                            <td>{{ $beratDaging[$i] ?? '-' }}</td>
                                                                                            <td>
                                                                                                @php
                                                                                                    $suhuItem = $suhuDaging[$i] ?? [];
                                                                                                @endphp

                                                                                                {{ is_array($suhuItem)
                                                                                                    ? implode(', ', $suhuItem)
                                                                                                    : $suhuItem }}
                                                                                            </td>
                                                                                            <td>{{ $rataDaging[$i] ?? '-' }}</td>
                                                                                        </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                </tr>

                                                                {{-- BAHAN UTAMA --}}
                                                                <tr>
                                                                    <td class="text-start">Bahan Utama</td>
                                                                    <td class="text-start">
                                                                        @php
                                                                            $premix = is_array($dep->premix)
                                                                                ? $dep->premix
                                                                                : (json_decode($dep->premix ?? '[]', true) ?: []);

                                                                            $kodePremix = is_array($dep->kode_premix)
                                                                                ? $dep->kode_premix
                                                                                : (json_decode($dep->kode_premix ?? '[]', true) ?: []);

                                                                            $beratPremix = is_array($dep->berat_premix)
                                                                                ? $dep->berat_premix
                                                                                : (json_decode($dep->berat_premix ?? '[]', true) ?: []);
                                                                        @endphp

                                                                        @if(count($premix))
                                                                            <table class="table table-bordered table-sm mb-0">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th>Bahan</th>
                                                                                        <th>Kode</th>
                                                                                        <th>Berat (kg)</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($premix as $i => $bahan)
                                                                                        <tr>
                                                                                            <td>{{ $bahan ?? '-' }}</td>
                                                                                            <td>{{ $kodePremix[$i] ?? '-' }}</td>
                                                                                            <td>{{ $beratPremix[$i] ?? '-' }}</td>
                                                                                        </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                </tr>

                                                                {{-- BAHAN LAIN --}}
                                                                <tr>
                                                                    <td class="text-start">Bahan Lain</td>
                                                                    <td class="text-start">
                                                                        @php
                                                                            $bahanLain = is_array($dep->bahan_lain)
                                                                                ? $dep->bahan_lain
                                                                                : (json_decode($dep->bahan_lain ?? '[]', true) ?: []);
                                                                        @endphp

                                                                        @if(count($bahanLain))
                                                                            <table class="table table-bordered table-sm mb-0">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th>Bahan</th>
                                                                                        <th>Kode</th>
                                                                                        <th>Berat (kg)</th>
                                                                                        <th>Sensori</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($bahanLain as $bahan)
                                                                                        <tr>
                                                                                            <td>{{ $bahan['premix'] ?? '-' }}</td>
                                                                                            <td>{{ $bahan['kode'] ?? '-' }}</td>
                                                                                            <td>{{ $bahan['berat'] ?? '-' }}</td>
                                                                                            <td>{{ $bahan['sens'] ?? '-' }}</td>
                                                                                        </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                </tr>

                                                                {{-- PARAMETER CAIRAN --}}
                                                                <tr class="table-secondary">
                                                                    <td colspan="2" class="text-start">
                                                                        <strong>PARAMETER CAIRAN</strong>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-start">Air (kg)</td>
                                                                    <td class="text-start">{{ $dep->air ?? '-' }}</td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-start">Suhu Air (°C)</td>
                                                                    <td class="text-start">{{ $dep->suhu_air ?? '-' }}</td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-start">Suhu Marinade (°C)</td>
                                                                    <td class="text-start">{{ $dep->suhu_marinade ?? '-' }}</td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-start">Lama Pengadukan (menit)</td>
                                                                    <td class="text-start">{{ $dep->lama_pengadukan ?? '-' }}</td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-start">Marinade Brix – Salinity</td>
                                                                    <td class="text-start">{{ $dep->marinade_brix_salinity ?? '-' }}</td>
                                                                </tr>

                                                                {{-- PARAMETER TUMBLING --}}
                                                                <tr class="table-secondary">
                                                                    <td colspan="2" class="text-start">
                                                                        <strong>PARAMETER TUMBLING</strong>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-start">Drum On (Menit)</td>
                                                                    <td class="text-start">{{ $dep->drum_on ?? '-' }}</td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-start">Drum Off (Menit)</td>
                                                                    <td class="text-start">{{ $dep->drum_off ?? '-' }}</td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-start">Drum Speed (RPM)</td>
                                                                    <td class="text-start">{{ $dep->drum_speed ?? '-' }}</td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-start">Vacuum Time (Menit)</td>
                                                                    <td class="text-start">{{ $dep->vacuum_time ?? '-' }}</td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-start">Total Time (Menit)</td>
                                                                    <td class="text-start">{{ $dep->total_time ?? '-' }}</td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-start">Mulai – Selesai</td>
                                                                    <td class="text-start">
                                                                        {{ $dep->waktu_mulai ?? '-' }}
                                                                        –
                                                                        {{ $dep->waktu_selesai ?? '-' }}
                                                                    </td>
                                                                </tr>

                                                                {{-- HASIL TUMBLING --}}
                                                                <tr class="table-secondary">
                                                                    <td colspan="2" class="text-start">
                                                                        <strong>HASIL TUMBLING</strong>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-start">Suhu Daging</td>
                                                                    <td class="text-start">
                                                                        @php
                                                                            $suhuAkhir = is_array($dep->suhu_daging_thumbling)
                                                                                ? $dep->suhu_daging_thumbling
                                                                                : (json_decode($dep->suhu_daging_thumbling ?? '[]', true) ?: []);
                                                                        @endphp

                                                                        @if(count($suhuAkhir))
                                                                            <table class="table table-bordered table-sm mb-2">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        @foreach($suhuAkhir as $i => $suhu)
                                                                                            <th>Suhu {{ $i + 1 }}</th>
                                                                                        @endforeach
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        @foreach($suhuAkhir as $suhu)
                                                                                            <td>{{ $suhu }}</td>
                                                                                        @endforeach
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                            <strong>Rata-rata:</strong>
                                                                            {{ $dep->rata_daging_thumbling ?? '-' }}
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                </tr>

                                                                {{-- KONDISI DAN CATATAN --}}
                                                                <tr class="table-secondary">
                                                                    <td colspan="2" class="text-start">
                                                                        <strong>KONDISI & CATATAN</strong>
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-start">Kondisi Akhir</td>
                                                                    <td class="text-start">
                                                                        {{ $dep->kondisi_daging_akhir ?? '-' }}
                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <td class="text-start">Catatan Akhir</td>
                                                                    <td class="text-start">
                                                                        {{ $dep->catatan_akhir ?? '-' }}
                                                                    </td>
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
                                </td>

                                {{-- QC --}}
                                <td class="text-center align-middle">
                                    {{ $dep->username ?? '-' }}
                                </td>

                                {{-- PRODUKSI --}}
                                <td class="text-center align-middle">
                                    {{ $dep->nama_produksi ?? '-' }}
                                </td>

                                {{-- STATUS SPV --}}
                                <td class="text-center align-middle">
                                    @if ($dep->status_spv == 0)
                                        <span class="fw-bold text-secondary">Created</span>
                                    @elseif ($dep->status_spv == 1)
                                        <span class="fw-bold text-success">Verified</span>
                                    @elseif ($dep->status_spv == 2)
                                        <a href="javascript:void(0);"
                                            data-bs-toggle="modal"
                                            data-bs-target="#revisionModal{{ $dep->uuid }}"
                                            class="fw-bold text-danger text-decoration-none">
                                            Revision
                                        </a>

                                        <div class="modal fade"
                                            id="revisionModal{{ $dep->uuid }}"
                                            tabindex="-1"
                                            aria-labelledby="revisionModalLabel{{ $dep->uuid }}"
                                            aria-hidden="true">

                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title"
                                                            id="revisionModalLabel{{ $dep->uuid }}">
                                                            Detail Revisi
                                                        </h5>

                                                        <button type="button"
                                                            class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal"
                                                            aria-label="Close">
                                                        </button>
                                                    </div>

                                                    <div class="modal-body text-start">
                                                        <p>
                                                            <strong>Status:</strong>
                                                            Revision
                                                        </p>
                                                        <p class="mb-0">
                                                            <strong>Catatan SPV:</strong>
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
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- ACTION --}}
                                <td class="text-center align-middle">
                                    <a href="{{ route('thumbling.edit', $dep->uuid) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                        Update
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox fs-4"></i>
                                    <br>
                                    Belum ada data Audit Thumbling.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- INFORMASI DATA --}}
            @if ($data->total() > 0)
                <div class="text-muted small mb-2">
                    Menampilkan {{ $data->firstItem() }}–{{ $data->lastItem() }}
                    dari {{ $data->total() }} data
                </div>
            @endif

            {{-- PAGINATION --}}
            <div class="mt-3">
                {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

            {{-- EXPORT PDF SESUAI HALAMAN UTAMA --}}
            @if (in_array($type_user, [0, 2, 8]))
                <form method="GET" action="{{ route('thumbling.exportPdf') }}">
                    <div class="d-flex flex-wrap align-items-end gap-2 mt-4 pt-3 border-top">

                        <div>
                            <label for="export_date" class="form-label fw-semibold">
                                Pilih Tanggal
                            </label>
                            <input type="date"
                                id="export_date"
                                name="date"
                                class="form-control form-control-sm"
                                required>
                        </div>

                        <div>
                            <label for="nama_produk" class="form-label fw-semibold">
                                Nama Produk
                            </label>
                            <select id="nama_produk"
                                name="nama_produk"
                                class="form-select form-select-sm"
                                required>
                                <option value="">-- Pilih Produk --</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-file-earmark-pdf"></i>
                            Export PDF
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
