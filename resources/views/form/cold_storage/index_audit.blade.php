@extends('layouts.app')

@section('content')
    <div class="container-fluid py-0">

        <div class="audit-breadcrumb mb-3">
            <strong>
                <i class="bi bi-clipboard-check me-1"></i>
                Pemantauan Suhu Produk di Cold Storage / Data Audit
            </strong>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h3 class="mb-0">
                        <i class="bi bi-thermometer-half"></i>
                        Data Pemantauan Suhu Produk di Cold Storage
                        <span class="audit-badge">AUDIT</span>
                    </h3>
                </div>

                <div class="audit-note mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Data ini merupakan salinan Audit dan hanya dapat dilihat.
                    Perubahan pada data operasional tidak mengubah salinan Audit.
                </div>

                <form method="GET" action="{{ route('cold_storage.audit') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light shadow-sm">

                    <div class="input-group" style="max-width:220px">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-calendar-date text-muted"></i>
                        </span>
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>

                    <div class="input-group" style="max-width:350px">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="Cari QC, Warehouse, suhu, catatan...">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter me-1"></i>Filter
                    </button>

                    <a href="{{ route('cold_storage.audit') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                    </a>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th>NO.</th>
                                <th>Date | Shift</th>
                                <th>Pukul</th>
                                <th>Cold Storage</th>
                                <th>Catatan</th>
                                <th>QC</th>
                                <th>Warehouse</th>
                                <th>Status Warehouse</th>
                                <th>SPV</th>
                                <th>Status SPV</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $no = ($data->currentPage() - 1) * $data->perPage() + 1;
                            @endphp

                            @forelse ($data as $dep)
                                @php
                                    $suhu_cs = is_string($dep->suhu_cs)
                                        ? json_decode($dep->suhu_cs, true)
                                        : $dep->suhu_cs ?? [];

                                    $suhu_cs = is_array($suhu_cs) ? $suhu_cs : [];
                                @endphp

                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>

                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($dep->date)->format('d-m-Y') }}
                                        | Shift: {{ $dep->shift }}
                                    </td>

                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($dep->pukul)->format('H:i') }}
                                    </td>

                                    <td class="text-center">
                                        @if (count($suhu_cs))
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal" data-bs-target="#auditSuhu{{ $dep->uuid }}">
                                                <i class="bi bi-eye"></i> Lihat Suhu Produk
                                            </button>

                                            <div class="modal fade" id="auditSuhu{{ $dep->uuid }}" tabindex="-1"
                                                aria-hidden="true">

                                                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                    <div class="modal-content">

                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title">
                                                                Detail Suhu Cold Storage (Audit)
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <div class="table-responsive">
                                                                <table
                                                                    class="table table-bordered table-sm text-center align-middle">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>No</th>
                                                                            <th>Nama Produk</th>
                                                                            <th>Kode Produksi</th>
                                                                            <th>Suhu Standar</th>
                                                                            <th>1</th>
                                                                            <th>2</th>
                                                                            <th>3</th>
                                                                            <th>4</th>
                                                                            <th>5</th>
                                                                            <th>Rata-rata</th>
                                                                            <th>Keterangan</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($suhu_cs as $i => $item)
                                                                            <tr>
                                                                                <td>{{ $i + 1 }}</td>
                                                                                <td>{{ $item['nama_produk'] ?? '-' }}</td>
                                                                                <td>{{ $item['kode_produksi'] ?? '-' }}
                                                                                </td>
                                                                                <td>{{ $item['suhu_standar'] ?? '-' }}</td>
                                                                                <td>{{ $item['cek_1'] ?? '-' }}</td>
                                                                                <td>{{ $item['cek_2'] ?? '-' }}</td>
                                                                                <td>{{ $item['cek_3'] ?? '-' }}</td>
                                                                                <td>{{ $item['cek_4'] ?? '-' }}</td>
                                                                                <td>{{ $item['cek_5'] ?? '-' }}</td>
                                                                                <td>{{ $item['rata_rata'] ?? '-' }}</td>
                                                                                <td>{{ $item['keterangan'] ?? '-' }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
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

                                    <td>{{ $dep->catatan ?: '-' }}</td>

                                    <td class="text-center">
                                        {{ $dep->username ?: '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->nama_warehouse ?: '-' }}
                                    </td>

                                    <td class="text-center">
                                        @if ($dep->status_warehouse == 1)
                                            <span class="text-success fw-bold">Diketahui</span>
                                        @else
                                            <span class="text-secondary fw-bold">-</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        {{ $dep->nama_spv ?: '-' }}
                                    </td>

                                    <td class="text-center">
                                        @if ($dep->status_spv == 1)
                                            <span class="text-success fw-bold">Verified</span>
                                        @elseif ($dep->status_spv == 2)
                                            <span class="text-danger fw-bold">Revision</span>
                                            @if ($dep->catatan_spv)
                                                <div class="small text-muted">
                                                    {{ $dep->catatan_spv }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-secondary fw-bold">Created</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">
                                        Belum ada data Audit Cold Storage.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>

    <style>
        .audit-breadcrumb {
            background: #dc3545;
            color: #fff;
            padding: 10px 14px;
            border-radius: 6px;
        }

        .audit-badge {
            display: inline-block;
            background: #7c3aed;
            color: #fff;
            font-size: 11px;
            padding: 5px 9px;
            border-radius: 5px;
            vertical-align: middle;
        }

        .audit-note {
            background: #f5f0ff;
            border-left: 4px solid #7c3aed;
            padding: 11px 14px;
            border-radius: 5px;
            color: #4c1d95;
        }

        .table td,
        .table th {
            font-size: .85rem;
            white-space: nowrap;
        }
    </style>
@endsection
