
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h4 class="mb-1">
                        <i class="bi bi-clipboard-check"></i>
                        Audit Sample Bulanan RND
                    </h4>
                    <small class="text-muted">
                        Riwayat snapshot data Sample Bulanan.
                    </small>
                </div>

                @if ((int) auth()->user()->type_user !== 5)
                    <a href="{{ route('sample_bulanan.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('sample_bulanan.audit') }}"
                class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light">

                <div class="input-group" style="max-width:220px;">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-calendar"></i>
                    </span>
                    <input type="date" name="date" class="form-control"
                        value="{{ request('date') }}">
                </div>

                <div class="input-group" style="max-width:350px;">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control"
                        placeholder="Cari username, plant, sample bulan..."
                        value="{{ request('search') }}">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="{{ route('sample_bulanan.audit') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Plant</th>
                            <th>Sample Bulan</th>
                            <th>Sample Storage</th>
                            <th>Detail Sample</th>
                            <th>Catatan</th>
                            <th>Nama Warehouse</th>
                            <th>Status Warehouse</th>
                            <th>Nama SPV</th>
                            <th>Status SPV</th>
                            <th>Catatan SPV</th>
                            <th>Diinput Oleh</th>
                            <th>Terakhir Diubah Oleh</th>
                            <th>Waktu Snapshot</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data as $item)
                            <tr>
                                <td class="text-center">
                                    {{ $data->firstItem() + $loop->index }}
                                </td>

                                <td class="text-nowrap">
                                    {{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d-m-Y') : '-' }}
                                </td>

                                <td>{{ $item->plant ?? '-' }}</td>

                                <td class="text-nowrap">
                                    {{ $item->sample_bulan ?? '-' }}
                                </td>

                                <td>
                                    @forelse ($item->sample_storage_audit as $storage)
                                        <span class="badge bg-info text-dark mb-1">
                                            {{ $storage }}
                                        </span>
                                    @empty
                                        -
                                    @endforelse
                                </td>

                                <td style="min-width:350px;">
                                    @if (count($item->sample_audit))
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Nama Produk</th>
                                                        <th>Kode Produksi</th>
                                                        <th>Best Before</th>
                                                        <th>Quantity (gr)</th>
                                                        <th>Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($item->sample_audit as $sample)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $sample['nama_produk'] ?? '-' }}</td>
                                                            <td>{{ $sample['kode_produksi'] ?? '-' }}</td>
                                                            <td>{{ $sample['best_before'] ?? '-' }}</td>
                                                            <td>{{ $sample['quantity'] ?? '-' }}</td>
                                                            <td>{{ $sample['keterangan'] ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>{{ $item->catatan ?? '-' }}</td>
                                <td>{{ $item->nama_warehouse ?? '-' }}</td>

                                <td class="text-center">
                                    @if ((string) $item->status_warehouse === '1')
                                        <span class="badge bg-success">Checked</span>
                                    @elseif ((string) $item->status_warehouse === '2')
                                        <span class="badge bg-danger">Recheck</span>
                                    @else
                                        <span class="badge bg-secondary">
                                            {{ $item->status_warehouse ?? '-' }}
                                        </span>
                                    @endif
                                </td>

                                <td>{{ $item->nama_spv ?? '-' }}</td>

                                <td class="text-center">
                                    @if ((string) $item->status_spv === '1')
                                        <span class="badge bg-success">Verified</span>
                                    @elseif ((string) $item->status_spv === '2')
                                        <span class="badge bg-danger">Revision</span>
                                    @elseif ((string) $item->status_spv === '0')
                                        <span class="badge bg-warning text-dark">Created</span>
                                    @else
                                        <span class="badge bg-secondary">
                                            {{ $item->status_spv ?? '-' }}
                                        </span>
                                    @endif
                                </td>

                                <td>{{ $item->catatan_spv ?? '-' }}</td>
                                <td>{{ $item->username ?? '-' }}</td>
                                <td>{{ $item->username_updated ?? '-' }}</td>

                                <td class="text-nowrap">
                                    {{ $item->created_at ? $item->created_at->format('d-m-Y H:i:s') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="text-center py-4">
                                    Belum ada data Audit Sample Bulanan.
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
