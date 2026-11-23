
@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h3 class="mb-1">
                        <i class="bi bi-clipboard-check"></i>
                        Audit Pemeriksaan Produk Retur
                    </h3>
                    <small class="text-muted">
                        Snapshot data Retur saat pertama kali disimpan.
                    </small>
                </div>

                @if ((int) auth()->user()->type_user !== 5)
                    <a href="{{ route('retur.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('retur.audit') }}"
                class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light">

                <div class="input-group" style="max-width:220px;">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-calendar-date"></i>
                    </span>
                    <input type="date" name="date" class="form-control"
                        value="{{ request('date') }}">
                </div>

                <div class="input-group" style="max-width:350px;">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control"
                        placeholder="Cari produk, kode produksi, mobil..."
                        value="{{ request('search') }}">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>

                <a href="{{ route('retur.audit') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th rowspan="2">No.</th>
                            <th rowspan="2">Tanggal</th>
                            <th rowspan="2">Shift</th>
                            <th rowspan="2">No. Mobil</th>
                            <th rowspan="2">Nama Supir</th>
                            <th rowspan="2">Nama Produk</th>
                            <th rowspan="2">Kode Produksi</th>
                            <th rowspan="2">Expired Date</th>
                            <th rowspan="2">Jumlah (Box/Pack)</th>
                            <th colspan="3">Kondisi Retur</th>
                            <th rowspan="2">Keterangan</th>
                            <th rowspan="2">Catatan</th>
                            <th rowspan="2">QC</th>
                            <th rowspan="2">Warehouse</th>
                            <th rowspan="2">Status Warehouse</th>
                            <th rowspan="2">SPV</th>
                            <th rowspan="2">Status SPV</th>
                            <th rowspan="2">Catatan SPV</th>
                            <th rowspan="2">Diubah Oleh</th>
                            <th rowspan="2">Waktu Snapshot</th>
                        </tr>
                        <tr>
                            <th>Bocor</th>
                            <th>Isi Kurang</th>
                            <th>Lain-lain</th>
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

                                <td class="text-center">{{ $item->shift ?? '-' }}</td>
                                <td>{{ $item->no_mobil ?? '-' }}</td>
                                <td>{{ $item->nama_supir ?? '-' }}</td>
                                <td>{{ $item->nama_produk ?? '-' }}</td>
                                <td>{{ $item->kode_produksi ?? '-' }}</td>

                                <td class="text-nowrap">
                                    {{ $item->expired_date ? \Carbon\Carbon::parse($item->expired_date)->format('d-m-Y') : '-' }}
                                </td>

                                <td class="text-end">{{ $item->jumlah ?? '-' }}</td>
                                <td>{{ $item->bocor ?? '-' }}</td>
                                <td>{{ $item->isi_kurang ?? '-' }}</td>
                                <td>{{ $item->lainnya ?? '-' }}</td>
                                <td>{{ $item->keterangan ?? '-' }}</td>
                                <td>{{ $item->catatan ?? '-' }}</td>
                                <td>{{ $item->username ?? '-' }}</td>
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
                                <td>{{ $item->username_updated ?? '-' }}</td>

                                <td class="text-nowrap">
                                    {{ $item->created_at ? $item->created_at->format('d-m-Y H:i:s') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="22" class="text-center py-4">
                                    Belum ada data Audit Retur.
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
