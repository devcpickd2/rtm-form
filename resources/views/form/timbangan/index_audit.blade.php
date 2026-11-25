@extends('layouts.app')

@section('content')
    <div class="container-fluid py-0">
        <div class="card shadow-sm">
            <div class="card-body">

                {{-- JUDUL --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="mb-0">
                        <i class="bi bi-list-check"></i>
                        Data Peneraan Timbangan
                        <span class="badge audit-badge">MODE AUDIT</span>
                    </h3>
                </div>

                {{-- BREADCRUMB --}}
                <div class="audit-breadcrumb mb-3">
                    <a href="{{ route('timbangan.index') }}">
                        <i class="bi bi-arrow-left me-1"></i>
                        Data Peneraan Timbangan
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
                            Data Peneraan Timbangan yang ditampilkan merupakan salinan
                            data saat pertama kali disimpan. Data Audit digunakan untuk
                            kebutuhan pemeriksaan dan tidak mengikuti perubahan pada
                            data operasional setelah snapshot dibuat.
                        </div>
                    </div>
                </div>

                {{-- FILTER --}}
                <form method="GET" action="{{ route('timbangan.audit') }}"
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
                            value="{{ request('search') }}" placeholder="Cari kode timbangan / QC / catatan...">
                    </div>

                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search me-1"></i>
                        Cari
                    </button>

                    @if (request('date') || request('search'))
                        <a href="{{ route('timbangan.audit') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset
                        </a>
                    @endif
                </form>

                @php
                    $decodeAuditArray = function ($value) {
                        if (is_array($value)) {
                            return $value;
                        }

                        if (is_string($value)) {
                            $decoded = json_decode($value, true);
                            return is_array($decoded) ? $decoded : [];
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
                                <th>Kode Timbangan</th>
                                <th>Standar (gr)</th>
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
                            @forelse ($data as $dep)
                                @php
                                    $kodeTimbangan = $decodeAuditArray($dep->kode_timbangan);
                                    $standar = $decodeAuditArray($dep->standar);
                                    $waktuTera = $decodeAuditArray($dep->waktu_tera);
                                    $hasilTera = $decodeAuditArray($dep->hasil_tera);

                                    // Mengikuti nama kolom pada halaman utama.
                                    $tindakanPerbaikan = $decodeAuditArray($dep->tindakan_koreksi);

                                    $jumlahBaris = max(
                                        count($kodeTimbangan),
                                        count($standar),
                                        count($waktuTera),
                                        count($hasilTera),
                                        count($tindakanPerbaikan),
                                        1,
                                    );
                                @endphp

                                @for ($i = 0; $i < $jumlahBaris; $i++)
                                    <tr>
                                        @if ($i === 0)
                                            <td rowspan="{{ $jumlahBaris }}" class="text-center align-middle">
                                                {{ $data->firstItem() + $loop->parent->index }}
                                            </td>

                                            <td rowspan="{{ $jumlahBaris }}" class="text-center align-middle">
                                                {{ $dep->date ? \Carbon\Carbon::parse($dep->date)->format('d-m-Y') : '-' }}
                                                <br>
                                                Shift: {{ $dep->shift ?? '-' }}
                                            </td>
                                        @endif

                                        <td class="text-center align-middle">
                                            {{ $kodeTimbangan[$i] ?? '-' }}
                                        </td>

                                        <td class="text-center align-middle">
                                            {{ $standar[$i] ?? '-' }}
                                        </td>

                                        <td class="text-center align-middle">
                                            {{ $waktuTera[$i] ?? '-' }}
                                        </td>

                                        <td class="text-center align-middle">
                                            {{ $hasilTera[$i] ?? '-' }}
                                        </td>

                                        <td class="align-middle">
                                            {{ $tindakanPerbaikan[$i] ?? '-' }}
                                        </td>

                                        @if ($i === 0)
                                            {{-- QC --}}
                                            <td rowspan="{{ $jumlahBaris }}" class="text-center align-middle">
                                                {{ $dep->username ?? '-' }}
                                            </td>

                                            {{-- PRODUKSI --}}
                                            <td rowspan="{{ $jumlahBaris }}" class="text-center align-middle">
                                                {{ $dep->nama_produksi ?? '-' }}

                                                <hr class="my-1">

                                                Status:
                                                @if ($dep->status_produksi == 1)
                                                    <span class="badge bg-success">Selesai</span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        {{ $dep->status_produksi ?? '-' }}
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- SPV --}}
                                            <td rowspan="{{ $jumlahBaris }}" class="text-center align-middle">
                                                {{ $dep->nama_spv ?? '-' }}

                                                <hr class="my-1">

                                                @if ($dep->status_spv == 1)
                                                    <span class="badge bg-success">Verified</span>
                                                @elseif ($dep->status_spv == 2)
                                                    <span class="badge bg-danger">Revision</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @endif

                                                @if (!empty($dep->catatan_spv))
                                                    <hr class="my-1">
                                                    <small class="text-muted">
                                                        {{ $dep->catatan_spv }}
                                                    </small>
                                                @endif
                                            </td>

                                            {{-- ACTION --}}
                                            <td rowspan="{{ $jumlahBaris }}" class="text-center align-middle">
                                                <a href="{{ route('timbangan.edit', $dep->uuid) }}"
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
                                    <td colspan="11" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size:1.5rem;"></i>
                                        <br>
                                        Belum ada data Audit Peneraan Timbangan.
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

                <form method="GET" action="{{ route('timbangan.exportPdfAudit') }}">

                    <div class="d-flex align-items-end gap-2">

                        <!-- Pilih Tanggal -->
                        <div class="col-auto">
                            <label for="date" class="col-form-label fw-semibold">Pilih Tanggal</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" id="date" name="date" class="form-control form-control-sm"
                                value="{{ request('date') }}" required>
                        </div>

                        <!-- Pilih Shift -->
                        <div class="col-auto">
                            <label for="shift" class="col-form-label fw-semibold">Pilih Shift</label>
                        </div>
                        <div class="col-auto">
                            <select id="shift" name="shift" class="form-control form-control-sm" required>
                                <option value="">-- Pilih Shift --</option>
                                <option value="1" {{ request('shift') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ request('shift') == '2' ? 'selected' : '' }}>2</option>
                                <option value="3" {{ request('shift') == '3' ? 'selected' : '' }}>3</option>
                            </select>
                        </div>

                        <!-- Button Export PDF -->
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
