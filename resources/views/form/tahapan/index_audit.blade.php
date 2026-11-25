
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
                    Data Tahapan Suhu Produk Setiap Tahapan Proses
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('tahapan.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data Tahapan Suhu Produk Setiap Tahapan Proses
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
                        Data Tahapan Suhu Produk Setiap Tahapan Proses merupakan
                        snapshot atau salinan kondisi data saat pertama kali disimpan.
                        Data ini digunakan untuk pemeriksaan dan audit serta
                        terpisah dari data operasional asli.
                    </div>
                </div>
            </div>

            {{-- FILTER --}}
            <form id="filterForm"
                method="GET"
                action="{{ route('tahapan.audit') }}"
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
                    <a href="{{ route('tahapan.audit') }}"
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
                            <th rowspan="2">Date | Shift</th>
                            <th rowspan="2">Nama Produk</th>
                            <th rowspan="2">Kode Produksi</th>
                            <th colspan="8">JAM MULAI</th>
                            <th>SUHU PRODUK (°C)</th>
                            <th rowspan="2">Catatan</th>
                            <th rowspan="2">QC</th>
                            <th rowspan="2">Produksi</th>
                            <th rowspan="2">SPV</th>
                            <th rowspan="2">Action</th>
                        </tr>
                        <tr>
                            <th>Filling/Portioning</th>
                            <th>IQF</th>
                            <th>Top Sealer</th>
                            <th>X-Ray</th>
                            <th>Sticker</th>
                            <th>Shrink</th>
                            <th>Packing Box</th>
                            <th>Cold Storage</th>
                            <th>Result Suhu</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody">
                        @forelse ($data as $index => $dep)
                            @php
                                // SUHU FILLING
                                $suhu_filling = $dep->suhu_filling;

                                if (is_string($suhu_filling)) {
                                    $decoded = json_decode($suhu_filling, true);
                                    $suhu_filling = is_array($decoded) ? $decoded : [];
                                } elseif (!is_array($suhu_filling)) {
                                    $suhu_filling = [];
                                }

                                $suhu_filling = array_values(array_filter(
                                    $suhu_filling,
                                    function ($item) {
                                        return is_array($item) &&
                                            (!empty($item['nama_bahan']) || !empty($item['suhu']));
                                    }
                                ));

                                // SUHU TAHAPAN LAINNYA
                                $suhuFields = [
                                    'suhu_masuk_iqf',
                                    'suhu_keluar_iqf',
                                    'suhu_sealer',
                                    'suhu_xray',
                                    'suhu_sticker',
                                    'suhu_shrink',
                                ];

                                $suhuData = [];

                                foreach ($suhuFields as $field) {
                                    $value = $dep->$field;

                                    if (is_string($value)) {
                                        $decoded = json_decode($value, true);
                                        $value = is_array($decoded) ? $decoded : [];
                                    }

                                    if (!is_array($value)) {
                                        $value = [];
                                    }

                                    $suhuData[$field] = array_values(array_filter(
                                        $value,
                                        fn($v) => $v !== null && $v !== ''
                                    ));
                                }

                                $totalRow = max(
                                    count($suhu_filling),
                                    count($suhuData['suhu_masuk_iqf']),
                                    count($suhuData['suhu_keluar_iqf']),
                                    count($suhuData['suhu_sealer']),
                                    count($suhuData['suhu_xray']),
                                    count($suhuData['suhu_sticker']),
                                    count($suhuData['suhu_shrink']),
                                    1
                                );

                                $tanggalFields = [
                                    'tanggal_filling',
                                    'tanggal_iqf',
                                    'tanggal_sealer',
                                    'tanggal_xray',
                                    'tanggal_sticker',
                                    'tanggal_shrink',
                                    'tanggal_packing',
                                    'tanggal_cs',
                                ];

                                $waktuFields = [
                                    'filling_mulai',
                                    'filling_selesai',
                                    'waktu_iqf',
                                    'waktu_sealer',
                                    'waktu_xray',
                                    'waktu_sticker',
                                    'waktu_shrink',
                                    'waktu_packing',
                                    'waktu_cs',
                                ];
                            @endphp

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

                                <td class="text-center align-middle">
                                    {{ $dep->kode_produksi ?? '-' }}
                                </td>

                                {{-- FILLING / PORTIONING --}}
                                <td class="text-center align-middle">
                                    {{ $dep->filling_mulai
                                        ? \Carbon\Carbon::parse($dep->filling_mulai)->format('H:i')
                                        : '-' }}
                                    -
                                    {{ $dep->filling_selesai
                                        ? \Carbon\Carbon::parse($dep->filling_selesai)->format('H:i')
                                        : '-' }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $dep->tanggal_filling
                                            ? \Carbon\Carbon::parse($dep->tanggal_filling)->format('d-m-Y')
                                            : '' }}
                                    </small>
                                </td>

                                {{-- IQF --}}
                                <td class="text-center align-middle">
                                    {{ $dep->waktu_iqf
                                        ? \Carbon\Carbon::parse($dep->waktu_iqf)->format('H:i')
                                        : '-' }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $dep->tanggal_iqf
                                            ? \Carbon\Carbon::parse($dep->tanggal_iqf)->format('d-m-Y')
                                            : '' }}
                                    </small>
                                </td>

                                {{-- TOP SEALER --}}
                                <td class="text-center align-middle">
                                    {{ $dep->waktu_sealer
                                        ? \Carbon\Carbon::parse($dep->waktu_sealer)->format('H:i')
                                        : '-' }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $dep->tanggal_sealer
                                            ? \Carbon\Carbon::parse($dep->tanggal_sealer)->format('d-m-Y')
                                            : '' }}
                                    </small>
                                </td>

                                {{-- X-RAY --}}
                                <td class="text-center align-middle">
                                    {{ $dep->waktu_xray
                                        ? \Carbon\Carbon::parse($dep->waktu_xray)->format('H:i')
                                        : '-' }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $dep->tanggal_xray
                                            ? \Carbon\Carbon::parse($dep->tanggal_xray)->format('d-m-Y')
                                            : '' }}
                                    </small>
                                </td>

                                {{-- STICKER --}}
                                <td class="text-center align-middle">
                                    {{ $dep->waktu_sticker
                                        ? \Carbon\Carbon::parse($dep->waktu_sticker)->format('H:i')
                                        : '-' }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $dep->tanggal_sticker
                                            ? \Carbon\Carbon::parse($dep->tanggal_sticker)->format('d-m-Y')
                                            : '' }}
                                    </small>
                                </td>

                                {{-- SHRINK --}}
                                <td class="text-center align-middle">
                                    {{ $dep->waktu_shrink
                                        ? \Carbon\Carbon::parse($dep->waktu_shrink)->format('H:i')
                                        : '-' }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $dep->tanggal_shrink
                                            ? \Carbon\Carbon::parse($dep->tanggal_shrink)->format('d-m-Y')
                                            : '' }}
                                    </small>
                                </td>

                                {{-- PACKING BOX --}}
                                <td class="text-center align-middle">
                                    {{ $dep->waktu_packing
                                        ? \Carbon\Carbon::parse($dep->waktu_packing)->format('H:i')
                                        : '-' }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $dep->tanggal_packing
                                            ? \Carbon\Carbon::parse($dep->tanggal_packing)->format('d-m-Y')
                                            : '' }}
                                    </small>
                                </td>

                                {{-- COLD STORAGE --}}
                                <td class="text-center align-middle">
                                    {{ $dep->waktu_cs
                                        ? \Carbon\Carbon::parse($dep->waktu_cs)->format('H:i')
                                        : '-' }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $dep->tanggal_cs
                                            ? \Carbon\Carbon::parse($dep->tanggal_cs)->format('d-m-Y')
                                            : '' }}
                                    </small>
                                </td>

                                {{-- RESULT SUHU --}}
                                <td class="text-center align-middle">
                                    @if (
                                        !empty($suhu_filling) ||
                                        !empty(array_filter($suhuData)) ||
                                        !empty($dep->downtime) ||
                                        !empty($dep->suhu_cs)
                                    )
                                        <a href="javascript:void(0);"
                                            data-bs-toggle="modal"
                                            data-bs-target="#suhuModal{{ $dep->uuid }}"
                                            class="fw-bold text-decoration-underline">
                                            Result
                                        </a>

                                        {{-- MODAL DETAIL SUHU --}}
                                        <div class="modal fade"
                                            id="suhuModal{{ $dep->uuid }}"
                                            tabindex="-1"
                                            aria-labelledby="suhuModalLabel{{ $dep->uuid }}"
                                            aria-hidden="true">

                                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                <div class="modal-content">

                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title"
                                                            id="suhuModalLabel{{ $dep->uuid }}">
                                                            Detail Pengecekan Suhu Produk
                                                        </h5>

                                                        <button type="button"
                                                            class="btn-close"
                                                            data-bs-dismiss="modal"
                                                            aria-label="Close">
                                                        </button>
                                                    </div>

                                                    <div class="modal-body p-0">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-sm mb-0 text-center">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th rowspan="2">No</th>
                                                                        <th rowspan="2">Filling</th>
                                                                        <th colspan="6">Tahapan Lainnya</th>
                                                                        <th rowspan="2">Downtime</th>
                                                                        <th rowspan="2">Cold Storage</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Masuk IQF</th>
                                                                        <th>Keluar IQF</th>
                                                                        <th>Top Sealer</th>
                                                                        <th>X-Ray</th>
                                                                        <th>Sticker</th>
                                                                        <th>Shrink</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody>
                                                                    @for ($i = 0; $i < $totalRow; $i++)
                                                                        <tr>
                                                                            <td>{{ $i + 1 }}</td>

                                                                            <td class="text-start">
                                                                                @if (isset($suhu_filling[$i]))
                                                                                    {{ $suhu_filling[$i]['nama_bahan'] ?? '-' }}
                                                                                    @if (
                                                                                        isset($suhu_filling[$i]['suhu']) &&
                                                                                        $suhu_filling[$i]['suhu'] !== ''
                                                                                    )
                                                                                        : {{ $suhu_filling[$i]['suhu'] }}°C
                                                                                    @endif
                                                                                @else
                                                                                    -
                                                                                @endif
                                                                            </td>

                                                                            <td>
                                                                                {{ isset($suhuData['suhu_masuk_iqf'][$i])
                                                                                    ? $suhuData['suhu_masuk_iqf'][$i] . '°C'
                                                                                    : '-' }}
                                                                            </td>

                                                                            <td>
                                                                                {{ isset($suhuData['suhu_keluar_iqf'][$i])
                                                                                    ? $suhuData['suhu_keluar_iqf'][$i] . '°C'
                                                                                    : '-' }}
                                                                            </td>

                                                                            <td>
                                                                                {{ isset($suhuData['suhu_sealer'][$i])
                                                                                    ? $suhuData['suhu_sealer'][$i] . '°C'
                                                                                    : '-' }}
                                                                            </td>

                                                                            <td>
                                                                                {{ isset($suhuData['suhu_xray'][$i])
                                                                                    ? $suhuData['suhu_xray'][$i] . '°C'
                                                                                    : '-' }}
                                                                            </td>

                                                                            <td>
                                                                                {{ isset($suhuData['suhu_sticker'][$i])
                                                                                    ? $suhuData['suhu_sticker'][$i] . '°C'
                                                                                    : '-' }}
                                                                            </td>

                                                                            <td>
                                                                                {{ isset($suhuData['suhu_shrink'][$i])
                                                                                    ? $suhuData['suhu_shrink'][$i] . '°C'
                                                                                    : '-' }}
                                                                            </td>

                                                                            @if ($i == 0)
                                                                                <td rowspan="{{ $totalRow }}"
                                                                                    class="align-middle">
                                                                                    {{ $dep->downtime !== null && $dep->downtime !== ''
                                                                                        ? $dep->downtime . ' Menit'
                                                                                        : '-' }}
                                                                                </td>

                                                                                <td rowspan="{{ $totalRow }}"
                                                                                    class="align-middle">
                                                                                    {{ $dep->suhu_cs !== null && $dep->suhu_cs !== ''
                                                                                        ? $dep->suhu_cs . ' °C'
                                                                                        : '-' }}
                                                                                </td>
                                                                            @endif
                                                                        </tr>
                                                                    @endfor
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        <div class="p-3">
                                                            <div class="alert alert-light mb-0 text-start">
                                                                <strong>Catatan:</strong>
                                                                <br>
                                                                {!! !empty($dep->catatan)
                                                                    ? nl2br(e($dep->catatan))
                                                                    : '-' !!}
                                                            </div>
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
                                        <span>-</span>
                                    @endif
                                </td>

                                {{-- CATATAN --}}
                                <td class="text-center align-middle">
                                    {{ $dep->keterangan ?? '-' }}
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

                                        {{-- MODAL REVISI --}}
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
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- ACTION --}}
                                <td class="text-center align-middle">
                                    <a href="{{ route('tahapan.edit', $dep->uuid) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                        Update
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="text-center py-4">
                                    <i class="bi bi-inbox fs-4"></i>
                                    <br>
                                    Belum ada data Audit Tahapan.
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
                <form method="GET" action="{{ route('tahapan.exportPdfAudit') }}">
                    <div class="d-flex flex-wrap align-items-end gap-2 mt-4 pt-3 border-top">
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
