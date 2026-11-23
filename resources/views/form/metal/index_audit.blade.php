
@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-clipboard-check"></i> Audit Metal Detector
            </h4>
            <small class="text-muted">
                Riwayat snapshot pemeriksaan Metal Detector (read-only).
            </small>
        </div>

        @if ((int) auth()->user()->type_user !== 5)
            <a href="{{ route('metal.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        @endif
    </div>

    {{-- FILTER --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET"
                  action="{{ route('metal.audit') }}"
                  class="d-flex flex-wrap align-items-end gap-2">

                <div>
                    <label class="form-label">Pencarian</label>
                    <input type="text"
                           name="search"
                           class="form-control form-control-sm"
                           placeholder="Produk, kode produksi, no. program..."
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

                <a href="{{ route('metal.audit') }}"
                   class="btn btn-secondary btn-sm">
                    Reset
                </a>
            </form>
        </div>
    </div>

    {{-- DATA AUDIT --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>No. Program</th>
                            <th>Jumlah Pemeriksaan</th>
                            <th>QC</th>
                            <th>Produksi</th>
                            <th>SPV</th>
                            <th>Status SPV</th>
                            <th>Waktu Snapshot</th>
                            <th>Detail</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $row)
                            @php
                                // Cast array dapat menghasilkan string JSON
                                // jika data lama tersimpan dalam bentuk double-encoded JSON.
                                $pemeriksaan = $row->pemeriksaan ?? [];

                                while (is_string($pemeriksaan)) {
                                    $decoded = json_decode($pemeriksaan, true);

                                    if (!is_array($decoded)) {
                                        $pemeriksaan = [];
                                        break;
                                    }

                                    $pemeriksaan = $decoded;
                                }

                                if (!is_array($pemeriksaan)) {
                                    $pemeriksaan = [];
                                }

                                $detailId = 'detail-metal-' . $row->uuid;
                            @endphp

                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $loop->index }}
                                </td>

                                <td class="text-center">
                                    {{ $row->date ? \Carbon\Carbon::parse($row->date)->format('d-m-Y') : '-' }}
                                </td>

                                <td class="text-center">{{ $row->shift ?? '-' }}</td>
                                <td>{{ $row->nama_produk ?? '-' }}</td>
                                <td>{{ $row->kode_produksi ?? '-' }}</td>
                                <td class="text-center">{{ $row->no_program ?? '-' }}</td>

                                <td class="text-center">
                                    <span class="badge bg-info">
                                        {{ count($pemeriksaan) }} Baris
                                    </span>
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

                            {{-- DETAIL --}}
                            <tr class="collapse" id="{{ $detailId }}">
                                <td colspan="13">
                                    <div class="p-3 bg-light rounded">

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-3">
                                                <strong>Tanggal:</strong><br>
                                                {{ $row->date ? \Carbon\Carbon::parse($row->date)->format('d-m-Y') : '-' }}
                                            </div>

                                            <div class="col-md-3">
                                                <strong>Shift:</strong><br>
                                                {{ $row->shift ?? '-' }}
                                            </div>

                                            <div class="col-md-3">
                                                <strong>Produk:</strong><br>
                                                {{ $row->nama_produk ?? '-' }}
                                            </div>

                                            <div class="col-md-3">
                                                <strong>Kode Produksi:</strong><br>
                                                {{ $row->kode_produksi ?? '-' }}
                                            </div>
                                        </div>

                                        <h6 class="fw-bold mt-3">
                                            Detail Pemeriksaan Metal Detector
                                        </h6>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm bg-white align-middle">
                                                <thead class="table-primary text-center">
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Pukul</th>
                                                        <th>Fe (1.5)</th>
                                                        <th>Non Fe (2.0)</th>
                                                        <th>SUS 316 (2.5)</th>
                                                        <th>Keterangan</th>
                                                        <th>Tindakan Koreksi</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @forelse ($pemeriksaan as $item)
                                                        <tr>
                                                            <td class="text-center">
                                                                {{ $loop->iteration }}
                                                            </td>

                                                            <td class="text-center">
                                                                {{ $item['pukul'] ?? '-' }}
                                                            </td>

                                                            <td class="text-center">
                                                                @if (($item['fe'] ?? '') === 'Oke')
                                                                    <span class="badge bg-success">Oke</span>
                                                                @else
                                                                    <span class="badge bg-danger">Tidak Oke</span>
                                                                @endif
                                                            </td>

                                                            <td class="text-center">
                                                                @if (($item['non_fe'] ?? '') === 'Oke')
                                                                    <span class="badge bg-success">Oke</span>
                                                                @else
                                                                    <span class="badge bg-danger">Tidak Oke</span>
                                                                @endif
                                                            </td>

                                                            <td class="text-center">
                                                                @if (($item['sus_316'] ?? '') === 'Oke')
                                                                    <span class="badge bg-success">Oke</span>
                                                                @else
                                                                    <span class="badge bg-danger">Tidak Oke</span>
                                                                @endif
                                                            </td>

                                                            <td class="text-center">
                                                                @if (($item['keterangan'] ?? '') === 'Terdeteksi')
                                                                    <span class="badge bg-danger">Terdeteksi</span>
                                                                @elseif (($item['keterangan'] ?? '') === 'Tidak Terdeteksi')
                                                                    <span class="badge bg-success">Tidak Terdeteksi</span>
                                                                @else
                                                                    {{ $item['keterangan'] ?? '-' }}
                                                                @endif
                                                            </td>

                                                            <td>
                                                                {{ $item['tindakan_koreksi'] ?? '-' }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center">
                                                                Tidak ada detail pemeriksaan.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="row g-3 mt-2">
                                            <div class="col-md-6">
                                                <strong>Catatan QC:</strong><br>
                                                {{ $row->catatan ?? '-' }}
                                            </div>

                                            <div class="col-md-6">
                                                <strong>Catatan SPV:</strong><br>
                                                {{ $row->catatan_spv ?? '-' }}
                                            </div>
                                        </div>

                                        <hr>

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
                                                <strong>Update Produksi:</strong><br>
                                                {{ $row->tgl_update_produksi ? \Carbon\Carbon::parse($row->tgl_update_produksi)->format('d-m-Y H:i') : '-' }}
                                            </div>

                                            <div class="col-md-6">
                                                <strong>Verifikasi SPV:</strong><br>
                                                {{ $row->tgl_update_spv ? \Carbon\Carbon::parse($row->tgl_update_spv)->format('d-m-Y H:i') : '-' }}
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
                                <td colspan="13" class="text-center py-4">
                                    Belum ada data Audit Metal Detector.
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
