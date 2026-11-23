
@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1">Audit Pengemasan</h4>
            <small class="text-muted">
                Data snapshot pemeriksaan Pengemasan - Checking dan Packing
            </small>
        </div>

        <a href="{{ route('pengemasan.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- FILTER --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('pengemasan.audit') }}"
                  class="d-flex flex-wrap align-items-end gap-2">

                <div>
                    <label class="form-label">Pencarian</label>
                    <input type="text"
                           name="search"
                           class="form-control form-control-sm"
                           placeholder="Produk, kode produksi, username..."
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

                <a href="{{ route('pengemasan.audit') }}"
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
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Pukul</th>
                            <th>Produk</th>
                            <th>Kode Produksi</th>
                            <th>Checking</th>
                            <th>Packing</th>
                            <th>Catatan</th>
                            <th>Dibuat Oleh</th>
                            <th>Status SPV</th>
                            <th>Waktu Snapshot</th>
                            <th>Detail</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $row)
                            @php
                                $decodeJson = function ($value) {
                                    if (is_array($value)) {
                                        return $value;
                                    }

                                    if (is_string($value)) {
                                        return json_decode($value, true) ?? [];
                                    }

                                    return [];
                                };

                                $trayChecking = $decodeJson($row->tray_checking);
                                $boxChecking = $decodeJson($row->box_checking);
                                $trayPacking = $decodeJson($row->tray_packing);
                                $boxPacking = $decodeJson($row->box_packing);

                                $detailId = 'detail-pengemasan-' . $row->uuid;
                            @endphp

                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $loop->index }}
                                </td>

                                <td class="text-center">
                                    {{ $row->date ? $row->date->format('d-m-Y') : '-' }}
                                </td>

                                <td class="text-center">{{ $row->shift ?? '-' }}</td>
                                <td class="text-center">{{ $row->pukul ?? '-' }}</td>
                                <td>{{ $row->nama_produk ?? '-' }}</td>
                                <td>{{ $row->kode_produksi ?? '-' }}</td>

                                <td class="text-center">
                                    <span class="badge bg-info">
                                        Checking
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-success">
                                        Packing
                                    </span>
                                </td>

                                <td>{{ $row->catatan ?? '-' }}</td>
                                <td>{{ $row->username ?? '-' }}</td>

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
                                    {{ $row->created_at ? $row->created_at->format('d-m-Y H:i') : '-' }}
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#{{ $detailId }}">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>

                            {{-- DETAIL SNAPSHOT --}}
                            <tr class="collapse" id="{{ $detailId }}">
                                <td colspan="13">
                                    <div class="p-3 bg-light rounded">

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-4">
                                                <strong>Tanggal Packing:</strong>
                                                {{ $row->date_packing ? $row->date_packing->format('d-m-Y') : '-' }}
                                            </div>

                                            <div class="col-md-4">
                                                <strong>Shift Packing:</strong>
                                                {{ $row->shift_packing ?? '-' }}
                                            </div>

                                            <div class="col-md-4">
                                                <strong>Pukul Packing:</strong>
                                                {{ $row->pukul_packing ?? '-' }}
                                            </div>
                                        </div>

                                        <hr>

                                        {{-- CHECKING --}}
                                        <h6 class="fw-bold text-primary">
                                            Pengemasan - Checking
                                        </h6>

                                        <div class="row g-3">

                                            {{-- TRAY CHECKING --}}
                                            <div class="col-md-6">
                                                <div class="card h-100">
                                                    <div class="card-header fw-bold">
                                                        Tray / Pack Checking
                                                    </div>
                                                    <div class="card-body">
                                                        <p>
                                                            <strong>Nama Produk:</strong>
                                                            {{ $trayChecking['nama_produk'] ?? '-' }}
                                                        </p>

                                                        <p>
                                                            <strong>QR Code:</strong>
                                                            {{ $trayChecking['qrcode'] ?? '-' }}
                                                        </p>

                                                        <p>
                                                            <strong>Kondisi:</strong>
                                                            {{ $trayChecking['kondisi'] ?? '-' }}
                                                        </p>

                                                        <p class="mb-1">
                                                            <strong>Foto Prod. Code / Best Before:</strong>
                                                        </p>

                                                        @if (!empty($trayChecking['kode_produksi']))
                                                            <a href="{{ asset('storage/' . $trayChecking['kode_produksi']) }}"
                                                               target="_blank">
                                                                <img src="{{ asset('storage/' . $trayChecking['kode_produksi']) }}"
                                                                     class="img-thumbnail"
                                                                     style="max-width:180px; max-height:180px;">
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Tidak ada foto</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- BOX CHECKING --}}
                                            <div class="col-md-6">
                                                <div class="card h-100">
                                                    <div class="card-header fw-bold">
                                                        Box Checking
                                                    </div>
                                                    <div class="card-body">
                                                        <p>
                                                            <strong>Kondisi:</strong>
                                                            {{ $boxChecking['kondisi'] ?? '-' }}
                                                        </p>

                                                        <p>
                                                            <strong>Keterangan:</strong>
                                                            {{ $row->keterangan_checking ?? '-' }}
                                                        </p>

                                                        <p class="mb-1">
                                                            <strong>Foto Prod. Code / Best Before:</strong>
                                                        </p>

                                                        @if (!empty($boxChecking['kode_produksi']))
                                                            <a href="{{ asset('storage/' . $boxChecking['kode_produksi']) }}"
                                                               target="_blank">
                                                                <img src="{{ asset('storage/' . $boxChecking['kode_produksi']) }}"
                                                                     class="img-thumbnail"
                                                                     style="max-width:180px; max-height:180px;">
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Tidak ada foto</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>

                                        {{-- PACKING --}}
                                        <h6 class="fw-bold text-success">
                                            Pengemasan - Packing
                                        </h6>

                                        <div class="row g-3">

                                            {{-- TRAY PACKING --}}
                                            <div class="col-md-6">
                                                <div class="card h-100">
                                                    <div class="card-header fw-bold">
                                                        Tray / Pack Packing
                                                    </div>
                                                    <div class="card-body">
                                                        <p>
                                                            <strong>Nama Produk:</strong>
                                                            {{ $trayPacking['nama_produk'] ?? '-' }}
                                                        </p>

                                                        <p>
                                                            <strong>QR Code:</strong>
                                                            {{ $trayPacking['qrcode'] ?? '-' }}
                                                        </p>

                                                        <p>
                                                            <strong>Kondisi:</strong>
                                                            {{ $trayPacking['kondisi'] ?? '-' }}
                                                        </p>

                                                        <p class="mb-1">
                                                            <strong>Foto Prod. Code / Best Before:</strong>
                                                        </p>

                                                        @if (!empty($trayPacking['kode_produksi']))
                                                            <a href="{{ asset('storage/' . $trayPacking['kode_produksi']) }}"
                                                               target="_blank">
                                                                <img src="{{ asset('storage/' . $trayPacking['kode_produksi']) }}"
                                                                     class="img-thumbnail"
                                                                     style="max-width:180px; max-height:180px;">
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Tidak ada foto</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- BOX PACKING --}}
                                            <div class="col-md-6">
                                                <div class="card h-100">
                                                    <div class="card-header fw-bold">
                                                        Box Packing
                                                    </div>
                                                    <div class="card-body">
                                                        <p>
                                                            <strong>Isi Box:</strong>
                                                            {{ $boxPacking['isi_box'] ?? '-' }}
                                                        </p>

                                                        <p>
                                                            <strong>Kondisi:</strong>
                                                            {{ $boxPacking['kondisi'] ?? '-' }}
                                                        </p>

                                                        <p>
                                                            <strong>Keterangan:</strong>
                                                            {{ $row->keterangan_packing ?? '-' }}
                                                        </p>

                                                        <p class="mb-1">
                                                            <strong>Foto Prod. Code / Best Before:</strong>
                                                        </p>

                                                        @if (!empty($boxPacking['kode_produksi']))
                                                            <a href="{{ asset('storage/' . $boxPacking['kode_produksi']) }}"
                                                               target="_blank">
                                                                <img src="{{ asset('storage/' . $boxPacking['kode_produksi']) }}"
                                                                     class="img-thumbnail"
                                                                     style="max-width:180px; max-height:180px;">
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Tidak ada foto</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>

                                        {{-- INFORMASI AUDIT --}}
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <strong>QC:</strong><br>
                                                {{ $row->username ?? '-' }}
                                            </div>

                                            <div class="col-md-3">
                                                <strong>Produksi:</strong><br>
                                                {{ $row->nama_produksi ?? '-' }}
                                            </div>

                                            <div class="col-md-3">
                                                <strong>Supervisor:</strong><br>
                                                {{ $row->nama_spv ?? '-' }}
                                            </div>

                                            <div class="col-md-3">
                                                <strong>Username Update:</strong><br>
                                                {{ $row->username_updated ?? '-' }}
                                            </div>

                                            <div class="col-md-6">
                                                <strong>Catatan Supervisor:</strong><br>
                                                {{ $row->catatan_spv ?? '-' }}
                                            </div>

                                            <div class="col-md-6">
                                                <strong>Catatan:</strong><br>
                                                {{ $row->catatan ?? '-' }}
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <span class="badge bg-dark">
                                                Snapshot Audit: {{ $row->created_at?->format('d-m-Y H:i:s') ?? '-' }}
                                            </span>
                                            <span class="badge bg-secondary">
                                                Source UUID: {{ $row->source_uuid ?? '-' }}
                                            </span>
                                        </div>

                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="13" class="text-center py-4">
                                    Belum ada data Audit Pengemasan.
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
