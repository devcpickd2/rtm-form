@extends('layouts.app')

@section('content')
    <div class="container-fluid py-2">

        <div class="card shadow-sm">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                    <h3 class="mb-0">
                        <i class="bi bi-shield-check"></i>
                        Audit Verifikasi Produk Institusi
                    </h3>

                    <a href="{{ route('institusi.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                <form method="GET" action="{{ route('institusi.audit') }}"
                    class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 border rounded bg-light">

                    <input type="date" name="date" class="form-control" style="max-width:220px"
                        value="{{ request('date') }}">

                    <input type="text" name="search" class="form-control" style="max-width:350px"
                        placeholder="Cari produk, kode produksi, lokasi..." value="{{ request('search') }}">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Cari
                    </button>

                    <a href="{{ route('institusi.audit') }}" class="btn btn-secondary">
                        Reset
                    </a>
                </form>

                <div class="alert alert-info">
                    Data Audit merupakan salinan kondisi data Institusi saat pertama kali disimpan.
                    Data ini hanya dapat dilihat dan tidak dapat diubah.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Tanggal</th>
                                <th rowspan="2">Shift</th>
                                <th rowspan="2">Jenis Produk</th>
                                <th rowspan="2">Kode Produksi</th>
                                <th colspan="2">Waktu Proses</th>
                                <th rowspan="2">Lokasi</th>
                                <th colspan="2">Suhu Produk (°C)</th>
                                <th rowspan="2">Sensori</th>
                                <th rowspan="2">Keterangan</th>
                                <th rowspan="2">Catatan</th>
                                <th rowspan="2">QC</th>
                                <th rowspan="2">Produksi</th>
                                <th rowspan="2">Status Produksi</th>
                                <th rowspan="2">SPV</th>
                                <th rowspan="2">Status SPV</th>
                                <th rowspan="2">Catatan SPV</th>
                                <th rowspan="2">Waktu Snapshot</th>
                            </tr>
                            <tr>
                                <th>Mulai</th>
                                <th>Selesai</th>
                                <th>Sebelum</th>
                                <th>Sesudah</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($data as $item)
                                <tr>
                                    <td class="text-center">
                                        {{ $data->firstItem() + $loop->index }}
                                    </td>

                                    <td class="text-center">
                                        {{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d-m-Y') : '-' }}
                                    </td>

                                    <td class="text-center">{{ $item->shift ?? '-' }}</td>
                                    <td>{{ $item->jenis_produk ?? '-' }}</td>
                                    <td>{{ $item->kode_produksi ?? '-' }}</td>

                                    <td class="text-center">
                                        {{ $item->waktu_proses_mulai ? \Carbon\Carbon::parse($item->waktu_proses_mulai)->format('H:i') : '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $item->waktu_proses_selesai ? \Carbon\Carbon::parse($item->waktu_proses_selesai)->format('H:i') : '-' }}
                                    </td>

                                    <td>{{ $item->lokasi ?? '-' }}</td>
                                    <td class="text-center">{{ $item->suhu_sebelum ?? '-' }}</td>
                                    <td class="text-center">{{ $item->suhu_sesudah ?? '-' }}</td>
                                    <td>{{ $item->sensori ?? '-' }}</td>
                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                    <td>{{ $item->catatan ?? '-' }}</td>
                                    <td>{{ $item->username ?? '-' }}</td>
                                    <td>{{ $item->nama_produksi ?? '-' }}</td>

                                    <td class="text-center">
                                        {{ $item->status_produksi ?? '-' }}
                                    </td>

                                    <td>{{ $item->nama_spv ?? '-' }}</td>

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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="20" class="text-center py-4">
                                        Belum ada data Audit Institusi.
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
        .table th,
        .table td {
            white-space: nowrap;
            font-size: 0.85rem;
            vertical-align: middle;
        }
    </style>
@endsection
