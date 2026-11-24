@extends('layouts.app')

@section('content')
    <div class="container-fluid py-0">

        <div class="card shadow-sm">
            <div class="card-body">

                {{-- JUDUL --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0 fw-bold">
                        <i class="bi bi-list-check"></i>
                        Data Pemeriksaan Pemasakan Noodle
                        <span class="badge audit-badge">MODE AUDIT</span>
                    </h3>
                </div>

                {{-- BREADCRUMB --}}
                <div class="audit-breadcrumb mb-3">
                    <a href="{{ route('noodle.index') }}">
                        <i class="bi bi-arrow-left me-1"></i>
                        Data Pemeriksaan Pemasakan Noodle
                    </a>
                    <span class="mx-2">/</span>
                    <span class="fw-bold">Data Audit</span>
                </div>

                {{-- INFORMASI AUDIT --}}
                <div class="alert audit-info d-flex align-items-start mb-4">
                    <i class="bi bi-shield-check me-3 fs-4"></i>
                    <div>
                        <strong class="d-block mb-1">Halaman Data Audit</strong>
                        <div class="small">
                            Anda sedang melihat data Pemeriksaan Pemasakan Noodle
                            yang tersimpan pada halaman Audit. Data di halaman ini
                            merupakan salinan data untuk kebutuhan pemeriksaan dan
                            audit, sehingga perubahan pada data Audit tidak
                            memengaruhi data operasional asli.
                        </div>
                    </div>
                </div>

                {{-- FILTER --}}
                <form method="GET" action="{{ route('noodle.audit') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                    <div class="input-group" style="max-width:220px;">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-calendar-date text-muted"></i>
                        </span>
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>

                    <div class="input-group" style="max-width:350px;">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="Cari nama produk, QC, atau data mixing...">
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search"></i> Cari
                    </button>

                    <a href="{{ route('noodle.audit') }}" class="btn btn-secondary btn-sm">
                        Reset
                    </a>
                </form>

                {{-- TABEL --}}
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>NO.</th>
                                <th>Date | Shift</th>
                                <th>Nama Produk</th>
                                <th>Pemasakan Noodle</th>
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
                                    <td class="text-center">{{ $no++ }}</td>

                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}
                                        | Shift: {{ $dep->shift }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->nama_produk }}
                                    </td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#mixingModal{{ $dep->uuid }}">
                                            <i class="bi bi-eye"></i> Hasil Mixing
                                        </button>

                                        {{-- DETAIL MIXING --}}
                                        <div class="modal fade" id="mixingModal{{ $dep->uuid }}" tabindex="-1"
                                            aria-hidden="true">

                                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                <div class="modal-content">

                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title">
                                                            Detail Pemeriksaan Pemasakan Noodle
                                                        </h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body text-start">

                                                        <table class="table table-bordered table-sm">
                                                            <tr>
                                                                <th width="25%">Tanggal</th>
                                                                <td>
                                                                    {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Shift</th>
                                                                <td>{{ $dep->shift }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Nama Produk</th>
                                                                <td>{{ $dep->nama_produk }}</td>
                                                            </tr>
                                                        </table>

                                                        @forelse ($dep->mixing_decoded as $index => $item)
                                                            <div class="card mb-3">
                                                                <div class="card-header bg-primary text-white">
                                                                    Detail Mixing {{ $index + 1 }}
                                                                </div>

                                                                <div class="card-body">
                                                                    <table class="table table-bordered table-sm">
                                                                        <tr>
                                                                            <th width="30%">Nama Produk</th>
                                                                            <td>{{ $item['nama_produk'] ?? '-' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Kode Produksi</th>
                                                                            <td>{{ $item['kode_produksi'] ?? '-' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Bahan Utama</th>
                                                                            <td>{{ $item['bahan_utama'] ?? '-' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Kode Bahan</th>
                                                                            <td>{{ $item['kode_bahan'] ?? '-' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Berat Bahan</th>
                                                                            <td>{{ $item['berat_bahan'] ?? '-' }} Kg</td>
                                                                        </tr>
                                                                    </table>

                                                                    <h6 class="fw-bold mt-3">Detail Parameter Proses</h6>

                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered table-sm">
                                                                            <thead class="table-light">
                                                                                <tr>
                                                                                    <th>Parameter</th>
                                                                                    <th>Data</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach ($item as $key => $value)
                                                                                    @if (is_array($value))
                                                                                        <tr>
                                                                                            <td>{{ ucwords(str_replace('_', ' ', $key)) }}
                                                                                            </td>
                                                                                            <td>
                                                                                                {{ implode(', ', array_map(fn($v) => is_array($v) ? json_encode($v) : (string) $v, $value)) }}
                                                                                            </td>
                                                                                        </tr>
                                                                                        @elseif
                                                                                        (!in_array($key, ['nama_produk', 'kode_produksi', 'bahan_utama', 'kode_bahan', 'berat_bahan']))
                                                                                        <tr>
                                                                                            <td>{{ ucwords(str_replace('_', ' ', $key)) }}
                                                                                            </td>
                                                                                            <td>{{ $value ?? '-' }}</td>
                                                                                        </tr>
                                                                                    @endif
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>

                                                                    @if (!empty($item['bahan_lain']))
                                                                        <h6 class="fw-bold mt-3">Bahan Lain</h6>
                                                                        <div class="table-responsive">
                                                                            <table class="table table-bordered table-sm">
                                                                                <thead class="table-light">
                                                                                    <tr>
                                                                                        <th>Nama Bahan</th>
                                                                                        <th>Kode Bahan</th>
                                                                                        <th>Berat</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach ($item['bahan_lain'] as $bahan)
                                                                                        <tr>
                                                                                            <td>{{ $bahan['nama_bahan'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ $bahan['kode_bahan_lain'] ?? '-' }}
                                                                                            </td>
                                                                                            <td>{{ $bahan['berat_bahan'] ?? '-' }}
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="alert alert-secondary">
                                                                Belum ada data mixing.
                                                            </div>
                                                        @endforelse

                                                        {{-- CATATAN --}}
                                                        <h6 class="fw-bold mt-3">Catatan</h6>

                                                        @php
                                                            $catatan = $dep->catatan;
                                                            if (is_string($catatan)) {
                                                                $decoded = json_decode($catatan, true);
                                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                                    $catatan = $decoded;
                                                                }
                                                            }
                                                        @endphp

                                                        @if (is_array($catatan))
                                                            <table class="table table-bordered table-sm">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>Waktu Awal</th>
                                                                        <th>Waktu Akhir</th>
                                                                        <th>Catatan</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($catatan as $c)
                                                                        <tr>
                                                                            <td>{{ is_array($c) ? $c['pukul_awal'] ?? '-' : '-' }}
                                                                            </td>
                                                                            <td>{{ is_array($c) ? $c['pukul_akhir'] ?? '-' : '-' }}
                                                                            </td>
                                                                            <td>{{ is_array($c) ? $c['isi'] ?? '-' : $c }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        @else
                                                            <div class="border rounded p-2 bg-light">
                                                                {{ $catatan ?: '-' }}
                                                            </div>
                                                        @endif

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
                                    </td>

                                    <td class="text-center">{{ $dep->username ?? '-' }}</td>
                                    <td class="text-center">{{ $dep->nama_produksi ?? '-' }}</td>

                                    <td class="text-center">
                                        @if ($dep->status_spv == 0)
                                            <span class="fw-bold text-secondary">Created</span>
                                        @elseif ($dep->status_spv == 1)
                                            <span class="fw-bold text-success">Verified</span>
                                        @elseif ($dep->status_spv == 2)
                                            <span class="fw-bold text-danger">Revision</span>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- ACTION AUDIT --}}
                                    <td class="text-center">
                                        <a href="{{ route('noodle.edit', $dep->uuid) }}"
                                            class="btn btn-warning btn-sm mb-1">
                                            <i class="bi bi-pencil"></i> Update
                                        </a>

                                        <form method="GET" action="{{ route('noodle.exportPdf') }}" target="_blank"
                                            class="d-inline-block">
                                            <input type="hidden" name="uuid" value="{{ $dep->uuid }}">

                                            <button type="submit" class="btn btn-danger btn-sm mb-1">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                                Export PDF
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">
                                        Belum ada data Audit Noodle.
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
