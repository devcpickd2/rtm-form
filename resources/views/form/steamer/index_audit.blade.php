
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
                        Data Pemeriksaan Pemasakan dengan Steamer
                        <span class="badge audit-badge">MODE AUDIT</span>
                    </h3>
                </div>

                {{-- BREADCRUMB --}}
                <div class="audit-breadcrumb mb-3">
                    <a href="{{ route('steamer.index') }}">
                        <i class="bi bi-arrow-left me-1"></i>
                        Data Pemeriksaan Pemasakan dengan Steamer
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
                            Data Pemeriksaan Pemasakan dengan Steamer merupakan snapshot atau salinan kondisi
                            data saat pertama kali disimpan. Data ini digunakan untuk pemeriksaan dan audit
                            serta terpisah dari data operasional asli.
                        </div>
                    </div>
                </div>

                {{-- FILTER --}}
                <form method="GET" action="{{ route('steamer.audit') }}"
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
                            placeholder="Cari produk, catatan, atau username..."
                            value="{{ request('search') }}">
                    </div>

                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>

                    @if (request()->filled('date') || request()->filled('search'))
                        <a href="{{ route('steamer.audit') }}" class="btn btn-outline-secondary">
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
                                <th>Date | Shift</th>
                                <th>Nama Produk</th>
                                <th>Steaming</th>
                                <th>Catatan</th>
                                <th>QC</th>
                                <th>Produksi</th>
                                <th>SPV</th>
                                <th>Catatan SPV</th>
                                <th>Waktu Snapshot</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($data as $item)
                                @php
                                    $steamingRows = json_decode($item->steaming ?? '[]', true);
                                    $catatanRows = json_decode($item->catatan ?? '[]', true);

                                    $steamingRows = is_array($steamingRows) ? $steamingRows : [];
                                    $catatanRows = is_array($catatanRows) ? $catatanRows : [];

                                    $fieldLabels = [
                                        'kode_produksi' => 'Kode Produksi',
                                        'suhu_rm' => 'T. Raw Material (°C)',
                                        'jumlah_tray' => 'Jumlah Tray',
                                        'suhu_ruang' => 'T. Ruang (°C)',
                                        'suhu_produk' => 'T. Produk (°C)',
                                        'suhu_after' => 'T. Produk 1 Menit (°C)',
                                        'waktu' => 'Waktu (Menit)',
                                        'keterangan' => 'Keterangan',
                                        'jam_mulai' => 'Jam Mulai',
                                        'jam_selesai' => 'Jam Selesai',
                                        'kematangan' => 'Kematangan',
                                        'rasa' => 'Rasa',
                                        'aroma' => 'Aroma',
                                        'tekstur' => 'Tekstur',
                                        'warna' => 'Warna',
                                    ];
                                @endphp

                                <tr>
                                    <td class="text-center">
                                        {{ $data->firstItem() + $loop->index }}
                                    </td>

                                    <td class="text-center">
                                        {{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d-m-Y') : '-' }}
                                        | Shift: {{ $item->shift ?? '-' }}
                                    </td>

                                    <td>{{ $item->nama_produk ?? '-' }}</td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#steamingAudit{{ $item->uuid }}">
                                            <i class="bi bi-eye me-1"></i>
                                            Lihat {{ count($steamingRows) }} Pemeriksaan
                                        </button>
                                    </td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#catatanAudit{{ $item->uuid }}">
                                            <i class="bi bi-journal-text me-1"></i>
                                            Lihat {{ count($catatanRows) }} Catatan
                                        </button>
                                    </td>

                                    <td>{{ $item->username ?? '-' }}</td>
                                    <td>{{ $item->nama_produksi ?? '-' }}</td>

                                    <td class="text-center">
                                        @if ($item->status_spv == 1)
                                            <span class="badge bg-success">Verified</span>
                                        @elseif ($item->status_spv == 2)
                                            <span class="badge bg-danger">Revision</span>
                                        @else
                                            <span class="badge bg-secondary">Created</span>
                                        @endif
                                    </td>

                                    <td>{{ $item->catatan_spv ?? '-' }}</td>

                                    <td class="text-center">
                                        {{ $item->created_at ? $item->created_at->format('d-m-Y H:i:s') : '-' }}
                                    </td>

                                    <td class="text-center">
                                        @if ($type_user != 5)
                                            <a href="{{ route('steamer.edit', $item->uuid) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil"></i> Update
                                            </a>
                                        @else
                                            <span class="text-muted">Read Only</span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- DETAIL STEAMING --}}
                                <tr class="collapse" id="steamingAudit{{ $item->uuid }}">
                                    <td colspan="11">
                                        <div class="p-3">
                                            <h6 class="fw-bold mb-3">
                                                <i class="bi bi-clipboard-data me-1"></i>
                                                Detail Pemeriksaan Steaming
                                            </h6>

                                            @if (count($steamingRows))
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered align-middle">
                                                        <thead class="table-light text-center">
                                                            <tr>
                                                                <th>Field</th>
                                                                @foreach ($steamingRows as $index => $row)
                                                                    <th>
                                                                        Pemeriksaan {{ $index + 1 }}
                                                                    </th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($fieldLabels as $field => $label)
                                                                <tr>
                                                                    <th>{{ $label }}</th>

                                                                    @foreach ($steamingRows as $row)
                                                                        <td class="text-center">
                                                                            @if (in_array($field, ['kematangan', 'rasa', 'aroma', 'tekstur', 'warna']))
                                                                                {{ data_get($row, 'sensori.' . $field, '-') ?: '-' }}
                                                                            @else
                                                                                {{ $row[$field] ?? '-' }}
                                                                            @endif
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="text-muted">
                                                    Tidak ada detail pemeriksaan.
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                {{-- DETAIL CATATAN --}}
                                <tr class="collapse" id="catatanAudit{{ $item->uuid }}">
                                    <td colspan="11">
                                        <div class="p-3">
                                            <h6 class="fw-bold mb-3">
                                                <i class="bi bi-journal-text me-1"></i>
                                                Catatan Penggunaan Steamer
                                            </h6>

                                            @if (count($catatanRows))
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered align-middle">
                                                        <thead class="table-light text-center">
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Pukul Awal</th>
                                                                <th>Pukul Akhir</th>
                                                                <th>Catatan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($catatanRows as $catatan)
                                                                <tr>
                                                                    <td class="text-center">
                                                                        {{ $loop->iteration }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $catatan['pukul_awal'] ?? '-' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $catatan['pukul_akhir'] ?? '-' }}
                                                                    </td>
                                                                    <td>{{ $catatan['isi'] ?? '-' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="text-muted">
                                                    Tidak ada catatan.
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        <i class="bi bi-inbox fs-3 d-block text-muted mb-2"></i>
                                        Belum ada data Audit Steamer.
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
