
@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">
    <div class="card shadow-sm">
        <div class="card-body">

            {{-- JUDUL --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-list-check"></i>
                    Data Peneraan Thermometer
                    <span class="badge audit-badge">MODE AUDIT</span>
                </h3>
            </div>

            {{-- BREADCRUMB --}}
            <div class="audit-breadcrumb mb-3">
                <a href="{{ route('thermometer.index') }}">
                    <i class="bi bi-arrow-left me-1"></i>
                    Data Peneraan Thermometer
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
                        Data Peneraan Thermometer merupakan salinan data saat pertama
                        kali disimpan untuk kebutuhan pemeriksaan dan audit.
                        Data Audit tidak memengaruhi data operasional asli.
                    </div>
                </div>
            </div>

            {{-- FILTER --}}
            <form method="GET"
                action="{{ route('thermometer.audit') }}"
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
                        value="{{ request('search') }}"
                        placeholder="Cari kode, area, QC, atau catatan...">
                </div>

                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-search me-1"></i>
                    Cari
                </button>

                @if (request('date') || request('search'))
                    <a href="{{ route('thermometer.audit') }}"
                        class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset
                    </a>
                @endif
            </form>

            @php
                $toArray = function ($value) {
                    if (is_array($value)) {
                        return $value;
                    }

                    if (is_string($value)) {
                        $decoded = json_decode($value, true);

                        if (is_array($decoded)) {
                            return $decoded;
                        }
                    }

                    return [];
                };
            @endphp

            {{-- TABEL AUDIT --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th rowspan="2">NO.</th>
                            <th rowspan="2">Date | Shift</th>
                            <th>Kode Thermometer</th>
                            <th>Area</th>
                            <th>Standar (0.0°C)</th>
                            <th>Pukul</th>
                            <th>Hasil Tera</th>
                            <th>Tindakan Perbaikan</th>
                            <th rowspan="2">QC</th>
                            <th rowspan="2">Produksi</th>
                            <th rowspan="2">SPV</th>
                            <th rowspan="2">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $index => $dep)
                            @php
                                $kode = $toArray($dep->kode_thermometer);
                                $area = $toArray($dep->area);
                                $standar = $toArray($dep->standar);
                                $waktu = $toArray($dep->waktu_tera);
                                $hasil = $toArray($dep->hasil_tera);
                                $tindakan = $toArray($dep->tindakan_koreksi);

                                $jumlah = max(
                                    count($kode),
                                    count($area),
                                    count($standar),
                                    count($waktu),
                                    count($hasil),
                                    count($tindakan),
                                    1
                                );

                                $nomor = $data->firstItem() + $index;
                            @endphp

                            @for ($i = 0; $i < $jumlah; $i++)
                                <tr data-group="{{ $dep->uuid }}">
                                    @if ($i === 0)
                                        <td rowspan="{{ $jumlah }}"
                                            class="text-center align-middle">
                                            {{ $nomor }}
                                        </td>

                                        <td rowspan="{{ $jumlah }}"
                                            class="text-center align-middle">
                                            {{ $dep->date
                                                ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y')
                                                : '-' }}
                                            <br>
                                            Shift: {{ $dep->shift ?? '-' }}
                                        </td>
                                    @endif

                                    <td class="text-center align-middle">
                                        {{ $kode[$i] ?? '-' }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $area[$i] ?? '-' }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $standar[$i] ?? '0.0' }}
                                    </td>

                                    <td class="text-center align-middle">
                                        {{ $waktu[$i] ?? '-' }}
                                    </td>

                                    <td class="text-center align-middle
                                        {{ abs((float) str_replace(',', '.', $hasil[$i] ?? 0)) > 0.4 ? 'text-danger fw-bold' : '' }}">
                                        {{ $hasil[$i] ?? '-' }}
                                    </td>

                                    <td class="align-middle">
                                        {{ $tindakan[$i] ?? '-' }}
                                    </td>

                                    @if ($i === 0)
                                        {{-- QC --}}
                                        <td rowspan="{{ $jumlah }}"
                                            class="text-center align-middle">
                                            {{ $dep->username ?? '-' }}
                                        </td>

                                        {{-- PRODUKSI --}}
                                        <td rowspan="{{ $jumlah }}"
                                            class="text-center align-middle">
                                            {{ $dep->nama_produksi ?? '-' }}
                                        </td>

                                        {{-- SPV --}}
                                        <td rowspan="{{ $jumlah }}"
                                            class="text-center align-middle">
                                            @if ($dep->status_spv == 0)
                                                <span class="fw-bold text-secondary">
                                                    Created
                                                </span>
                                            @elseif ($dep->status_spv == 1)
                                                <span class="fw-bold text-success">
                                                    Verified
                                                </span>
                                            @elseif ($dep->status_spv == 2)
                                                <a href="javascript:void(0);"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#revisionModal{{ $dep->uuid }}"
                                                    class="text-danger fw-bold text-decoration-none">
                                                    Revision
                                                </a>

                                                {{-- MODAL CATATAN REVISI --}}
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
                                                <span class="text-secondary">-</span>
                                            @endif
                                        </td>

                                        {{-- ACTION --}}
                                        <td rowspan="{{ $jumlah }}"
                                            class="text-center align-middle">
                                            <a href="{{ route('thermometer.edit', $dep->uuid) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil"></i>
                                                Update
                                            </a>
                                        </td>
                                    @endif
                                </tr>
                            @endfor

                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox" style="font-size:1.5rem;"></i>
                                    <br>
                                    Belum ada data Audit Thermometer.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- JUMLAH DATA --}}
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

            {{-- EXPORT PDF --}}
            <div class="mt-4 pt-3 border-top">
                <form method="GET" action="{{ route('thermometer.exportPdfAudit') }}">
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

                        <div>
                            <label for="export_shift" class="form-label fw-semibold">
                                Pilih Shift
                            </label>
                            <select id="export_shift"
                                name="shift"
                                class="form-select form-select-sm"
                                required>
                                <option value="">-- Pilih Shift --</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-file-earmark-pdf"></i>
                            Export PDF
                        </button>
                    </div>
                </form>
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
