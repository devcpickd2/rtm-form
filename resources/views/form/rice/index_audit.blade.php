
@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3>
                        <i class="bi bi-eye"></i>
                        Audit Pemeriksaan Pemasakan dengan Rice Cooker
                    </h3>
                    <small class="text-muted">
                        Data snapshot Audit - hanya dapat dilihat
                    </small>
                </div>

                <a href="{{ route('rice.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <form method="GET" action="{{ route('rice.audit') }}"
                class="d-flex flex-wrap align-items-end gap-2 mb-3 p-2 border rounded bg-light">

                <div style="min-width:220px;">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="date" class="form-control"
                        value="{{ $date }}">
                </div>

                <div style="min-width:260px;">
                    <label class="form-label">Pencarian</label>
                    <input type="text" name="search" class="form-control"
                        value="{{ $search }}"
                        placeholder="Nama produk, username, pemeriksaan...">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="{{ route('rice.audit') }}" class="btn btn-secondary">
                    Reset
                </a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Nama Produk</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>Status Produksi</th>
                            <th>SPV</th>
                            <th>Status SPV</th>
                            <th>Catatan SPV</th>
                            <th>Waktu Snapshot</th>
                            <th>Detail</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($data as $index => $row)
                            @php
                                $cookerRows = is_array($row->cooker)
                                    ? $row->cooker
                                    : (json_decode($row->cooker ?? '[]', true) ?: []);

                                $catatanRows = is_array($row->catatan)
                                    ? $row->catatan
                                    : (json_decode($row->catatan ?? '[]', true) ?: []);

                                if (!is_array($catatanRows)) {
                                    $catatanRows = [];
                                }
                            @endphp

                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $index }}
                                </td>

                                <td>
                                    {{ $row->date?->format('d-m-Y') ?? '-' }}
                                </td>

                                <td class="text-center">{{ $row->shift ?? '-' }}</td>
                                <td>{{ $row->nama_produk ?? '-' }}</td>
                                <td>{{ $row->username ?? '-' }}</td>
                                <td>{{ $row->nama_produksi ?? '-' }}</td>

                                <td class="text-center">
                                    @if($row->status_produksi == 1)
                                        <span class="badge bg-success">Tersimpan</span>
                                    @elseif($row->status_produksi == 2)
                                        <span class="badge bg-warning text-dark">Revisi</span>
                                    @else
                                        <span class="badge bg-secondary">
                                            {{ $row->status_produksi ?? '-' }}
                                        </span>
                                    @endif
                                </td>

                                <td>{{ $row->nama_spv ?? '-' }}</td>

                                <td class="text-center">
                                    @if($row->status_spv == 1)
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif($row->status_spv == 2)
                                        <span class="badge bg-danger">Revisi</span>
                                    @else
                                        <span class="badge bg-secondary">Menunggu</span>
                                    @endif
                                </td>

                                <td>{{ $row->catatan_spv ?? '-' }}</td>

                                <td>
                                    {{ $row->created_at?->format('d-m-Y H:i:s') ?? '-' }}
                                </td>

                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#detailRice{{ $row->uuid }}">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>

                            <tr class="collapse" id="detailRice{{ $row->uuid }}">
                                <td colspan="12">
                                    <div class="card card-body bg-light">

                                        <h5 class="fw-bold mb-3">
                                            Detail Pemeriksaan Rice Cooker
                                        </h5>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <strong>UUID Data Asli:</strong>
                                                <div>{{ $row->source_uuid ?? '-' }}</div>
                                            </div>

                                            <div class="col-md-4">
                                                <strong>Username Update:</strong>
                                                <div>{{ $row->username_updated ?? '-' }}</div>
                                            </div>

                                            <div class="col-md-4">
                                                <strong>Waktu Update Produksi:</strong>
                                                <div>
                                                    {{ $row->tgl_update_produksi?->format('d-m-Y H:i:s') ?? '-' }}
                                                </div>
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <strong>Waktu Verifikasi SPV:</strong>
                                                <div>
                                                    {{ $row->tgl_update_spv?->format('d-m-Y H:i:s') ?? '-' }}
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="fw-bold">Pemeriksaan Cooker</h6>

                                        <div class="table-responsive mb-3">
                                            <table class="table table-sm table-bordered bg-white">
                                                <thead class="table-secondary">
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Nama Pemeriksaan</th>
                                                        <th>Hasil</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($cookerRows as $cookerIndex => $cooker)
                                                        <tr>
                                                            <td>{{ $cookerIndex + 1 }}</td>
                                                            <td colspan="2">
                                                                <div class="fw-bold mb-2">
                                                                    Pemeriksaan {{ $cookerIndex + 1 }}
                                                                </div>

                                                                <table class="table table-sm table-bordered mb-0">
                                                                    @foreach($cooker as $field => $value)
                                                                        <tr>
                                                                            <th style="width:30%">
                                                                                {{ ucwords(str_replace('_', ' ', $field)) }}
                                                                            </th>
                                                                            <td>
                                                                                @if(is_array($value))
                                                                                    @foreach($value as $subField => $subValue)
                                                                                        <div>
                                                                                            <strong>{{ ucwords(str_replace('_', ' ', $subField)) }}:</strong>
                                                                                            @if(is_array($subValue))
                                                                                                {{ json_encode($subValue, JSON_UNESCAPED_UNICODE) }}
                                                                                            @else
                                                                                                {{ $subValue ?: '-' }}
                                                                                            @endif
                                                                                        </div>
                                                                                    @endforeach
                                                                                @else
                                                                                    {{ $value !== '' && $value !== null ? $value : '-' }}
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center">
                                                                Tidak ada detail pemeriksaan.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <h6 class="fw-bold">Catatan Pemeriksaan</h6>

                                        <div class="table-responsive mb-3">
                                            <table class="table table-sm table-bordered bg-white">
                                                <thead class="table-secondary">
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Pukul Awal</th>
                                                        <th>Pukul Akhir</th>
                                                        <th>Catatan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($catatanRows as $catatanIndex => $catatan)
                                                        <tr>
                                                            <td>{{ $catatanIndex + 1 }}</td>
                                                            <td>{{ $catatan['pukul_awal'] ?? '-' }}</td>
                                                            <td>{{ $catatan['pukul_akhir'] ?? '-' }}</td>
                                                            <td>{{ $catatan['isi'] ?? '-' }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center">
                                                                Tidak ada catatan pemeriksaan.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="alert alert-info mb-0">
                                            <i class="bi bi-info-circle"></i>
                                            Data ini merupakan snapshot Audit dan tidak dapat diubah.
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center">
                                    Belum ada data Audit Rice Cooker.
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
@endsection
