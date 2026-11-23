
@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1">Audit Monitoring Proses Repack</h4>
            <small class="text-muted">Data snapshot Repack - akses hanya lihat</small>
        </div>

        <a href="{{ route('repack.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('repack.audit') }}"
                class="d-flex flex-wrap align-items-end gap-2">

                <div style="min-width:250px;">
                    <label class="form-label">Pencarian</label>
                    <input type="text"
                        name="search"
                        class="form-control"
                        value="{{ $search }}"
                        placeholder="Produk, kode produksi, karton...">
                </div>

                <div>
                    <label class="form-label">Tanggal</label>
                    <input type="date"
                        name="date"
                        class="form-control"
                        value="{{ $date }}">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="{{ route('repack.audit') }}" class="btn btn-secondary">
                    Reset
                </a>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Riwayat Data Audit Repack</strong>
            <span class="badge bg-info">
                Total: {{ $data->total() }} data
            </span>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Nama Produk</th>
                            <th>Kode Produksi</th>
                            <th>Karton</th>
                            <th>Jumlah</th>
                            <th>Expired Date</th>
                            <th>Kodefikasi</th>
                            <th>Content</th>
                            <th>Kerapihan</th>
                            <th>Lainnya</th>
                            <th>Keterangan</th>
                            <th>Catatan</th>
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
                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $index }}
                                </td>

                                <td>{{ $row->date?->format('d-m-Y') ?? '-' }}</td>
                                <td class="text-center">{{ $row->shift ?? '-' }}</td>
                                <td>{{ $row->nama_produk ?? '-' }}</td>
                                <td>{{ $row->kode_produksi ?? '-' }}</td>
                                <td>{{ $row->karton ?? '-' }}</td>
                                <td class="text-end">{{ $row->jumlah ?? '-' }}</td>
                                <td>{{ $row->expired_date?->format('d-m-Y') ?? '-' }}</td>

                                <td class="text-center">
                                    <span class="badge {{ $row->kodefikasi === 'sesuai' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $row->kodefikasi ?? '-' }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge {{ $row->content === 'sesuai' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $row->content ?? '-' }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge {{ $row->kerapihan === 'sesuai' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $row->kerapihan ?? '-' }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge {{ $row->lainnya === 'sesuai' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $row->lainnya ?? '-' }}
                                    </span>
                                </td>

                                <td>{{ $row->keterangan ?? '-' }}</td>
                                <td>{{ $row->catatan ?? '-' }}</td>
                                <td>{{ $row->nama_produksi ?? '-' }}</td>

                                <td class="text-center">
                                    @if($row->status_produksi == 1)
                                        <span class="badge bg-success">Tersimpan</span>
                                    @elseif($row->status_produksi == 2)
                                        <span class="badge bg-warning text-dark">Revisi</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $row->status_produksi ?? '-' }}</span>
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
                                    <button class="btn btn-sm btn-info"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#detailRepack{{ $row->uuid }}">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>

                            <tr class="collapse" id="detailRepack{{ $row->uuid }}">
                                <td colspan="21">
                                    <div class="card card-body bg-light">
                                        <h6 class="fw-bold">Detail Snapshot Audit Repack</h6>

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <strong>UUID Data Asli:</strong><br>
                                                {{ $row->source_uuid ?? '-' }}
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <strong>Username Input:</strong><br>
                                                {{ $row->username ?? '-' }}
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <strong>Username Update:</strong><br>
                                                {{ $row->username_updated ?? '-' }}
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <strong>Waktu Update Produksi:</strong><br>
                                                {{ $row->tgl_update_produksi?->format('d-m-Y H:i:s') ?? '-' }}
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <strong>Waktu Verifikasi SPV:</strong><br>
                                                {{ $row->tgl_update_spv?->format('d-m-Y H:i:s') ?? '-' }}
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <strong>Waktu Snapshot:</strong><br>
                                                {{ $row->created_at?->format('d-m-Y H:i:s') ?? '-' }}
                                            </div>
                                        </div>

                                        <div class="alert alert-info mb-0 mt-2">
                                            Data ini merupakan salinan Audit dan tidak dapat diubah melalui halaman ini.
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="21" class="text-center">
                                    Belum ada data Audit Repack.
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
