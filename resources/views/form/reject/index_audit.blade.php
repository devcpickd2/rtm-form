
@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-clipboard-check"></i>
                Audit Monitoring False Rejection
            </h4>
            <small class="text-muted">
                Riwayat snapshot Monitoring False Rejection (read-only).
            </small>
        </div>

        @if ((int) auth()->user()->type_user !== 5)
            <a href="{{ route('reject.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        @endif
    </div>

    {{-- FILTER --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET"
                  action="{{ route('reject.audit') }}"
                  class="d-flex flex-wrap align-items-end gap-2">

                <div>
                    <label class="form-label">Pencarian</label>
                    <input type="text"
                           name="search"
                           class="form-control form-control-sm"
                           placeholder="Produk, kode produksi, mesin..."
                           value="{{ $search }}">
                </div>

                <div>
                    <label class="form-label">Tanggal</label>
                    <input type="date"
                           name="date"
                           class="form-control form-control-sm"
                           value="{{ $date }}">
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="{{ route('reject.audit') }}"
                   class="btn btn-secondary btn-sm">
                    Reset
                </a>
            </form>
        </div>
    </div>

    {{-- TABEL AUDIT --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Mesin</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Jumlah Tidak Lolos</th>
                            <th>Jumlah Kontaminan</th>
                            <th>False Rejection</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Status SPV</th>
                            <th>Snapshot</th>
                            <th>Detail</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $row)
                            @php
                                $detailId = 'detail-reject-' . $row->uuid;

                                $namaMesin = match ((string) $row->nama_mesin) {
                                    '1' => 'X-Ray',
                                    '2' => 'Metal Detector',
                                    default => '-',
                                };
                            @endphp

                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $loop->index }}
                                </td>

                                <td class="text-center">
                                    {{ $row->date ? $row->date->format('d-m-Y') : '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $row->shift ?? '-' }}
                                </td>

                                <td class="text-center">
                                    <span class="badge {{ $row->nama_mesin == 1 ? 'bg-info' : 'bg-warning text-dark' }}">
                                        {{ $namaMesin }}
                                    </span>
                                </td>

                                <td>{{ $row->nama_produk ?? '-' }}</td>
                                <td>{{ $row->kode_produksi ?? '-' }}</td>

                                <td class="text-center">
                                    {{ $row->jumlah_tidak_lolos ?? '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $row->jumlah_kontaminan ?? '-' }}
                                </td>

                                <td class="text-center">
                                    {{ $row->false_rejection ?? '-' }}
                                </td>

                                <td>{{ $row->username ?? '-' }}</td>
                                <td>{{ $row->nama_produksi ?? '-' }}</td>
                                <td>{{ $row->nama_spv ?? '-' }}</td>

                                <td class="text-center">
                                    @if ($row->status_spv == 1)
                                        <span class="badge bg-success">Verified</span>
                                    @elseif ($row->status_spv == 2)
                                        <span class="badge bg-danger">Revision</span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    {{ $row->created_at?->format('d-m-Y H:i:s') ?? '-' }}
                                </td>

                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-primary btn-sm"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#{{ $detailId }}">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>

                            {{-- DETAIL SNAPSHOT --}}
                            <tr class="collapse" id="{{ $detailId }}">
                                <td colspan="15">
                                    <div class="p-3 bg-light rounded">

                                        <h6 class="fw-bold mb-3">
                                            Detail Monitoring False Rejection
                                        </h6>

                                        <div class="row g-3">

                                            <div class="col-md-3">
                                                <strong>Tanggal:</strong><br>
                                                {{ $row->date?->format('d-m-Y') ?? '-' }}
                                            </div>

                                            <div class="col-md-3">
                                                <strong>Shift:</strong><br>
                                                {{ $row->shift ?? '-' }}
                                            </div>

                                            <div class="col-md-3">
                                                <strong>Nama Mesin:</strong><br>
                                                {{ $namaMesin }}
                                            </div>

                                            <div class="col-md-3">
                                                <strong>Source ID:</strong><br>
                                                {{ $row->source_id ?? '-' }}
                                            </div>

                                            <div class="col-md-6">
                                                <strong>Nama Produk:</strong><br>
                                                {{ $row->nama_produk ?? '-' }}
                                            </div>

                                            <div class="col-md-6">
                                                <strong>Kode Produksi:</strong><br>
                                                {{ $row->kode_produksi ?? '-' }}
                                            </div>

                                            <div class="col-md-6">
                                                <strong>Jumlah Pack/Tray yang Tidak Lolos:</strong><br>
                                                {{ $row->jumlah_tidak_lolos ?? '-' }}
                                            </div>

                                            <div class="col-md-6">
                                                <strong>Jumlah Pack/Tray yang Terdapat Kontaminan:</strong><br>
                                                {{ $row->jumlah_kontaminan ?? '-' }}
                                            </div>

                                            <div class="col-md-6">
                                                <strong>Jenis Kontaminan:</strong><br>
                                                {{ $row->jenis_kontaminan ?? '-' }}
                                            </div>

                                            <div class="col-md-6">
                                                <strong>Posisi Kontaminan:</strong><br>
                                                {{ $row->posisi_kontaminan ?? '-' }}
                                            </div>

                                            <div class="col-md-6">
                                                <strong>False Rejection:</strong><br>
                                                {{ $row->false_rejection ?? '-' }}
                                                /
                                                {{ $row->jumlah_tidak_lolos ?? '-' }}
                                            </div>

                                            <div class="col-md-6">
                                                <strong>Catatan:</strong><br>
                                                {{ $row->catatan ?? '-' }}
                                            </div>

                                        </div>

                                        <hr>

                                        <h6 class="fw-bold mb-3">
                                            Informasi Verifikasi
                                        </h6>

                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <strong>Dibuat Oleh (QC):</strong><br>
                                                {{ $row->username ?? '-' }}
                                            </div>

                                            <div class="col-md-3">
                                                <strong>Diupdate Oleh:</strong><br>
                                                {{ $row->username_updated ?? '-' }}
                                            </div>

                                            <div class="col-md-3">
                                                <strong>Produksi:</strong><br>
                                                {{ $row->nama_produksi ?? '-' }}
                                            </div>

                                            <div class="col-md-3">
                                                <strong>Supervisor:</strong><br>
                                                {{ $row->nama_spv ?? '-' }}
                                            </div>

                                            <div class="col-md-6">
                                                <strong>Catatan SPV:</strong><br>
                                                {{ $row->catatan_spv ?? '-' }}
                                            </div>

                                            <div class="col-md-6">
                                                <strong>Status SPV:</strong><br>
                                                @if ($row->status_spv == 1)
                                                    <span class="badge bg-success">Verified</span>
                                                @elseif ($row->status_spv == 2)
                                                    <span class="badge bg-danger">Revision</span>
                                                @else
                                                    <span class="badge bg-secondary">Pending</span>
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                <strong>Waktu Update Produksi:</strong><br>
                                                {{ $row->tgl_update_produksi ? \Carbon\Carbon::parse($row->tgl_update_produksi)->format('d-m-Y H:i:s') : '-' }}
                                            </div>

                                            <div class="col-md-6">
                                                <strong>Waktu Verifikasi SPV:</strong><br>
                                                {{ $row->tgl_update_spv ? \Carbon\Carbon::parse($row->tgl_update_spv)->format('d-m-Y H:i:s') : '-' }}
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <span class="badge bg-dark">
                                                Snapshot Audit
                                            </span>

                                            <small class="text-muted ms-2">
                                                Source UUID: {{ $row->source_uuid ?? '-' }}
                                            </small>
                                        </div>

                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="15" class="text-center py-4">
                                    Belum ada data Audit Monitoring False Rejection.
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
